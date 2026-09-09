# Dolce Delícias — front-end

Vitrine e catálogo de encomendas da Dolce Delícias. **Só front-end**: não há
back-end, login nem pagamento. O pedido é montado no navegador e fechado no
WhatsApp da unidade escolhida.

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
├── index.php               home: herói, catálogo e como encomendar
├── unidades.php            uma seção por loja, com âncora #<slug>
├── produto.php             página de produto (lê ?slug=), com 404 amigável
├── carrinho.php            o pedido em página inteira
├── partials/
│   ├── bootstrap.php       carrega os dados e define os helpers
│   ├── header.php          abre o documento + topo fixo + menu de catálogos
│   ├── hero-cartaz.php     o herói da home, em tipo de madeira
│   ├── catalog-menu.php    itens do menu Catálogo (desktop e celular)
│   ├── filtro-linha.php    segmentado encomendas x balcão (2 lugares)
│   ├── footer.php          rodapé + fecha o drawer e o documento
│   ├── product-card.php    um card do catálogo
│   ├── unit-section.php    uma loja inteira em unidades.php
│   └── cart-drawer.php     carrinho lateral
├── data/
│   ├── products.php        catálogo (return array)
│   └── units.php           matriz + 5 unidades (return array)
├── assets/
│   ├── css/input.css       fonte do Tailwind: tokens, temas daisyUI, componentes
│   ├── css/app.css         GERADO pelo Tailwind CLI
│   ├── js/cart.js          estado do carrinho + mensagem do WhatsApp
│   ├── js/ui.js            tema, unidade, drawer, busca, filtros, reveal, avisos
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

---

## Carrinho e WhatsApp

- Estado em `localStorage['dolce_cart']`; unidade em `localStorage['dolce_unit']`.
- `assets/js/cart.js` expõe `addToCart`, `updateQty`, `removeItem`,
  `renderDrawer`, `updateBadge` e `checkout`.
- Os botões usam **delegação de eventos** num único listener no `document`, então
  qualquer card impresso depois já funciona.
- `checkout()` lê a unidade escolhida no `<script type="application/json" id="units-data">`
  publicado pelo PHP, monta a mensagem e abre `https://wa.me/<numero>?text=…`.

Mensagem gerada:

```
Olá! Quero fazer uma encomenda pelo site da Dolce Delícias.

Unidade: Matriz — PREENCHER bairro

• 150x Pão de Queijo (R$ 120,00 / 100 un) — R$ 180,00

Total estimado: R$ 180,00

Observação: entrega dia 12
```

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
| Fotos de produto | campo `imagem` em `data/products.php` + `dd_imagem()` |
| Fotos de fachada | campo `imagem` em `data/units.php` |
| Números de WhatsApp | campo `whatsapp` em `data/units.php` (hoje `55000000000`) |
| PDFs por unidade | `catalogos/` + campo `catalogoPdf` |
| Galeria do produto | `produto.php`, bloco "GALERIA" (hoje uma imagem só) |
| Logo oficial | `assets/img/logo.svg` + `dd_logo()` em `partials/bootstrap.php` |

### O que falta preencher antes de publicar

1. **Números de WhatsApp** das 6 unidades. Enquanto forem `55000000000`, o card
   avisa na tela que o número não está cadastrado.
2. **Endereços, horários e links de mapa** — hoje marcados como `PREENCHER`.
3. **Fotos** de produtos e fachadas (veja `assets/img/README.md`).
4. **PDFs reais** dos catálogos (veja `catalogos/README.md`).
5. **Resto do cardápio.** O catálogo atual é uma amostra; os sabores marcados
   com `// conferir` em `data/products.php` são exemplo e precisam de confirmação.

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
