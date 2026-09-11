# Dolce Delícias — front-end

Vitrine e catálogo de encomendas da Dolce Delícias. **Só front-end**: não há
back-end, login nem pagamento. O pedido é montado no navegador e fechado no
WhatsApp da matriz.

- **PHP puro** (sem framework), templates `.php` com includes reutilizáveis
- **Tailwind CSS v4** com config CSS-first (`@theme` em `assets/css/input.css`)
- **daisyUI 5** para os componentes (card, drawer, badge, dropdown, botões)
- **JavaScript vanilla** em ES modules; carrinho em `localStorage`

Sem React, sem Alpine, sem Stimulus. Sem build de JS — os módulos vão direto
para o navegador.

---

## Como rodar

Precisa de **PHP 8.1+** e **Node 18+**.

### 1. Instalar as dependências (só as do Tailwind)

```bash
npm install
```

### 2. Compilar o CSS (deixe rodando num terminal)

```bash
npx @tailwindcss/cli -i assets/css/input.css -o assets/css/app.css --watch
```

### 3. Subir o servidor (em outro terminal)

```bash
php -S localhost:8000
```

Abra **http://localhost:8000**.

Os mesmos comandos estão no `package.json`: `npm run dev` (CSS em watch),
`npm run build` (CSS minificado para produção) e `npm run serve`.

> `assets/css/app.css` é **gerado**. Não edite esse arquivo — mexa em
> `assets/css/input.css` e deixe o watch recompilar.

---

## Estrutura

```
dolce-delicias/
├── index.php               home: herói, promoções, catálogo e como encomendar
├── unidades.php            uma seção por loja, com âncora #<slug>
├── sobre.php               a empresa: quem somos, história, portfólio, empresas
├── produto.php             página de produto (lê ?slug=), com 404 amigável
├── carrinho.php            o pedido em página inteira
├── partials/
│   ├── bootstrap.php       carrega os dados e define os helpers
│   ├── header.php          abre o documento + topo fixo + menu de catálogos
│   ├── hero.php            o herói de cartaz (home e unidades)
│   ├── catalog-menu.php    itens do menu Catálogo (desktop e celular)
│   ├── filtro-linha.php    segmentado encomendas x balcão (2 lugares)
│   ├── footer.php          rodapé + fecha o drawer e o documento
│   ├── product-card.php    um card do catálogo
│   ├── unit-section.php    uma loja inteira em unidades.php
│   ├── promocoes.php       faixa de ofertas da home (RF-14 e RF-20)
│   ├── pedido-validacao.php  confirmação: horário, prazo, entrega, pagamento
│   └── cart-drawer.php     carrinho lateral
├── data/
│   ├── products.php        catálogo (return array)
│   ├── units.php           matriz + 5 unidades (return array)
│   ├── promocoes.php       ofertas com agenda (semanal, mensal, período)
│   └── empresa.php         textos institucionais que alimentam sobre.php
├── assets/
│   ├── css/input.css       fonte do Tailwind: tokens, temas daisyUI, componentes
│   ├── css/app.css         GERADO pelo Tailwind CLI
│   ├── js/cart.js          estado do carrinho + mensagem do WhatsApp
│   ├── js/ui.js            unidade, drawer, busca, filtros, reveal, avisos, confirmação
│   └── img/                placeholders — veja assets/img/README.md
├── catalogos/              um PDF por unidade + completo.pdf — veja catalogos/README.md
└── package.json
```

`partials/bootstrap.php` não estava no desenho original da pasta. Ele existe
porque `produto.php` precisa consultar o catálogo **antes** de imprimir o
cabeçalho, para decidir entre a página do produto e o 404.

---

## O catálogo é 100% orientado a dados

Todo o site sai de `data/products.php` e `data/units.php`.

### Adicionar um produto

Copie um bloco em `data/products.php`, troque `id` e `slug` e salve. O card, a
página `produto.php?slug=…`, a busca e os relacionados aparecem sozinhos.

### Adicionar uma categoria

**Não existe lista fixa de categorias.** Escreva o nome novo no campo
`categoria` de qualquer produto e o botão de filtro aparece. A ordem dos botões
segue a ordem de aparição dos produtos no array.

### Preços

