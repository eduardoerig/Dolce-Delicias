<?php

declare(strict_types=1);

/**
 * partials/cart-drawer.php — carrinho lateral (drawer do daisyUI).
 *
 * Deve ser irmão de .drawer-content, dentro de .drawer (veja footer.php).
 * A lista de itens é desenhada por assets/js/cart.js a partir do
 * localStorage['dolce_cart'] — aqui só existe a casca e o estado vazio.
 *
 * Acessibilidade: o painel recebe `inert` enquanto está fechado (assets/js/ui.js),
 * o que tira tudo que está dentro dele do foco e do leitor de tela.
 */
?>
<div class="drawer-side z-[70]">
  <label for="carrinho-toggle" class="drawer-overlay" aria-label="Fechar o pedido"></label>

  <aside id="painel-carrinho"
         role="dialog"
         aria-modal="true"
         aria-labelledby="titulo-carrinho"
         class="flex h-full w-[min(24rem,100vw)] flex-col bg-papel text-base-content shadow-bandeja-alta">

    <header class="flex items-center gap-2.5 px-5 pb-3 pt-4">
      <h2 id="titulo-carrinho" class="text-xl">Seu pedido</h2>
      <span class="text-sm font-semibold text-crust" data-cart-count>0 itens</span>
      <button type="button" data-cart-close
              class="btn btn-ghost btn-sm ml-auto h-9 w-9 rounded-xl p-0"
              aria-label="Fechar o pedido">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
      </button>
    </header>

    <?php // Unidade em uma linha só: é para o WhatsApp dela que o pedido vai. ?>
    <p class="border-b border-base-300 px-5 pb-3 text-xs text-crust">
      Pedido pela <span class="font-bold text-base-content" data-unit-label>Matriz</span>
    </p>

    <!-- Estado vazio -->
    <div data-cart-empty class="flex flex-1 flex-col items-center justify-center gap-3 px-8 text-center">
      <svg class="h-10 w-10 text-crust/35" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M3 4h2l2.4 11.2a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.5L21 8H6"/><circle cx="10" cy="20" r="1.4"/><circle cx="18" cy="20" r="1.4"/>
      </svg>
      <p class="text-sm text-crust">Nenhum item por aqui ainda.</p>
      <a href="index.php#catalogo" class="btn btn-primary btn-sm font-bold">Ver o catálogo</a>
    </div>

    <!-- Itens (desenhados por assets/js/cart.js) -->
    <ul data-cart-items class="oculto flex-1 divide-y divide-base-300 overflow-y-auto px-5"></ul>

    <!-- Fechamento -->
    <footer data-cart-footer class="oculto border-t border-base-300 px-5 py-4">
      <?php // Observação fica recolhida: quase ninguém preenche, e aberta ela
         // dominava o rodapé. <details> abre e navega pelo teclado sem JS. ?>
      <details class="group">
        <summary class="flex w-fit cursor-pointer list-none items-center gap-1 text-xs font-semibold text-crust hover:text-brand [&::-webkit-details-marker]:hidden">
          <svg class="h-3.5 w-3.5 transition-transform group-open:rotate-90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 6 6 6-6 6"/></svg>
          Adicionar observação
        </summary>
        <label>
          <span class="sr-only">Observação sobre o pedido</span>
          <textarea data-cart-obs rows="2"
                    class="textarea mt-2 w-full resize-none rounded-xl border-campo bg-base-200 text-sm"
                    placeholder="Data da entrega, sabores, endereço…"></textarea>
        </label>
      </details>

      <div class="mt-3 flex items-baseline justify-between">
        <span class="text-sm font-semibold text-crust">Total estimado</span>
        <span class="fonte-display text-2xl text-brand" data-cart-total>R$ 0,00</span>
      </div>

      <?php
      /**
       * O drawer NÃO fecha o pedido: ele leva para carrinho.php, onde ficam as
       * quatro perguntas da confirmação (horário, prazo, retirada/entrega e
       * pagamento — ver partials/pedido-validacao.php). Cabiam aqui? Não: são
       * quatro decisões num painel de 24rem que já rola. E duplicar os campos
       * nos dois lugares seria manter duas cópias em sincronia para sempre.
       *
       * Por ser navegação, é <a> e não <button>: abre em nova aba, aparece no
       * histórico e funciona sem JavaScript.
       */
      ?>
      <a href="carrinho.php" class="btn btn-primary mt-3 w-full font-bold">
        Revisar e fechar pedido
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m13 6 6 6-6 6"/></svg>
      </a>

      <div class="mt-2.5 flex items-center justify-end text-xs font-semibold text-crust">
        <button type="button" data-cart-clear
                class="rounded underline decoration-base-300 underline-offset-4 hover:text-brand">
          Esvaziar
        </button>
      </div>
    </footer>
  </aside>
</div>
