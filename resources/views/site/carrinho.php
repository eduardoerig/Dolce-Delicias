<?php

declare(strict_types=1);

/**
 * carrinho.php — o pedido em página inteira.
 *
 * Três etapas, nesta ordem também no celular: os itens, como receber e como
 * pagar. O resumo com o total e o botão fica ao lado no computador e por
 * último no celular. Sem pagamento e sem back-end — o fechamento é pelo
 * WhatsApp da matriz.
 *
 * A lista é montada por assets/js/cart.js a partir do localStorage['dolce_cart'],
 * igual ao painel lateral: os mesmos data-* servem os dois.
 */

require_once DD_BASE . '/partials/bootstrap.php';

$tituloPagina    = 'Seu pedido — Dolce Delícias';
$descricaoPagina = 'Revise os itens da sua encomenda e feche no WhatsApp da matriz da Dolce Delícias.';

include DD_BASE . '/partials/header.php';
?>

<div class="bg-farinha">

  <?php // Herói: foto de fundo, camada escura, título e o que fazer aqui. ?>
  <?php ob_start(); ?>
    <h1 id="titulo-pedido" class="text-4xl sm:text-5xl">Seu pedido</h1>
    <p class="mt-3 max-w-xl text-lg leading-relaxed">
      Confira as quantidades, diga como quer receber e feche pelo WhatsApp da matriz. Nada é cobrado aqui.
    </p>
  <?php
  $heroiConteudo = (string) ob_get_clean();
  $heroiFoto     = 'pedido';
  $heroiTitulo   = 'titulo-pedido';
  $heroiCaminho  = 'Seu pedido';
  $heroiTom      = 'escuro';
  $heroiBaixo    = true;
  include DD_BASE . '/partials/heroi-foto.php';
  ?>

  <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-10">

    <!-- Estado vazio -->
    <div data-cart-empty class="cartao-etapa flex flex-col items-center gap-3 px-6 py-16 text-center">
      <span class="grid h-16 w-16 place-items-center rounded-full bg-polvilho text-crust">
        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M3 4h2l2.4 11.2a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.5L21 8H6"/><circle cx="10" cy="20" r="1.4"/><circle cx="18" cy="20" r="1.4"/>
        </svg>
      </span>
      <p class="text-xl font-bold">Seu pedido está vazio</p>
      <p class="max-w-sm text-sm leading-relaxed text-crust">
        Escolha os itens no catálogo. Eles ficam guardados neste aparelho até você fechar o pedido.
      </p>
      <a href="/#catalogo" class="botao-primario mt-2">Ver o catálogo</a>
    </div>

    <div data-cart-footer class="oculto grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_24rem] lg:items-start lg:gap-8">

      <div class="grid min-w-0 grid-cols-1 gap-6">
        <!-- 1. Itens -->
        <section class="cartao-etapa" aria-labelledby="etapa-itens">
          <div class="flex items-center justify-between gap-3">
            <h2 id="etapa-itens" class="etapa-titulo"><span class="etapa-numero" aria-hidden="true">1</span>Itens do pedido</h2>
            <button type="button" data-cart-clear class="link-discreto">Esvaziar</button>
          </div>

          <!-- Lista (desenhada por assets/js/cart.js) -->
          <ul data-cart-items class="oculto mt-2 divide-y divide-linha"></ul>

          <a href="/#catalogo" class="link-acao mt-2">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            Adicionar mais itens
          </a>
        </section>

        <?php
        // 2 e 3: como receber e como pagar (partials/pedido-validacao.php).
        include DD_BASE . '/partials/pedido-validacao.php';
        ?>
      </div>

      <!-- Resumo -->
      <aside class="cartao-etapa lg:sticky lg:top-24" aria-labelledby="titulo-resumo">
        <h2 id="titulo-resumo" class="etapa-titulo">Resumo</h2>

        <?php // Pedido pelo site é sempre com a matriz (ver matriz() em cart.js);
           // o nome é escrito por ui.js. Recebimento e pagamento repetem o que
           // está marcado nas etapas, para ninguém mandar sem conferir. ?>
        <dl class="mt-4 grid gap-2.5 text-sm">
          <div class="flex justify-between gap-4">
            <dt class="text-crust">Pedido para</dt>
            <dd class="text-right font-semibold" data-unit-label>Matriz</dd>
          </div>
          <div class="flex justify-between gap-4">
            <dt class="text-crust">Itens</dt>
            <dd class="text-right font-semibold" data-cart-count>0 itens</dd>
          </div>
          <div class="flex justify-between gap-4">
            <dt class="text-crust">Recebimento</dt>
            <dd class="text-right font-semibold" data-pedido-resumo-entrega>Retirar na matriz</dd>
          </div>
          <div class="flex justify-between gap-4">
            <dt class="text-crust">Pagamento</dt>
            <dd class="text-right font-semibold" data-pedido-resumo-pagamento>Pix</dd>
          </div>
        </dl>

        <details class="group mt-4 border-t border-linha pt-4">
          <summary class="flex w-fit cursor-pointer list-none items-center gap-1.5 text-sm font-semibold text-crust hover:text-brand [&::-webkit-details-marker]:hidden">
            <svg class="h-4 w-4 transition-transform group-open:rotate-90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 6 6 6-6 6"/></svg>
            Adicionar observação
          </summary>
          <label class="mt-2 block">
            <span class="sr-only">Observação sobre o pedido</span>
            <textarea data-cart-obs rows="3" class="campo-texto"
                      placeholder="Data da entrega, sabores, ponto de referência…"></textarea>
          </label>
        </details>

        <div class="mt-4 flex items-baseline justify-between gap-4 border-t border-linha pt-4">
          <span class="font-semibold">Total estimado</span>
          <span class="text-2xl font-bold" data-cart-total>R$ 0,00</span>
        </div>

        <button type="button" data-cart-checkout class="botao-primario mt-4 min-h-13 w-full whitespace-normal text-center text-base">
          <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M12.04 2C6.6 2 2.2 6.4 2.2 11.84c0 1.74.46 3.44 1.32 4.94L2 22l5.36-1.4a9.8 9.8 0 0 0 4.68 1.2c5.44 0 9.84-4.4 9.84-9.84S17.48 2 12.04 2Zm5.72 13.9c-.24.68-1.4 1.3-1.94 1.34-.5.06-1.12.08-1.8-.12-.42-.12-.96-.3-1.64-.6-2.9-1.26-4.78-4.18-4.92-4.38-.14-.2-1.18-1.56-1.18-2.98 0-1.42.74-2.12 1-2.4.26-.3.58-.36.78-.36h.56c.18 0 .42-.06.66.5.24.58.82 2 .9 2.14.06.14.1.3.02.48-.1.2-.14.32-.28.48-.14.18-.3.38-.42.5-.14.14-.28.3-.12.58.16.28.72 1.18 1.54 1.92 1.06.94 1.94 1.24 2.22 1.38.28.14.44.12.6-.08.16-.18.68-.8.86-1.08.18-.28.36-.22.6-.14.24.1 1.56.74 1.82.88.28.14.44.2.5.32.08.1.08.62-.16 1.3Z"/>
          </svg>
          Fechar pedido no WhatsApp
        </button>

        <?php // RF-18 fora do escopo: nada de frete aqui. O que a matriz confirma
           // na conversa é preço e prazo. ?>
        <p class="mt-3 text-xs leading-relaxed text-crust">
          Valor de referência. A matriz confirma preço, prazo e entrega na conversa.
        </p>
      </aside>
    </div>
  </div>
</div>

<?php include DD_BASE . '/partials/footer.php'; ?>
