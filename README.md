# Dolce Delícias

Site estático: HTML, CSS e JavaScript puro. A única biblioteca é o GSAP
(`assets/js/vendor/`), usada nas animações da home. Sem build, sem npm, sem servidor
próprio. A versão antiga em PHP + PostgreSQL está na branch `legado/php`.

## Rodar no computador

Funciona em qualquer servidor estático, inclusive numa subpasta: no WAMP, abra
`http://localhost/Dolce-Delicias/`. Todos os caminhos são relativos — mantenha assim, sem `/` na frente, ou o site
quebra fora da raiz do servidor. Também serve a extensão **Live Server** do VS Code, ou:

```bash
python -m http.server 8000
```

e acesse `http://localhost:8000`. Abrir o `index.html` direto pelo arquivo
(`file://`) não funciona: o navegador bloqueia a leitura de `dados/*.json`.

## Pastas

```
index.html             página inicial
pages/                 as outras páginas (produto, unidades, sobre, carrinho, 404)
assets/css/app.css     todo o estilo do site
assets/js/dados.js     carrega os JSON e calcula preços e promoções
assets/js/desenhos.js  desenhos usados quando falta foto de produto
assets/js/carrinho.js  carrinho e fechamento no WhatsApp
assets/js/site.js      topo, rodapé, gaveta do carrinho, avisos e card de produto
assets/js/paginas/     o código de cada página
assets/js/heroi-cento.js, encomendas-zap.js   animações (GSAP)
assets/js/vendor/      GSAP
assets/img/            fotos
dados/*.json           produtos, unidades e promoções
catalogos/*.pdf        catálogo de cada unidade
vercel.json            redirecionamentos de endereços antigos
```

Os scripts são comuns (sem `import`): cada página carrega, na ordem,
`dados.js`, `desenhos.js`, `carrinho.js`, `site.js` e o arquivo dela em
`assets/js/paginas/`. Funções de um arquivo podem ser usadas nos seguintes.

Dentro de `pages/`, os caminhos começam com `../` (`../assets/...`). No JavaScript,
use `RAIZ` antes de caminhos de arquivo e `pagina('nome.html')` para links entre páginas.

## Onde alterar textos

Títulos, frases, dados das lojas, textos de "A empresa", rodapé e links de WhatsApp
estão escritos direto nos `.html`. Procure por `ALTERAR` nos arquivos: cada comentário
marca um trecho que precisa ser trocado quando a informação mudar.

O topo e o rodapé estão repetidos em cada `.html`: mudou um, mude nos outros.
Vêm do JSON (e depois do Sanity) só os produtos, as promoções e as unidades usadas
na página do produto e no WhatsApp do pedido.
O CSS não é mais gerado pelo Tailwind; edite `app.css` direto.

## Como uma página carrega

1. O HTML chega com layout, textos fixos e imagens fixas.
2. O arquivo da página chama `iniciarPagina()` (em `site.js`).
3. `carregarDados()` busca produtos, unidades e promoções e guarda tudo na variável `site`.
4. A página desenha o que depende desses dados (catálogo, produto, carrinho).

Produto: `pages/produto.html?slug=<slug>`.

## Ligar o Sanity

Troque o endereço usado em `buscarJson()`, em `assets/js/dados.js`, por uma consulta
GROQ à API HTTP do Sanity (exemplo no comentário do arquivo). Cada coleção precisa
voltar **no mesmo formato** dos JSON de `dados/`; nenhuma página muda.

### produtos.json (lista)

`slug`, `nome`, `descricao`, `categoria` (os filtros saem daqui), `imagem` (ou `null`:
vira desenho), `destaque`, `preco` (`{ valor, por, minPedido, passo }` — `valor` é o
preço da embalagem descrita em `por`, ex. "100 unidades"), `tags` ("vegano",
"sem lactose", "sem glúten", "sem carne" viram filtros), `sabores`, `unidades`
(slugs das lojas; vazio = todas; só tem botão de compra se a matriz vende), `disponivel`.

### unidades.json (lista)

`slug`, `nome` ("Unidade 2 — Centro" vira nome e bairro), `matriz` (recebe os pedidos
do site), `endereco`, `telefone`, `whatsapp` (só dígitos, 55 + DDD; `55000000000`
desliga o botão), `horario`, `preparo`, `sobre`, `mapaUrl`, `imagem`, `avaliacao`,
`catalogoPdf`, `canais` (`[{ nome, url }]`).

### promocoes.json (lista)

`slug`, `titulo`, `selo` ("15% OFF"), `texto`, `ativa`, `quando`
(`{ tipo: semanal|mensal|periodo|sempre, dias: [0-6], meses: [1-12], de, ate }`,
datas `AAAA-MM-DD`), `produtos` e `unidades` (slugs). Aparece se ativa, dentro da
agenda e com algum produto vendido numa das unidades marcadas. As três atuais estão
inativas e sem produtos (vieram assim da versão PHP).
