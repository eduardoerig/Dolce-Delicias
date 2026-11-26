# Desconto da promoção no carrinho — Plano de implementação

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Mostrar no carrinho (gaveta e `pages/carrinho.html`) e na mensagem do WhatsApp o preço original riscado e o preço com o desconto da promoção que vale hoje na matriz.

**Architecture:** Campo `desconto` (%) na promoção (Sanity e JSON local). `dados.js` escolhe a melhor promoção válida para um slug; `carrinho.js` calcula o subtotal cheio e com desconto de cada item **na hora de desenhar**, a partir de `site.promocoes`. Nada de desconto vai para o `localStorage`.

**Tech Stack:** HTML/CSS/JS puros (scripts globais com `defer`, sem módulos), Sanity Studio (`studio/schema.js`), testes com `node --test` + `node:vm` (sem dependência nova).

**Spec:** `docs/superpowers/specs/2026-11-26-desconto-promocao-design.md`

## Global Constraints

- Desconto só quando a promoção: aparece (`promocaoAparece`), `valeHoje === true`, `unidades` inclui `site.matriz.slug` e `desconto > 0`.
- Mais de uma promoção válida no mesmo produto: vale o **maior** `desconto`.
- Campo Sanity: `desconto`, número, opcional, de 1 a 90, título "Desconto (%)".
- O desconto é aplicado no **subtotal da linha** (`preco * qtd`), arredondado para centavos: `Math.round(valor * (100 - desconto)) / 100`.
- Nada de desconto é salvo no carrinho (`dolce_cart`). Os itens continuam com `preco` cheio.
- Texto da linha do item: `‹titulo› −‹desconto›% · válido na ‹site.matriz.nome›`.
- WhatsApp: `• ‹qtd›x ‹nome› (‹por›) — de R$ X por R$ Y (‹titulo› −‹desconto›%)`, e `Descontos: − R$ Z` antes de `Total estimado:`, só quando houver desconto.
- Sem promoção com desconto, tudo fica exatamente como hoje (HTML, total e mensagem).
- Loops no padrão do projeto: `for (let i = 0; i < lista.length; i++)`.

## Review Focus

- `site` ainda `null` (catálogo não carregou) → `melhorPromocao` devolve `null` sem erro. Teste na Task 1.
- Promoção semanal que não vale hoje ("Programe-se") → sem desconto. Teste na Task 1.
- Promoção válida, mas só em outra loja (sem a matriz) → sem desconto. Teste na Task 1.
- Promoção sem `desconto` ou com `0` → comporta-se como hoje, só com o selo. Teste na Task 1.
- Item no carrinho cujo produto saiu do catálogo (slug que não existe mais) → linha sem desconto, total cheio, sem erro. Teste na Task 2.

---

### Task 1: Campo de desconto e escolha da promoção

**Files:**
- Modify: `studio/schema.js` (tipo `promocao`, logo depois do campo `selo`)
- Modify: `dados/promocoes.json` (acrescentar `"desconto"` em cada promoção, com o número do selo: 15, 10, 10)
- Modify: `assets/js/dados.js` (perto de `promocoesDoProduto`)
- Create: `tests/desconto.test.mjs`
- Modify: `package.json` (script `"test": "node --test tests/"`)

**Interfaces:**
- Produces: `melhorPromocao(slug: string) -> promocao | null`, onde a promoção tem `titulo` e `desconto`.
- Produces: `valorComDesconto(valor: number, desconto: number) -> number`, com 2 casas.

- [ ] **Step 1: Write the failing test** em `tests/desconto.test.mjs`. Carrega `assets/js/dados.js` num contexto `vm` (stubs: `document.querySelector() -> null`, `location`), define `site` direto no contexto e testa:

```js
test('valorComDesconto arredonda para centavos', () => {
  assert.equal(ctx.valorComDesconto(120, 15), 102);
  assert.equal(ctx.valorComDesconto(10, 33), 6.7);
});
test('melhorPromocao: maior desconto entre as que valem hoje na matriz', () => {
  // promoções: A 10% vale hoje matriz; B 15% vale hoje matriz; C 50% não vale hoje;
  // D 40% vale hoje só em 'u1'; E sem desconto — todas com o produto 'p1'
  assert.equal(ctx.melhorPromocao('p1').titulo, 'B');
});
test('melhorPromocao: null sem promoção válida, sem desconto ou com site null', () => {
  assert.equal(ctx.melhorPromocao('p2'), null);   // p2 só está em C, D e E
  ctx.site = null; assert.equal(ctx.melhorPromocao('p1'), null);
});
```

- [ ] **Step 2: Run** `node --test tests/` → FAIL (`valorComDesconto is not a function`).
- [ ] **Step 3: Implement** `valorComDesconto` e `melhorPromocao` em `assets/js/dados.js`. `melhorPromocao` percorre `site.promocoes` (já filtradas por `promocaoAparece`) e compara os produtos pelo slug: `promocao.produtosDaPromocao[j].slug === slug`. Acrescente o campo `desconto` no schema: `field('desconto','Desconto (%)','number',{validation:R=>R.min(1).max(90)})`. Acrescente o campo no `dados/promocoes.json`.
- [ ] **Step 4: Run** `node --test tests/` → PASS.
- [ ] **Step 5: Commit** `git commit -m "Adiciona o desconto (%) da promoção e escolhe a melhor que vale na matriz"`

### Task 2: Carrinho e mensagem do WhatsApp com desconto

**Files:**
- Modify: `assets/js/carrinho.js` (`totalDoCarrinho`, `linhaDoCarrinho`, `mostrarCarrinho`, `mensagemDoPedido`)
- Modify: `tests/desconto.test.mjs`

