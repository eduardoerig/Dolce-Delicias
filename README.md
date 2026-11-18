<div align="center">

<img src="assets/img/logo.svg" alt="Dolce Delícias" width="260">

### Salgados, doces e encomendas — loja online da Dolce Delícias

![HTML](https://img.shields.io/badge/HTML-E1051E?style=flat-square&logo=html5&logoColor=white)
![CSS](https://img.shields.io/badge/CSS-E1051E?style=flat-square&logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-FCE24C?style=flat-square&logo=javascript&logoColor=2A1710)
![GSAP](https://img.shields.io/badge/GSAP-7A2E12?style=flat-square&logo=greensock&logoColor=white)
![Sanity](https://img.shields.io/badge/Sanity-2A1710?style=flat-square&logo=sanity&logoColor=white)
![Vercel](https://img.shields.io/badge/Vercel-2A1710?style=flat-square&logo=vercel&logoColor=white)

[**Site**](https://dolcedelicias.vercel.app) · [**Painel (Studio)**](https://dolcedelicias.vercel.app/studio)

</div>

---

## Sobre

Site estático em HTML, CSS e JavaScript puros, sem framework. A única biblioteca é o
GSAP (`assets/js/vendor/`), usado nas animações da home. Produtos, lojas e promoções
vêm do **Sanity**, e o pedido é fechado pelo **WhatsApp** da matriz.

## Cores da marca

| | Nome | Hex | Uso |
| :-: | --- | --- | --- |
| ![](https://img.shields.io/badge/-%20%20%20%20-E1051E?style=flat-square) | `brand` | `#E1051E` | vermelho da logo, botões e destaques |
| ![](https://img.shields.io/badge/-%20%20%20%20-B00418?style=flat-square) | `brand-escuro` | `#B00418` | hover e estados ativos |
| ![](https://img.shields.io/badge/-%20%20%20%20-FCE24C?style=flat-square) | `accent` | `#FCE24C` | selos e promoções |
| ![](https://img.shields.io/badge/-%20%20%20%20-F8E3CD?style=flat-square) | `cream` | `#F8E3CD` | fundos de seção |
| ![](https://img.shields.io/badge/-%20%20%20%20-7A2E12?style=flat-square) | `crust` | `#7A2E12` | detalhes em marrom |
| ![](https://img.shields.io/badge/-%20%20%20%20-2A1710?style=flat-square) | `ink` | `#2A1710` | textos |

As cores ficam como variáveis no início de `assets/css/app.css` (`--color-brand`, …).
Fontes: **Alfa Slab One** (títulos), **Baloo 2** (logo) e **Figtree** (texto).

## Rodar no computador

**Só a loja** — funciona em qualquer servidor estático, inclusive numa subpasta
(no WAMP: `http://localhost/Dolce-Delicias/`), no Live Server do VS Code, ou:

```bash
python -m http.server 8000
```

Abrir o `index.html` direto pelo arquivo (`file://`) não funciona. Todos os caminhos
são relativos: mantenha assim, sem `/` na frente.

**Loja + Studio, igual à Vercel:**

```bash
npm --prefix studio ci   # só na primeira vez
npm run build
npm run dev              # http://localhost:3000  e  /studio
```

## Pastas

```
index.html             página inicial
pages/                 produto, unidades, sobre, carrinho, 404
assets/css/app.css     todo o estilo do site
assets/js/dados.js     busca o catálogo no Sanity e calcula preços e promoções
assets/js/desenhos.js  desenhos usados quando falta foto de produto
assets/js/carrinho.js  carrinho e fechamento no WhatsApp
assets/js/site.js      topo, rodapé, gaveta do carrinho, avisos e card de produto
assets/js/paginas/     o código de cada página
assets/js/vendor/      GSAP
assets/img/            fotos e logo
dados/*.json           catálogo local (usado até a primeira importação no Sanity)
catalogos/*.pdf        catálogo de cada unidade
studio/                Sanity Studio (publicado em /studio)
scripts/               build e servidor de teste
vercel.json            build, redirecionamentos e rotas
```

## Como funciona

1. O HTML chega com layout, textos e imagens fixas.
2. O arquivo da página chama `iniciarPagina()` (`site.js`).
3. `carregarDados()` busca produtos, unidades e promoções no Sanity e guarda tudo em `site`.
4. A página desenha o que depende desses dados (catálogo, produto, carrinho).

Os scripts são comuns (sem `import`) e carregam nesta ordem: `dados.js`,
`desenhos.js`, `carrinho.js`, `site.js` e o arquivo da página. Dentro de `pages/`,
os caminhos começam com `../`; no JavaScript, use `RAIZ` antes de caminhos de
arquivo e `pagina('nome.html')` para links.

A consulta ao Sanity sai pelo próprio domínio (`/sanity/...`, ver `rewrites` no
`vercel.json`); se falhar, o site tenta a API direto. Enquanto o Sanity estiver vazio
e sem importação, o site usa os JSON de `dados/`.

## Onde alterar

- **Produtos, lojas e promoções:** no [Studio](https://dolcedelicias.vercel.app/studio).
- **Textos fixos** (títulos, "A empresa", rodapé, links de WhatsApp): direto nos `.html`.
  Procure por `ALTERAR`. O topo e o rodapé se repetem em cada página — mudou um, mude nos outros.
- **Estilo:** `assets/css/app.css` (não é mais gerado pelo Tailwind).

## Publicar na Vercel

Um único projeto, com **Root Directory = raiz do repositório** (nunca `studio`).
O `vercel.json` já traz tudo:

| Ajuste         | Valor                    |
| -------------- | ------------------------ |
| Framework      | Other                    |
| Install        | `npm --prefix studio ci` |
| Build          | `node scripts/build.mjs` |
| Output         | `public`                 |

Resultado: `/` é a loja e `/studio` é o painel, no mesmo domínio.

> [!IMPORTANT]
> Para conseguir entrar no Studio, o domínio precisa estar liberado em
> [sanity.io/manage → API → CORS origins](https://www.sanity.io/manage/project/wdisx8zs/api):
> `https://dolcedelicias.vercel.app` e `http://localhost:3000`. Sem isso o painel
> fica na tela "Connect this Studio to your project".

## Pendências

Cada pendência está marcada no código como `TODO (iniciais)` — busque por `TODO`
no VS Code (Ctrl+Shift+F). Horas e status ficam na planilha "Horas - Grupo 3".

| Quem | O que falta |
| --- | --- |
| **E** | Papéis e acesso no Sanity; domínio próprio e backup do conteúdo |
| **R, G** | Dados reais e fotos das fachadas das 6 lojas (`pages/unidades.html`) |
| **R, G** | Preços, pedido mínimo, prazos e lojas de cada produto |
| **R, G** | Sessão de fotos dos produtos; catálogos em PDF de cada loja |
| **R, G** | Cadastrar fotos e PDFs no Sanity; treinar a equipe no Studio |
| **G** | Tratar e recortar as fotos (`assets/img/README.md`) |
| **LD, LH** | Texto de apresentação de cada loja |
| **LD, LH, PW** | Revisar história e institucional (`pages/sobre.html`) |
| **LD, LH, PW** | Aviso de privacidade (`pages/carrinho.html`) |
| **LD, LH, PW** | Testar o pedido em celulares reais; homologar com o cliente; manual do Studio |
| **LD, PW** | Promoções reais |
| **LH, LD, PW** | Revisão visual do site (`index.html`) |

<div align="center">
<sub>Feito com ❤️ e muito salgadinho pelo Grupo 3</sub>
</div>
