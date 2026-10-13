// Página do pedido: repete no resumo a forma de pagamento escolhida.

iniciarPagina(function () {
  for (const opcao of document.querySelectorAll('[data-pedido-pagamento]')) {
    opcao.addEventListener('change', mostrarPagamento);
  }
  mostrarPagamento();
});

function mostrarPagamento() {
  const marcado = document.querySelector('[data-pedido-pagamento]:checked');
  const nome = marcado.closest('.opcao').querySelector('.font-bold').textContent;
  document.querySelector('[data-pedido-resumo-pagamento]').textContent = nome;
}
