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
