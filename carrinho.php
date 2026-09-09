<?php

declare(strict_types=1);

/**
 * carrinho.php — o pedido em página inteira.
 *
 * A lista é montada por assets/js/cart.js a partir do localStorage['dolce_cart'],
 * exatamente como no drawer lateral: os mesmos data-* servem os dois.
 * Sem pagamento e sem back-end — o fechamento é pelo WhatsApp da matriz.
 */

require_once __DIR__ . '/partials/bootstrap.php';

$tituloPagina    = 'Seu pedido — Dolce Delícias';
$descricaoPagina = 'Revise os itens da sua encomenda e feche no WhatsApp da matriz da Dolce Delícias.';

include __DIR__ . '/partials/header.php';
?>

<div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14">

  <nav aria-label="Você está aqui" class="mb-6 flex items-center gap-2 text-sm font-semibold text-crust">
    <a href="index.php" class="rounded hover:text-brand">Início</a>
    <span aria-hidden="true">/</span>
    <span class="text-base-content">Seu pedido</span>
  </nav>

  <h1 class="text-4xl sm:text-5xl">Seu pedido</h1>
  <p class="mt-3 max-w-xl text-lg leading-relaxed text-crust">
    Confira as quantidades antes de mandar. Nada é cobrado aqui.
  </p>

  <div class="mt-9 grid gap-8 lg:grid-cols-[1.4fr_1fr] lg:items-start">

    <!-- Itens -->
    <div>
      <!-- Estado vazio -->
      <div data-cart-empty class="flex flex-col items-center gap-3 rounded-bandeja border border-dashed border-base-300 px-6 py-16 text-center">
        <svg class="h-10 w-10 text-crust/35" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M3 4h2l2.4 11.2a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.5L21 8H6"/><circle cx="10" cy="20" r="1.4"/><circle cx="18" cy="20" r="1.4"/>
        </svg>
        <p class="text-lg font-bold">Seu pedido está vazio</p>
        <p class="max-w-sm text-sm text-crust">
          Os itens ficam guardados neste navegador até você fechar o pedido.
        </p>
        <a href="index.php#catalogo" class="btn btn-primary btn-sm mt-1 font-bold">
          Ver o catálogo
        </a>
      </div>

      <!-- Lista (desenhada por assets/js/cart.js) -->
      <ul data-cart-items
          class="oculto divide-y divide-base-300 overflow-hidden rounded-bandeja border border-base-300 bg-papel px-5 shadow-bandeja"></ul>

      <div data-cart-footer class="oculto mt-4 flex items-center justify-between gap-3">
        <a href="index.php#catalogo" class="rounded text-sm font-semibold text-crust underline decoration-base-300 underline-offset-4 hover:text-brand">
          Continuar escolhendo
        </a>
        <button type="button" data-cart-clear
                class="rounded text-sm font-semibold text-crust underline decoration-base-300 underline-offset-4 hover:text-brand">
          Esvaziar pedido
        </button>
      </div>
    </div>

    <!-- Resumo -->
    <aside class="rounded-bandeja border border-base-300 bg-papel p-6 shadow-bandeja lg:sticky lg:top-24">
      <h2 class="text-xl">Resumo</h2>

      <?php // Pedido pelo site é sempre com a matriz — ver matriz() em cart.js.
         // O nome sai de data/units.php, escrito por ui.js. ?>
      <p class="mt-3 text-sm text-crust">
        Pedido pela <span class="font-bold text-base-content" data-unit-label>Matriz</span>
      </p>

      <details class="group mt-4">
        <summary class="flex w-fit cursor-pointer list-none items-center gap-1 text-xs font-semibold text-crust hover:text-brand [&::-webkit-details-marker]:hidden">
          <svg class="h-3.5 w-3.5 transition-transform group-open:rotate-90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 6 6 6-6 6"/></svg>
          Adicionar observação
        </summary>
        <label>
          <span class="sr-only">Observação sobre o pedido</span>
          <textarea data-cart-obs rows="3"
                    class="textarea mt-2 w-full resize-none rounded-xl border-campo bg-base-200 text-sm"
                    placeholder="Data da entrega, sabores, endereço…"></textarea>
        </label>
      </details>

      <div class="mt-5 flex items-baseline justify-between border-t border-base-300 pt-4">
        <span class="font-semibold text-crust">Total estimado</span>
        <span class="fonte-display text-3xl text-brand" data-cart-total>R$ 0,00</span>
      </div>

      <button type="button" data-cart-checkout
              class="btn btn-primary mt-4 h-13 min-h-13 w-full text-base font-bold">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
          <path d="M12.04 2C6.6 2 2.2 6.4 2.2 11.84c0 1.74.46 3.44 1.32 4.94L2 22l5.36-1.4a9.8 9.8 0 0 0 4.68 1.2c5.44 0 9.84-4.4 9.84-9.84S17.48 2 12.04 2Zm5.72 13.9c-.24.68-1.4 1.3-1.94 1.34-.5.06-1.12.08-1.8-.12-.42-.12-.96-.3-1.64-.6-2.9-1.26-4.78-4.18-4.92-4.38-.14-.2-1.18-1.56-1.18-2.98 0-1.42.74-2.12 1-2.4.26-.3.58-.36.78-.36h.56c.18 0 .42-.06.66.5.24.58.82 2 .9 2.14.06.14.1.3.02.48-.1.2-.14.32-.28.48-.14.18-.3.38-.42.5-.14.14-.28.3-.12.58.16.28.72 1.18 1.54 1.92 1.06.94 1.94 1.24 2.22 1.38.28.14.44.12.6-.08.16-.18.68-.8.86-1.08.18-.28.36-.22.6-.14.24.1 1.56.74 1.82.88.28.14.44.2.5.32.08.1.08.62-.16 1.3Z"/>
        </svg>
        Finalizar no WhatsApp
      </button>

      <p class="mt-3 text-xs leading-relaxed text-crust">
        Valor de referência: a matriz confirma preço, prazo e frete na conversa.
      </p>
    </aside>
  </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
