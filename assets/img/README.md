# Imagens

Nada aqui é definitivo, exceto `logo.svg` (usado como favicon).

O site **não quebra sem foto**: `dd_imagem()` em `partials/bootstrap.php` checa se o
arquivo existe de verdade. Se não existir, ele desenha um placeholder texturizado
com o ícone da categoria. Ou seja: você pode deixar o caminho da foto já cadastrado
em `data/products.php` antes de a foto chegar.

## Onde cada imagem entra

| Pasta | O que vai aqui | Quem aponta para ela |
|---|---|---|
| `produtos/` | foto de cada item do catálogo | campo `imagem` em `data/products.php` |
| `unidades/` | fachada de cada loja | campo `imagem` em `data/units.php` |
| (raiz) | `logo.svg` — placa oval da marca | favicon em `partials/header.php` |

## Como nomear

Use o mesmo `slug` do cadastro. Exemplos:

```
assets/img/produtos/pao-de-queijo.jpg
assets/img/produtos/salgados-fritos.jpg
assets/img/unidades/matriz.jpg
```

## Tamanhos sugeridos

| Uso | Proporção | Tamanho |
|---|---|---|
| Card e página de produto | 4:3 | 1200 × 900 px |
| Fachada de unidade | 16:10 | 1280 × 800 px |
| Banner do herói | 4:3 | 1600 × 1200 px |

JPG com qualidade ~80 dá conta. Se puder, gere também `.webp`.

## Banner do herói

Ainda **não** vem daqui. O herói tem um espaço reservado marcado em `index.php`
(bloco "ESPAÇO RESERVADO"), porque a imagem deve vir do back-end quando ele existir.
Para colocar uma foto fixa enquanto isso, troque o miolo daquele contêiner por uma
tag `img` com `object-cover`, mantendo a proporção 4/3.

## Logo

`logo.svg` é uma reconstrução da placa oval vermelha com os raios brancos, feita para
o site funcionar sem depender de arquivo externo. Quando chegar o vetor oficial da
marca, substitua este arquivo **e** a função `dd_logo()` em `partials/bootstrap.php`
(a versão inline existe para herdar a fonte display da página).