Cada produto tem uma lista `precos`. Cada faixa é o preço **da embalagem**
inteira, mais o rótulo dela:

```php
['valor' => 120.00, 'por' => '100 unidades', 'minPedido' => 50]
```

O site deriva o resto sozinho, em `dd_faixa()` (`partials/bootstrap.php`):

| Campo derivado | Como sai | No exemplo |
|---|---|---|
| `base` | número dentro de `por` | 100 |
| `unitario` | `valor / base` | R$ 1,20 |
| `min` | `minPedido`, ou `base` | 50 |
| `passo` | `passo` do cadastro; senão `min`, quando ele é menor que `base`; senão `base` | 50 |

**A quantidade é sempre contada em peças**, não em centos: o cliente pede
"150 pão de queijo" e o subtotal é `unitario × 150`. É por isso que
`minPedido: 50` funciona num produto vendido por cento.

Se o passo automático não servir para algum item, declare `'passo' => 10` na
faixa e ele manda.

**O preço é único para toda a rede** (RF-06 revisado). A relação produto↔unidade
existe só como recorte de catálogo, no campo `unidades` — nunca como preço
diferente por loja.

### Restrições alimentares (RF-24)

Escreva `'vegano'` ou `'sem lactose'` nas `tags` de um produto e o chip de filtro
aparece sozinho no catálogo. A lista de tags reconhecidas está em
`dd_restricoes()` (`partials/bootstrap.php`) — hoje `vegano`, `sem lactose`,
`sem glúten` e `sem carne`. O chip só é desenhado quando **algum** produto
carrega a tag, então o filtro nunca promete um recorte vazio.

Diferente da categoria, as restrições **acumulam**: ligar "vegano" e
"sem lactose" pede as duas coisas ao mesmo tempo.

### Promoções (RF-14 e RF-20)

`data/promocoes.php` guarda as duas coisas, porque as duas são "uma oferta com
uma data": a promoção de quarta-feira e as campanhas de baixa temporada. O que
muda entre elas é só o campo `quando`:

```php
['tipo' => 'semanal', 'dias'  => [3]]           // toda quarta (0=domingo)
['tipo' => 'mensal',  'meses' => [7, 12, 1]]    // julho, dezembro e janeiro
['tipo' => 'periodo', 'de' => '2026-12-01', 'ate' => '2026-12-24']
['tipo' => 'sempre']
```

**Vigente e visível não são a mesma coisa**, e é isso que faz a divulgação
funcionar:

| Agenda | Quando aparece na home |
|---|---|
| `semanal`, `sempre` | todo dia, com selo **"é hoje"** no dia certo |
| `mensal`, `periodo` | só dentro da janela |

Uma promoção de quarta anunciada só na quarta não divulga nada — quem monta a
encomenda na segunda precisa saber. Já a campanha de julho anunciada em março é
ruído. Quem decide isso é `dd_promocoes_visiveis()`, em `partials/bootstrap.php`;
`partials/promocoes.php` não tem nenhuma regra de calendário.

Para conferir julho e dezembro sem esperar julho e dezembro, o partial aceita uma
data:

```php
$promocoesEm = new DateTimeImmutable('2026-12-16');
include __DIR__ . '/partials/promocoes.php';
```

Sem nada cadastrado (ou tudo com `'ativo' => false`), a seção inteira some — a
home não fica com um título "Promoções" e vazio embaixo.

O desconto **não** é calculado pelo site: o total continua sendo o preço cheio, e
quem aplica a oferta é a matriz no fechamento.

### A empresa (RF-19, RF-21, RF-22, RF-31)

`sobre.php` responde quatro perguntas da mesma conversa, na ordem em que alguém
faria: quem somos (RF-31), de onde viemos (RF-21), o que fazemos (RF-22) e o que
fazemos pela sua empresa (RF-19). Quatro páginas separadas dariam quatro páginas
curtas e um menu inchado.

Todo o texto vem de `data/empresa.php` — a página só tem estrutura. Cada seção
some sozinha quando a chave dela está vazia, então dá para publicar com metade
preenchida sem ficar buraco na tela. O total de unidades é **contado** de
`data/units.php`, nunca cadastrado, para os dois não divergirem quando abrir a
próxima loja.

