import {test} from 'node:test';
import assert from 'node:assert/strict';
import vm from 'node:vm';
import fs from 'node:fs';

const ctx = vm.createContext({
  document: {querySelector: () => null},
  location: {pathname: '/pages/unidades.html'},
  iniciarPagina: () => {},
});
vm.runInContext(fs.readFileSync('assets/js/dados.js', 'utf8'), ctx);
vm.runInContext(fs.readFileSync('assets/js/paginas/unidades.js', 'utf8'), ctx);

const ASSET = 'https://cdn.sanity.io/files/wdisx8zs/production/abc.pdf';

test('linkDoPdf: arquivo da pasta fica igual, o do Sanity ganha ?dl=', () => {
  assert.equal(ctx.linkDoPdf('catalogos/unidade-1.pdf'), '../catalogos/unidade-1.pdf');
  assert.equal(ctx.linkDoPdf(ASSET), ASSET + '?dl=');
  assert.equal(ctx.linkDoPdf(''), '');
  assert.equal(ctx.linkDoPdf('javascript:alert(1)'), '');
});

test('pdfCompleto: PDF enviado na matriz, senão o da pasta', () => {
  // 'let site' não vira propriedade do contexto, por isso a atribuição roda dentro dele.
  vm.runInContext('site = {matriz: {catalogoPdf: "catalogos/matriz.pdf"}}', ctx);
  assert.equal(ctx.pdfCompleto(), '../catalogos/completo.pdf');
  vm.runInContext(`site = {matriz: {catalogoPdf: "${ASSET}"}}`, ctx);
  assert.equal(ctx.pdfCompleto(), ASSET + '?dl=');
  vm.runInContext('site = {matriz: null}', ctx);
  assert.equal(ctx.pdfCompleto(), '../catalogos/completo.pdf');
});

test('tituloEBairro: do nome com "—" ou do fim do endereço', () => {
  assert.deepEqual({...ctx.tituloEBairro({nome: 'Matriz — Centro', endereco: 'Rua X — Outro'})}, {titulo: 'Matriz', bairro: 'Centro'});
  assert.deepEqual({...ctx.tituloEBairro({nome: 'Unidade 1', endereco: 'Av. Brasil, 1.250 — Jardim América'})}, {titulo: 'Unidade 1', bairro: 'Jardim América'});
  assert.deepEqual({...ctx.tituloEBairro({nome: 'Loja Nova'})}, {titulo: 'Loja Nova', bairro: ''});
});
