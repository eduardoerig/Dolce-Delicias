const brl=new Intl.NumberFormat('pt-BR',{style:'currency',currency:'BRL'});
export function totalPedido(itens){return itens.reduce((sum,item)=>sum+Number(item.preco)*Number(item.qtd),0);}
export function mensagemPedido(itens,unidade,confirmacao,observacao=''){
 const partes=['Olá! Quero fazer uma encomenda pelo site da Dolce Delícias.','',`Unidade: ${unidade.nome}`,`Como receber: ${confirmacao.entrega}`];
 if(confirmacao.entrega.toLowerCase().startsWith('entrega')&&confirmacao.endereco)partes.push(`Endereço: ${confirmacao.endereco}`);
 partes.push('',...itens.map(i=>`• ${i.qtd}x ${i.nome} (${i.por}) — ${brl.format(Number(i.preco)*Number(i.qtd))}`),'',`Total estimado: ${brl.format(totalPedido(itens))}`,`Forma de pagamento: ${confirmacao.pagamento}`,'Preços cheios. Promoções confirmadas pela matriz no fechamento.');
 if(unidade.preparo)partes.push(`Preparo mínimo: ${unidade.preparo}`);
 if(observacao)partes.push('',`Observação: ${observacao}`);
 return partes.join('\n');
}