### Produtos por unidade (RF-25)

Cada produto pode declarar `'unidades' => ['matriz', 'unidade-2']` com os slugs
das lojas que o vendem. **Isso não muda nada na interface**: o catálogo do site
é único. O campo existe para a geração futura do PDF por unidade. Ausente ou
vazio quer dizer "todas as lojas".

---

## Carrinho e WhatsApp

- Estado em `localStorage['dolce_cart']`. Não há escolha de unidade: pedido
  pelo site vai sempre para a matriz (`matriz()` em `assets/js/cart.js`).
- `assets/js/cart.js` expõe `addToCart`, `updateQty`, `removeItem`,
  `renderDrawer`, `updateBadge` e `checkout`.
- Os botões usam **delegação de eventos** num único listener no `document`, então
  qualquer card impresso depois já funciona.
- `checkout()` lê a matriz no `<script type="application/json" id="units-data">`
  publicado pelo PHP, monta a mensagem e abre `https://wa.me/<numero>?text=…`.

### O caminho do pedido

```
catálogo → drawer (resumo) → carrinho.php (confirmação) → WhatsApp
```

O drawer **não fecha o pedido**: o botão dele leva para `carrinho.php`, onde
fica a confirmação (`partials/pedido-validacao.php`) com as quatro perguntas que
a padaria precisa responder antes da conversa:

| Pergunta | RF | De onde vem |
|---|---|---|
| Horário de funcionamento | RF-29 | `horario` da matriz |
| Tempo mínimo de preparo | RF-30 | `preparo` da matriz |
| Retirar na matriz ou entrega | RF-17 | escolha da pessoa (padrão: retirar) |
| Forma de pagamento | — | escolha da pessoa (padrão: Pix) |

Os campos vivem **só** em `carrinho.php` — no painel de 24rem do drawer viraria
formulário rolando dentro de formulário, e duplicá-los seria manter duas cópias
em sincronia. `checkout()` lê os quatro por `[data-pedido-*]`; chamado de
qualquer outra página, cai nos padrões conservadores (retirar na matriz,
pagamento a combinar).

**Nada é pago pelo site.** A forma de pagamento é só declarada. E **RF-18 está
fora do escopo**: não há valor, faixa nem condição de frete em lugar nenhum —
perguntar "entrega?" é RF-17, cobrar por ela não é deste site.

Mensagem gerada:

```
Olá! Quero fazer uma encomenda pelo site da Dolce Delícias.

Unidade: Matriz — PREENCHER bairro
Como receber: Entrega
Endereço: Rua das Flores, 42 — perto da escola

• 150x Pão de Queijo (R$ 120,00 / 100 un) — R$ 180,00

Total estimado: R$ 180,00
Forma de pagamento: Pix
Preparo mínimo: 48 horas para encomendas

Observação: entrega dia 12
```

A linha `Endereço:` só aparece quando a escolha é entrega — voltar para
"retirar" descarta o que foi digitado, em vez de mandar um endereço órfão.

O drawer lateral e a página `carrinho.php` usam os **mesmos** `data-*`, então
`cart.js` desenha os dois com o mesmo código.

### Miniatura de cada item

Cada item guarda `imagem` e `categoria` (vindos dos `data-imagem` / `data-categoria`
do botão "Adicionar"). Se houver foto, o carrinho mostra a foto; se não, desenha o
mesmo placeholder texturizado do card, com o ícone da categoria.

Os ícones **não** estão duplicados em JavaScript: `partials/footer.php` imprime um
`<template id="icones-produto">` com um SVG por categoria — os mesmos que
`dd_icone_categoria()` gera para o card — e `cart.js` lê de lá.

---

## Marca e tema

Tokens em `assets/css/input.css`:

| Token | Claro | Para que serve |
|---|---|---|
| `--color-brand` | `#E1051E` | vermelho da placa |
| `--color-accent` | `#FCE24C` | amarelo de carimbo |
| `--color-cream` | `#F8E3CD` | creme da marca |
| `--color-ink` | `#2A1710` | marrom torrado (texto) |
| `--color-crust` | `#7A2E12` | marrom assado (texto secundário) |

### Convenção de superfícies

O fundo padrão do site é **branco**. São quatro camadas:

