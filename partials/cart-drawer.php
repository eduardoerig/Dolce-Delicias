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

      <button type="button" data-cart-checkout
              class="btn btn-primary mt-3 w-full font-bold">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
          <path d="M12.04 2C6.6 2 2.2 6.4 2.2 11.84c0 1.74.46 3.44 1.32 4.94L2 22l5.36-1.4a9.8 9.8 0 0 0 4.68 1.2c5.44 0 9.84-4.4 9.84-9.84S17.48 2 12.04 2Zm5.72 13.9c-.24.68-1.4 1.3-1.94 1.34-.5.06-1.12.08-1.8-.12-.42-.12-.96-.3-1.64-.6-2.9-1.26-4.78-4.18-4.92-4.38-.14-.2-1.18-1.56-1.18-2.98 0-1.42.74-2.12 1-2.4.26-.3.58-.36.78-.36h.56c.18 0 .42-.06.66.5.24.58.82 2 .9 2.14.06.14.1.3.02.48-.1.2-.14.32-.28.48-.14.18-.3.38-.42.5-.14.14-.28.3-.12.58.16.28.72 1.18 1.54 1.92 1.06.94 1.94 1.24 2.22 1.38.28.14.44.12.6-.08.16-.18.68-.8.86-1.08.18-.28.36-.22.6-.14.24.1 1.56.74 1.82.88.28.14.44.2.5.32.08.1.08.62-.16 1.3Z"/>
        </svg>
        Finalizar no WhatsApp
      </button>

      <div class="mt-2.5 flex items-center justify-between text-xs font-semibold text-crust">
        <a href="carrinho.php" class="rounded underline decoration-base-300 underline-offset-4 hover:text-brand">
          Abrir em página inteira
        </a>
        <button type="button" data-cart-clear
                class="rounded underline decoration-base-300 underline-offset-4 hover:text-brand">
          Esvaziar
        </button>
      </div>
    </footer>
  </aside>
</div>
