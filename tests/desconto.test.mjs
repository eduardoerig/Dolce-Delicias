import {test} from 'node:test';
import assert from 'node:assert/strict';
import vm from 'node:vm';
import fs from 'node:fs';

const ctx = vm.createContext({
  document: {querySelector: () => null},
  location: {pathname: '/'},
});
vm.runInContext(fs.readFileSync('assets/js/dados.js', 'utf8'), ctx);

function promo(titulo, desconto, valeHoje, unidades, slugs) {
  return {titulo, desconto, valeHoje, unidades, produtosDaPromocao: slugs.map(slug => ({slug}))};
}
// 'let site' não vira propriedade do contexto, por isso a atribuição roda dentro dele.
ctx.novoSite = {
  matriz: {slug: 'm'},
  promocoes: [
    promo('A', 10, true, ['m'], ['p1']),
    promo('B', 15, true, ['m', 'u1'], ['p1']),
    promo('C', 50, false, ['m'], ['p1', 'p2']),
    promo('D', 40, true, ['u1'], ['p1', 'p2']),
    promo('E', undefined, true, ['m'], ['p1', 'p2']),
  ],
};
vm.runInContext('site = novoSite', ctx);

test('valorComDesconto arredonda para centavos', () => {
  assert.equal(ctx.valorComDesconto(120, 15), 102);
  assert.equal(ctx.valorComDesconto(10, 33), 6.7);
});
test('melhorPromocao: maior desconto entre as que valem hoje na matriz', () => {
  assert.equal(ctx.melhorPromocao('p1').titulo, 'B');
});
test('melhorPromocao: null sem promoção válida, sem desconto ou com site null', () => {
  assert.equal(ctx.melhorPromocao('p2'), null);
  vm.runInContext('site = null', ctx);
  assert.equal(ctx.melhorPromocao('p1'), null);
});

// Contexto separado com dados.js + carrinho.js (o de cima zera o site no último teste).
const cart = vm.createContext({
  document: {querySelector: () => null, querySelectorAll: () => [], addEventListener: () => {}},
  localStorage: {getItem: () => null, setItem: () => {}},
  iconeProduto: () => '', // vem de outro script do site
  location: {pathname: '/'},
});
vm.runInContext(fs.readFileSync('assets/js/dados.js', 'utf8'), cart);
vm.runInContext(fs.readFileSync('assets/js/carrinho.js', 'utf8'), cart);
cart.novoSite = {
  matriz: {slug: 'matriz', nome: 'Matriz — Centro'},
  promocoes: [promo('B', 15, true, ['matriz'], ['p1'])],
};
vm.runInContext('site = novoSite', cart);

test('resumoDoCarrinho soma cheio, desconto e total', () => {
  const itens = [{slug: 'p1', preco: 1.2, qtd: 100}, {slug: 'p9', preco: 3, qtd: 10}];
  assert.deepEqual({...cart.resumoDoCarrinho(itens)}, {subtotal: 150, desconto: 18, total: 132});
});
test('item de produto que saiu do catálogo fica sem desconto', () => {
  assert.equal(cart.valoresDoItem({slug: 'sumiu', preco: 2, qtd: 5}).promocao, null);
});
test('mensagem do WhatsApp mostra de/por e Descontos', () => {
  const msg = cart.mensagemDoPedido([{slug: 'p1', nome: 'Pão', por: '100 un', preco: 1.2, qtd: 100}], 'Rua X', 'Pix', '');
  assert.match(msg, /• 100x Pão \(100 un\) — de R\$ 120,00 por R\$ 102,00 \(B −15%\)/);
  assert.match(msg, /Descontos: − R\$ 18,00\nTotal estimado: R\$ 102,00/);
});
test('mensagem sem desconto fica igual à de hoje', () => {
  const msg = cart.mensagemDoPedido([{slug: 'p9', nome: 'Suco', por: 'un', preco: 3, qtd: 10}], '', 'Pix', '');
  assert.doesNotMatch(msg, /Descontos|de R\$/);
  assert.match(msg, /• 10x Suco \(un\) — R\$ 30,00\n\nTotal estimado: R\$ 30,00/);
});
test('linha do carrinho: promo só quando há desconto', () => {
  const base = {id: 'p1', slug: 'p1', nome: 'Pão', por: 'un', preco: 1.2, qtd: 100, min: 1, passo: 1};
  const com = cart.linhaDoCarrinho(base);
  assert.match(com, /<s class="item-pedido-cheio">R\$ 120,00<\/s> R\$ 102,00/);
  assert.match(com, /<p class="item-pedido-promo">B −15% · válido na Matriz — Centro<\/p>/);
  const sem = cart.linhaDoCarrinho({...base, slug: 'p9'});
  assert.doesNotMatch(sem, /item-pedido-promo|item-pedido-cheio/);
});