| Token | Valor | Onde entra |
|---|---|---|
| `base-100` | `#FFFFFF` | chão da página (`body`) e seções que casam com ele |
| `papel` | `#FFFFFF` | superfície elevada: cards, painéis, dropdown, drawer |
| `base-200` | `#F9EFE1` | superfície recuada: faixa creme, badges, campos |
| `base-300` | `#E8D6BD` | bordas e divisores decorativos |
| `campo` | `#9C8069` | borda de `input`/`textarea` — precisa de 3:1 (WCAG 1.4.11) |

O card é branco sobre branco e se destaca por borda e sombra. Ele usa
`bg-papel` (e não `bg-base-100`) para manter separada a ideia de *superfície
elevada* da de *chão da página*.

O creme não é mais o chão: virou faixa de acento (a seção "Como encomendar", a
barra de filtros, os placeholders de foto).

Tipografia: **Baloo 2** nos títulos (redonda, cara de placa de padaria) e
**Figtree** no corpo, via Google Fonts.

O motivo dos **raios** da logo vira dispositivo estrutural: `.raios` atrás do
herói e `.faixa-raios` como divisor de seção.

### Um tema só

O site é **somente claro**: um tema daisyUI, `dolce`, fixo em
`<html data-theme="dolce">`. Não há modo escuro, botão de alternar nem
`prefers-color-scheme` — os tokens têm um valor único e o `color-scheme: light`
do tema impede o navegador de escurecer controles nativos.

---

## Pontos de integração futura

Todos estão comentados no código com `>>> INTEGRAÇÃO FUTURA <<<`:

| O quê | Onde |
|---|---|
| Imagem do herói | `index.php`, bloco "ESPAÇO RESERVADO" |
| Fonte dos produtos | `dd_produtos()` em `partials/bootstrap.php` |
| Fonte das unidades | `dd_unidades()` em `partials/bootstrap.php` |
| Fonte das promoções | `dd_promocoes()` em `partials/bootstrap.php` |
| Textos institucionais | `dd_empresa()` em `partials/bootstrap.php` |
| Fotos de produto | campo `imagem` em `data/products.php` + `dd_imagem()` |
| Fotos de fachada | campo `imagem` em `data/units.php` |
| Números de WhatsApp | campo `whatsapp` em `data/units.php` (hoje `55000000000`) |
| Tempo de preparo / avaliação / canais | campos `preparo`, `avaliacao` e `canais` em `data/units.php` |
| PDFs por unidade | `catalogos/` + campo `catalogoPdf` (recorte pelo campo `unidades` do produto) |
| Galeria do produto | `produto.php`, bloco "GALERIA" (hoje uma imagem só) |
| Logo oficial | `assets/img/logo.svg` + `dd_logo()` em `partials/bootstrap.php` |

### O que falta preencher antes de publicar

1. **Números de WhatsApp** das 6 unidades. Enquanto forem `55000000000`, o card
   avisa na tela que o número não está cadastrado.
2. **Endereços, horários e links de mapa** — hoje marcados como `PREENCHER`.
3. **Tempo de preparo** (`preparo`) de cada unidade — hoje um exemplo. É o que
   aparece na confirmação do pedido e no rodapé.
4. **Link de avaliação** (`avaliacao`) de cada loja. Vazio, o rodapé cai para o
   WhatsApp da matriz.
5. **Canais de venda** (`canais`) — iFood e afins. Lista vazia não desenha nada.
6. **Fotos** de produtos e fachadas (veja `assets/img/README.md`).
7. **PDFs reais** dos catálogos (veja `catalogos/README.md`).
8. **Resto do cardápio.** O catálogo atual é uma amostra; os sabores e as tags de
   restrição marcados com `// conferir` em `data/products.php` são exemplo e
   precisam de confirmação da padaria.
9. **Relação produto↔unidade** (`unidades`) — hoje todos os itens apontam para
   todas as lojas.
10. **As promoções** em `data/promocoes.php` — nome, selo e regra de cada oferta.
    As três cadastradas são exemplo; a agenda (quarta-feira, julho/janeiro e
    dezembro) veio do requisito, o resto não.
