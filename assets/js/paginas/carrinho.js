// Página do pedido: repete no resumo a forma de pagamento escolhida.

iniciarPagina(function () {
  const opcoes = document.querySelectorAll('[data-pedido-pagamento]');
  for (let i = 0; i < opcoes.length; i++) {
    opcoes[i].addEventListener('change', mostrarPagamento);
  }
  mostrarPagamento();
});

function mostrarPagamento() {
  const marcado = document.querySelector('[data-pedido-pagamento]:checked');
  const nome = marcado.closest('.opcao').querySelector('.font-bold').textContent;
  document.querySelector('[data-pedido-resumo-pagamento]').textContent = nome;
}
