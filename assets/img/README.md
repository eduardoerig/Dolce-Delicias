# Imagens

Nada aqui é definitivo, exceto a logo (`logo.webp` e `logo-icone.png`).

O site **não quebra sem foto**: produto ou unidade com `imagem` vazia (`null`) em
`dados/*.json` ganha um desenho no lugar (`assets/js/desenhos.js`) — um
ícone pela categoria do produto ou a fachada da loja.

## Onde cada imagem entra

| Pasta | O que vai aqui | Quem aponta para ela |
|---|---|---|
| `produtos/` | foto de cada item do catálogo | campo `imagem` em `dados/produtos.json` |
| `unidades/` | fachada de cada loja | campo `imagem` em `dados/unidades.json` |
| `heroi/`, `recortes/`, `sobre/` | fotos fixas das páginas | os `.html` da raiz |
| (raiz) | `logo.webp` — logo oficial; `logo-icone.png` — a mesma, quadrada | topo e rodapé dos `.html`, README; favicon e avatar do WhatsApp |

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

`logo.webp` (640 px de largura, fundo transparente) e `logo-icone.png` (192 × 192) saem da
logo oficial em PNG. Para trocar a logo, substitua os dois arquivos mantendo os nomes;
se a proporção mudar, ajuste `width`/`height` das `<img>` no topo e no rodapé de cada `.html`.
