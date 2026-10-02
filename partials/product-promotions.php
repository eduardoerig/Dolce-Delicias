<?php

declare(strict_types=1);

/**
 * partials/product-promotions.php — as promoções que valem para este produto,
 * na página do produto. O card do catálogo mostra só o selo; aqui vai a regra.
 *
 * Mesmo desenho das ofertas da home (partials/promocoes.php), em tamanho de
 * coluna: uma faixa vermelha com o desconto em amarelo, "Vale hoje" ou
 * "Programe-se", o nome da oferta e quando e onde vale.
 *
 * Espera $produto; usa $ofertas se a página já tiver calculado.
 */

$ofertasProduto = $ofertas ?? dd_promocoes_produto($produto);
if ($ofertasProduto === []) {
    return;
}
?>
<section class="produto-ofertas" aria-labelledby="promo-titulo">
  <h2 id="promo-titulo" class="sr-only"><?= count($ofertasProduto) > 1 ? 'Promoções' : 'Promoção' ?></h2>
  <ul class="grid gap-2">
    <?php foreach ($ofertasProduto as $oferta): ?>
      <?php $ofertaVigente = !empty($oferta['vigente']); ?>
      <li class="produto-oferta">
        <p class="oferta-desconto"><?= e(trim((string) ($oferta['selo'] ?? '')) ?: 'Oferta') ?></p>
        <div class="min-w-0">
          <p class="flex flex-wrap items-center gap-x-3 gap-y-1">
            <strong class="text-lg leading-tight"><?= e((string) ($oferta['titulo'] ?? '')) ?></strong>
            <span class="<?= $ofertaVigente ? 'oferta-quando oferta-quando-hoje' : 'oferta-quando' ?>"><?= $ofertaVigente ? 'Vale hoje' : 'Programe-se' ?></span>
          </p>
          <p class="mt-1 text-sm leading-relaxed">
            <?= e(dd_promocao_regra($oferta)) ?>.<?php if (trim((string) ($oferta['lojas'] ?? '')) !== ''): ?> Vale em: <?= e((string) $oferta['lojas']) ?>.<?php endif; ?>
          </p>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>
  <p class="mt-2 text-xs leading-relaxed text-crust">O site mostra o preço cheio; o desconto é aplicado pela matriz no fechamento pelo WhatsApp.</p>
</section>