**Interfaces:**
- Consumes: `melhorPromocao`, `valorComDesconto` (Task 1).
- Produces: `valoresDoItem(item) -> {cheio: number, final: number, promocao: object | null}`.
- Produces: `resumoDoCarrinho(itens) -> {subtotal: number, desconto: number, total: number}`. Substitui `totalDoCarrinho`, que só tem os 2 usos em `carrinho.js` (`mostrarCarrinho` e `mensagemDoPedido`). Apague `totalDoCarrinho`.

- [ ] **Step 1: Write the failing test.** Carrega `dados.js` + `carrinho.js` no mesmo contexto `vm` (stub `document.addEventListener`, `localStorage.getItem() -> null`), com `site.matriz = {slug: 'matriz', nome: 'Matriz — Centro'}` e a promoção B (15%) no produto `p1`:

```js
test('resumoDoCarrinho soma cheio, desconto e total', () => {
  const itens = [{slug: 'p1', preco: 1.2, qtd: 100}, {slug: 'p9', preco: 3, qtd: 10}];
  assert.deepEqual(ctx.resumoDoCarrinho(itens), {subtotal: 150, desconto: 18, total: 132});
});
test('item de produto que saiu do catálogo fica sem desconto', () => {
  assert.equal(ctx.valoresDoItem({slug: 'sumiu', preco: 2, qtd: 5}).promocao, null);
});
test('mensagem do WhatsApp mostra de/por e Descontos', () => {
  const msg = ctx.mensagemDoPedido([{slug: 'p1', nome: 'Pão', por: '100 un', preco: 1.2, qtd: 100}], 'Rua X', 'Pix', '');
  assert.match(msg, /• 100x Pão \(100 un\) — de R\$ 120,00 por R\$ 102,00 \(B −15%\)/);
  assert.match(msg, /Descontos: − R\$ 18,00\nTotal estimado: R\$ 102,00/);
});
test('mensagem sem desconto fica igual à de hoje', () => {
  const msg = ctx.mensagemDoPedido([{slug: 'p9', nome: 'Suco', por: 'un', preco: 3, qtd: 10}], '', 'Pix', '');
  assert.doesNotMatch(msg, /Descontos|de R\$/);
});
```

- [ ] **Step 2: Run** `node --test tests/` → FAIL (`resumoDoCarrinho is not a function`).
- [ ] **Step 3: Implement** em `assets/js/carrinho.js`:
  - `valoresDoItem` e `resumoDoCarrinho`.
  - `linhaDoCarrinho`: com promoção, `.item-pedido-subtotal` mostra `<s class="item-pedido-cheio">cheio</s> final`, e abaixo do nome entra `<p class="item-pedido-promo">‹titulo› −X% · válido na ‹matriz›</p>`. Sem promoção, o HTML fica igual ao de hoje.
  - `mostrarCarrinho`: `[data-cart-total]` recebe `total`. Preenche `[data-cart-subtotal]` e `[data-cart-desconto]` e esconde as linhas `[data-cart-linha-desconto]` quando `desconto === 0`, usando a função `esconder` que já existe.
  - `mensagemDoPedido`: aplica o formato das Global Constraints.
- [ ] **Step 4: Run** `node --test tests/` → PASS.
- [ ] **Step 5: Commit** `git commit -m "Mostra o desconto da promoção no carrinho e na mensagem do WhatsApp"`

### Task 3: Resumo do pedido, visual e textos

**Files:**
- Modify: `pages/carrinho.html` (aside "Resumo", antes do bloco "Total estimado")
- Modify: `assets/css/app.css` (ao lado de `.item-pedido-subtotal`)
- Modify: `assets/js/paginas/inicio.js:96` e `assets/js/paginas/produto.js:129` (remover o `<p>` "O site mostra o preço cheio…")

**Interfaces:**
- Consumes: os atributos `data-cart-subtotal`, `data-cart-desconto` e `data-cart-linha-desconto` (Task 2).

- [ ] **Step 1: Markup.** No resumo do `carrinho.html`, acima de "Total estimado", entram duas linhas no mesmo padrão `flex justify-between`, as duas com `data-cart-linha-desconto` e a classe `oculto` no HTML: "Subtotal" (`data-cart-subtotal`) e "Descontos" (`data-cart-desconto`, com o valor como `− R$ X`).
- [ ] **Step 2: CSS.** `.item-pedido-cheio`: `color: var(--color-crust)`, `font-weight: 400`, `margin-right: .375rem`. `.item-pedido-promo`: `font-size: .75rem`, `font-weight: 700`, `color: var(--color-brand)`.
- [ ] **Step 3: Remover** os dois avisos de "preço cheio".
- [ ] **Step 4: Verificar no navegador.** Copie a loja para `public/` (`index.html assets pages dados catalogos`) e rode `node scripts/servidor-local.mjs`. Sem promoção com desconto no Sanity, o HTML da home, do produto, do carrinho e da gaveta tem que ser idêntico ao de antes, exceto pelos avisos removidos. Para ver o desconto, defina no console uma promoção de teste em `site.promocoes` (`valeHoje: true`, `unidades: ['matriz']`, `desconto: 15`, `produtosDaPromocao: [site.produtos[0]]`) e chame `mostrarCarrinho()`. Confira o valor riscado, a linha da promoção, Subtotal/Descontos/Total no resumo e, na gaveta, só o total final.
- [ ] **Step 5: Run** `node --test tests/` → PASS.
- [ ] **Step 6: Commit** `git commit -m "Exibe subtotal e descontos no resumo do pedido e remove o aviso de preço cheio"`
