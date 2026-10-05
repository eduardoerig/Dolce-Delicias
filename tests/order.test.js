import test from 'node:test';
import assert from 'node:assert/strict';
import {totalPedido,mensagemPedido} from '../assets/js/order.js';
const itens=[{nome:'Pão de queijo',qtd:150,preco:1.2,por:'R$ 120 / 100 un',desconto:20}];
test('preço cheio por peça, sem aplicação de desconto',()=>assert.equal(totalPedido(itens),180));
test('WhatsApp inclui entrega, pagamento, preparo e observação',()=>{
 const msg=mensagemPedido(itens,{nome:'Matriz',preparo:'48 horas'},{endereco:'Rua A, 42',pagamento:'Pix'},'Sem cebola');
 for(const texto of ['150x Pão de queijo','Matriz','Rua A, 42','Pix','48 horas','Sem cebola','180,00'])assert.ok(msg.includes(texto));
});
test('sem endereço, a entrega é combinada na conversa',()=>assert.ok(mensagemPedido(itens,{nome:'Matriz'},{endereco:'',pagamento:'Pix'}).includes('Entrega: endereço a combinar na conversa')));
