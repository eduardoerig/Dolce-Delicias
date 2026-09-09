<?php

declare(strict_types=1);

/**
 * partials/footer.php — fecha o <main>, desenha o rodapé, fecha o drawer e o documento.
 * Sempre depois de partials/header.php.
 */

require_once __DIR__ . '/bootstrap.php';

$unidadesRodape = dd_unidades();
$matrizRodape   = $unidadesRodape[0] ?? null;
foreach ($unidadesRodape as $u) {
    if (!empty($u['matriz'])) {
        $matrizRodape = $u;
        break;
    }
}
$anoAtual = date('Y');
?>
    </main>

    <div class="faixa-raios" aria-hidden="true"></div>

    <footer id="contato" class="bg-neutral text-neutral-content">
      <div class="mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 md:grid-cols-[1.2fr_1fr_1fr] lg:px-8">

        <div>
          <?= dd_logo('h-14 w-auto') ?>
          <p class="mt-4 max-w-xs text-sm leading-relaxed opacity-80">
            Padaria e panificadora. Atendemos escolas, faculdades, eventos e encomendas
            em <?= count($unidadesRodape) ?> unidades.
          </p>
          <a href="https://instagram.com/dolcedeliciasoficial"
             target="_blank" rel="noopener noreferrer"
             class="mt-5 inline-flex items-center gap-2 rounded-xl bg-white/10 px-3.5 py-2.5 text-sm font-semibold transition-colors hover:bg-white/20">
            <svg class="h-[1.15rem] w-[1.15rem]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
              <rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/>
            </svg>
            @dolcedeliciasoficial
          </a>
        </div>

        <nav aria-label="Rodapé">
          <h2 class="text-lg">Navegar</h2>
          <ul class="mt-4 space-y-2.5 text-sm">
            <li><a class="rounded opacity-80 transition-opacity hover:opacity-100" href="index.php#catalogo">Catálogo</a></li>
            <li><a class="rounded opacity-80 transition-opacity hover:opacity-100" href="index.php#encomendas">Como encomendar</a></li>
            <li><a class="rounded opacity-80 transition-opacity hover:opacity-100" href="unidades.php">Nossas unidades</a></li>
            <li><a class="rounded opacity-80 transition-opacity hover:opacity-100" href="carrinho.php">Meu pedido</a></li>
          </ul>
        </nav>

        <div>
          <h2 class="text-lg">Matriz</h2>
          <?php if ($matrizRodape): ?>
            <address class="mt-4 space-y-2 text-sm not-italic opacity-80">
              <p><?= e($matrizRodape['endereco']) ?></p>
              <p><?= e($matrizRodape['horario']) ?></p>
            </address>
            <?php // INTEGRAÇÃO FUTURA: o número real entra em data/units.php. ?>
            <a href="https://wa.me/<?= e($matrizRodape['whatsapp']) ?>"
               target="_blank" rel="noopener noreferrer"
               class="mt-5 inline-flex items-center gap-2 rounded-xl bg-accent px-3.5 py-2.5 text-sm font-bold text-accent-content transition-transform hover:-translate-y-0.5">
              Falar no WhatsApp
            </a>
          <?php endif; ?>
        </div>
      </div>

      <div class="border-t border-white/10">
        <p class="mx-auto max-w-7xl px-4 py-6 text-xs opacity-60 sm:px-6 lg:px-8">
          © <?= e((string) $anoAtual) ?> Dolce Delícias. Vitrine online — os pedidos são fechados por WhatsApp com a matriz.
        </p>
      </div>
    </footer>

  </div><!-- /drawer-content -->

  <?php include __DIR__ . '/cart-drawer.php'; ?>

</div><!-- /drawer -->

<?php
/**
 * Ícones de placeholder, um por categoria — os MESMOS que o card do catálogo usa.
 * O carrinho lê daqui quando o item ainda não tem foto, em vez de o JavaScript
 * carregar uma segunda cópia dos SVGs.
 */
?>
<template id="icones-produto">
  <?php foreach (dd_categorias() as $categoriaIcone): ?>
    <div data-icone-categoria="<?= e($categoriaIcone) ?>"><?= dd_icone_categoria($categoriaIcone) ?></div>
  <?php endforeach; ?>
  <div data-icone-categoria=""><?= dd_icone_categoria('') ?></div>
</template>

<!-- Avisos curtos (item adicionado, item removido). Preenchido por assets/js/ui.js. -->
<div class="toast toast-end z-[80] p-4" data-toast-area aria-live="polite" aria-atomic="true"></div>

</body>
</html>
