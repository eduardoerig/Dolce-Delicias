# Imagens

Nada aqui é definitivo, exceto `logo.svg` (usado como favicon).

O site **não quebra sem foto**: produto ou unidade com `imagem` vazia (`null`) em
`dados/*.json` ganha um desenho no lugar (`assets/js/desenhos.js`) — um
ícone pela categoria do produto ou a fachada da loja.

## Onde cada imagem entra

| Pasta | O que vai aqui | Quem aponta para ela |
|---|---|---|
| `produtos/` | foto de cada item do catálogo | campo `imagem` em `dados/produtos.json` |
| `unidades/` | fachada de cada loja | campo `imagem` em `dados/unidades.json` |
| `heroi/`, `recortes/`, `sobre/` | fotos fixas das páginas | os `.html` da raiz |
| (raiz) | `logo.svg` — placa oval da marca | favicon dos `.html` |

Com o Sanity, as fotos de produtos e unidades passam a ser imagens do próprio
Sanity e os campos `imagem` recebem a URL do CDN.

## Como nomear

Use o mesmo `slug` do cadastro. Exemplos:

```
assets/img/produtos/pao-de-queijo.jpg
assets/img/unidades/matriz.jpg
```

## Tamanhos sugeridos

| Uso | Proporção | Tamanho |
|---|---|---|
| Card e página de produto | 4:3 | 1200 × 900 px |
| Fachada de unidade | 16:10 | 1280 × 800 px |

JPG com qualidade ~80 dá conta. Se puder, gere também `.webp`.

## Logo

`logo.svg` é uma reconstrução da placa oval vermelha com os raios brancos. Quando
chegar o vetor oficial da marca, substitua este arquivo **e** o SVG da placa no topo e no rodapé de cada `.html`
(a versão inline existe para herdar a fonte da página).
