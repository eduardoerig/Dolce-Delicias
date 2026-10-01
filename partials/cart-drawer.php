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
  <?php // A camada escura fecha no clique; para teclado e leitor de tela quem fecha é o botão X. ?>
  <label for="carrinho-toggle" class="drawer-overlay" aria-hidden="true"></label>

  <?php // <div> e não <aside>: aside não aceita role="dialog". Pelo mesmo motivo a
     // cabeça e o fechamento são <div> — <header>/<footer> soltos num diálogo
     // viram um segundo cabeçalho e um segundo rodapé da página. ?>
  <div id="painel-carrinho"
         role="dialog"
         aria-modal="true"
         aria-labelledby="titulo-carrinho"
         class="gaveta-carrinho flex h-full w-[min(30rem,100vw)] flex-col bg-papel text-base-content shadow-bandeja-alta">

    <div class="flex items-center gap-2.5 px-6 pb-3 pt-5">
      <h2 id="titulo-carrinho" class="text-xl">Seu pedido</h2>
      <span class="text-sm font-semibold text-crust" data-cart-count>0 itens</span>
      <button type="button" data-cart-close
              class="icone-redondo ml-auto"
              aria-label="Fechar o pedido">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
      </button>
    </div>

    <?php // Unidade em uma linha só: é para o WhatsApp dela que o pedido vai. ?>
    <p class="border-b border-linha px-6 pb-4 text-xs text-crust">
      Pedido pela <span class="font-bold text-base-content" data-unit-label>Matriz</span>
    </p>

    <!-- Estado vazio -->
    <div data-cart-empty class="flex flex-1 flex-col items-center justify-center gap-3 px-8 text-center">
      <span class="grid h-14 w-14 place-items-center rounded-full bg-polvilho text-crust"><svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M3 4h2l2.4 11.2a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.5L21 8H6"/><circle cx="10" cy="20" r="1.4"/><circle cx="18" cy="20" r="1.4"/>
      </svg></span>
      <p class="text-sm text-crust">Nenhum item por aqui ainda.</p>
      <a href="/#catalogo" class="botao-primario">Ver o catálogo</a>
    </div>

    <!-- Itens (desenhados por assets/js/cart.js) -->
    <ul data-cart-items class="oculto flex-1 divide-y divide-linha overflow-y-auto px-6"></ul>

    <!-- Fechamento -->
    <div data-cart-footer class="oculto border-t border-linha px-6 py-5">
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
                    class="campo-texto mt-2"
                    placeholder="Data da entrega, sabores, endereço…"></textarea>
        </label>
      </details>

      <div class="mt-3 flex items-baseline justify-between">
        <span class="text-sm font-semibold text-crust">Total estimado</span>
        <span class="text-xl font-bold" data-cart-total>R$ 0,00</span>
      </div>

      <?php
      /**
       * O drawer NÃO fecha o pedido: ele leva para carrinho.php, onde ficam as
       * quatro perguntas da confirmação (horário, prazo, retirada/entrega e
       * pagamento — ver partials/pedido-validacao.php). Cabiam aqui? Não: são
       * quatro decisões num painel de 30rem que já rola. E duplicar os campos
       * nos dois lugares seria manter duas cópias em sincronia para sempre.
       *
       * Por ser navegação, é <a> e não <button>: abre em nova aba, aparece no
       * histórico e funciona sem JavaScript.
       */
      ?>
      <a href="/carrinho" class="botao-primario mt-3 w-full">Revisar e fechar pedido</a>

      <div class="mt-2.5 flex items-center justify-end text-xs font-semibold text-crust">
        <button type="button" data-cart-clear
                class="link-discreto">
          Esvaziar
        </button>
      </div>
    </div>
  </div>
</div>
