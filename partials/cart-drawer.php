<?php

declare(strict_types=1);

/**
 * partials/cart-drawer.php — carrinho lateral (drawer do daisyUI).
 *
 * Deve ser irmão de .drawer-content, dentro de .drawer (veja footer.php).
 * A lista de itens é desenhada por assets/js/cart.js a partir do
 * localStorage['dolce_cart'] — aqui só existe a casca e o estado vazio.
 *
 * Janela centralizada (o mecanismo continua o drawer do daisyUI; o CSS de
 * .gaveta-carrinho a põe no meio da tela). Desenho reto, como A empresa e
 * Unidades, e só o essencial: cabeça marrom com o título e a contagem, os
 * itens (a única parte que rola) e o rodapé com o total e o botão. Unidade,
 * pagamento e observação ficam em /carrinho, onde o pedido é fechado.
 *
 * Acessibilidade: o painel recebe `inert` enquanto está fechado (assets/js/ui.js),
 * o que tira tudo que está dentro dele do foco e do leitor de tela.
 */
?>
<div class="drawer-side z-[70]">
  <?php // A camada escura fecha no clique; para teclado e leitor de tela quem fecha é o botão X. ?>
  <label for="carrinho-toggle" class="drawer-overlay" aria-hidden="true"></label>

  <?php // <div> e não <aside>: aside não aceita role="dialog". Pelo mesmo motivo a
     // cabeça e o fechamento são <div> — <header>/<footer> soltos num diálogo
     // viram um segundo cabeçalho e um segundo rodapé da página. ?>
  <div id="painel-carrinho"
         role="dialog"
         aria-modal="true"
         aria-labelledby="titulo-carrinho"
         class="gaveta-carrinho flex flex-col bg-papel text-base-content">

    <div class="gaveta-cabeca">
      <h2 id="titulo-carrinho">Seu pedido</h2>
      <span class="gaveta-contagem" data-cart-count>0 itens</span>
      <?php // Primeiro [data-cart-close] do painel: é nele que o foco entra ao abrir. ?>
      <button type="button" data-cart-close class="gaveta-fechar" aria-label="Fechar o pedido">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
      </button>
    </div>

    <!-- Estado vazio -->
    <div data-cart-empty class="gaveta-vazio">
      <span class="gaveta-vazio-icone"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M3 4h2l2.4 11.2a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.5L21 8H6"/><circle cx="10" cy="20" r="1.4"/><circle cx="18" cy="20" r="1.4"/>
      </svg></span>
      <p class="text-lg font-bold">Seu pedido está vazio</p>
      <p class="max-w-xs text-sm leading-relaxed text-crust">Escolha os itens no catálogo. Eles ficam guardados neste aparelho até você fechar o pedido.</p>
      <a href="/#catalogo" data-cart-close class="botao-primario mt-2">Ver o catálogo</a>
    </div>

    <!-- Itens (desenhados por assets/js/cart.js) -->
    <ul data-cart-items class="oculto min-h-0 flex-1 divide-y divide-linha overflow-y-auto overscroll-contain px-5"></ul>

    <!-- Fechamento -->
    <div data-cart-footer class="oculto gaveta-rodape">
      <div class="flex items-baseline justify-between gap-4">
        <span class="text-sm font-semibold text-crust">Total estimado</span>
        <span class="text-2xl font-bold" data-cart-total>R$ 0,00</span>
      </div>

      <?php
      /**
       * O drawer NÃO fecha o pedido: ele leva para carrinho.php, onde ficam as
       * perguntas da confirmação (retirada/entrega e pagamento — ver
       * partials/pedido-validacao.php) e a observação. Duplicar os campos aqui
       * seria manter duas cópias em sincronia para sempre.
       *
       * Por ser navegação, é <a> e não <button>: aparece no histórico e
       * funciona sem JavaScript.
       */
      ?>
      <a href="/carrinho" class="botao-primario mt-4 w-full">Revisar e fechar pedido</a>

      <div class="mt-2 text-center">
        <button type="button" data-cart-clear class="link-discreto">Esvaziar o pedido</button>
      </div>
    </div>
  </div>
</div>
