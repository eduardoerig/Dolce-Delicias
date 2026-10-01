<?php

declare(strict_types=1);

/**
 * partials/product-promotions.php — as promoções que valem para este produto,
 * na página do produto. O card do catálogo mostra só o selo; aqui vai a regra.
 *
 * Espera $produto; usa $ofertas se a página já tiver calculado.
 */

$ofertasProduto = $ofertas ?? dd_promocoes_produto($produto);
if ($ofertasProduto === []) {
    return;
}
?>
<section class="produto-bloco" aria-labelledby="promo-titulo">
  <h2 id="promo-titulo" class="produto-subtitulo"><?= count($ofertasProduto) > 1 ? 'Promoções' : 'Promoção' ?></h2>
  <ul class="mt-3 grid gap-3">
    <?php foreach ($ofertasProduto as $oferta): ?>
      <li class="promo-produto">
        <p class="flex flex-wrap items-center gap-2">
          <span class="selo selo-promo"><?= e(trim((string) ($oferta['selo'] ?? '')) ?: 'Promoção') ?></span>
          <span class="text-xs font-bold <?= !empty($oferta['vigente']) ? 'text-success' : 'text-crust' ?>">
            <?= !empty($oferta['vigente']) ? 'Vale hoje' : 'Programe-se' ?>
          </span>
        </p>
        <h3 class="mt-2 font-sans text-base font-bold"><?= e((string) ($oferta['titulo'] ?? '')) ?></h3>
        <?php if (trim((string) ($oferta['texto'] ?? '')) !== ''): ?>
          <p class="mt-1 text-sm leading-relaxed text-crust"><?= e((string) $oferta['texto']) ?></p>
        <?php endif; ?>
        <p class="mt-2 text-xs leading-relaxed text-crust">
          <?= e(dd_promocao_regra($oferta)) ?>. Vale em: <?= e((string) ($oferta['lojas'] ?? '')) ?>.
        </p>
      </li>
    <?php endforeach; ?>
  </ul>
  <p class="mt-2 text-xs leading-relaxed text-crust">O site mostra o preço cheio; o desconto é aplicado pela matriz no fechamento pelo WhatsApp.</p>
</section>