11. **Os textos da empresa** em `data/empresa.php` — história, números, portfólio
    e o atendimento a empresas. **Nada ali é informação real**: datas e trajetória
    precisam vir do cliente, porque inventar história de empresa é pior do que
    deixar o espaço em branco.

---

## Requisitos (ER v01.00)

Só a **área pública** — os RF de administração (RF-01, 07, 08, 12, 13 e RNF-01,
04) estão fora do escopo deste front-end.

Revisões acordadas com o cliente: **RF-06** preço único para a rede; **RF-18**
entrega/frete cortados; **RF-27** generalizado para feedback de serviço;
**RF-32** e **RF-33** fora de escopo.

| RF | Requisito | Onde está |
|---|---|---|
| 02, 28 | Unidades e locais de atendimento | `unidades.php` |
| 03 | Mapa | link "Ver no mapa" em `partials/unit-section.php` |
| 04, 09, 25 | Cardápio por unidade | PDF por loja em `catalogos/` + campo `unidades` |
| 05 | Cardápio por tipo | chips de categoria, derivados de `categoria` |
| 06 | Preço único | `data/products.php` |
| 10, 23 | Vitrine e apresentação | `partials/product-card.php`, `produto.php` |
| 11, 15, 16 | Carrinho, WhatsApp e comanda | `assets/js/cart.js` |
| 14, 20 | Promoção de quarta e sazonalidade | `data/promocoes.php` + `partials/promocoes.php` |
| 17 | Entrega e retirada | `partials/pedido-validacao.php` |
| 19 | Divulgação para empresas | `sobre.php#empresas` |
| 21 | História da empresa | `sobre.php#historia` |
| 22 | Portfólio | `sobre.php#portfolio` |
| 24 | Filtro vegano / sem lactose | `dd_restricoes()` + chips no catálogo |
| 26 | Canais de venda | campo `canais` em `data/units.php` |
| 27 | Feedback de serviço | bloco no rodapé (`partials/footer.php`) |
| 29, 30 | Horário e tempo mínimo | `horario` e `preparo`, na confirmação do pedido |
| 31 | Institucional | `sobre.php#institucional` |

Toda a área pública está coberta. O que falta agora é **conteúdo real**, não
código — veja "O que falta preencher" acima.

**Fora de escopo, por decisão do cliente:** RF-01, 07, 08, 12 e 13 (área
administrativa, junto de RNF-01 e 04), RF-18 (entrega e frete), RF-32 (divulgação
da expansão) e RF-33 (fábrica de congelados). Se RF-32 ou RF-33 voltarem, viram
duas chaves novas em `data/empresa.php` e uma seção em `sobre.php`.

---

## Acessibilidade

- Contraste AA em texto e componentes.
- Foco visível em tudo (anel de 3px), nunca removido — só substituído.
- Drawer com `role="dialog"` e `aria-modal`, fecha no `Esc`, devolve o foco a
  quem abriu e usa `inert` enquanto está fechado.
- Seletor de unidade e menu mobile em `<details>` nativo: teclado funciona
  mesmo sem JS.
- Chips de filtro com `aria-pressed`; contagem de resultados em `aria-live`.
- Estados vazios com texto e saída (carrinho vazio, busca sem resultado).
- `prefers-reduced-motion` desliga as animações. Sem JS, nada fica invisível.

---

## Notas de implementação

- **Filtro em duas linhas.** O briefing pedia filtro por categoria. Como o
  catálogo mistura encomenda por cento (R$ 120 / 100 un) com balcão por unidade
  (R$ 6), há uma segunda linha de chips **Encomendas / Balcão**, derivada das
  tags `atacado` / `varejo`. Sem ela os dois mundos ficam embaralhados na mesma
  grade — e o catálogo ainda vai crescer bastante.
- **Sem `mbstring`.** `dd_ascii()` e `dd_resumo()` fazem o trabalho na mão, então
  o site roda numa instalação mínima de PHP.
- **`.oculto` em vez de `hidden`.** O JS liga e desliga uma única classe com
  `display:none !important`, para não brigar com o `flex`/`grid` que o elemento
  já carrega.
- **Clamp de duas linhas num `<span>`.** Como `.card-body` é flex, o Chrome
  "blockifica" `display:-webkit-box` no filho direto e o corte deixa de valer.
