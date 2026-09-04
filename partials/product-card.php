<?php

declare(strict_types=1);

/**
 * partials/product-card.php — um card do catálogo.
 *
 * Espera:
 *   $produto  array  item vindo de data/products.php
 *   $eager    bool   opcional. true carrega a imagem sem lazy (use nos primeiros cards).
 *
 * A busca e os filtros do catálogo leem os data-* deste elemento — não os remova.
 */

require_once __DIR__ . '/bootstrap.php';

/** @var array $produto */
$eager ??= false;

$faixa       = dd_faixa_principal($produto);
$linha       = dd_linha($produto);
$rotuloLinha = dd_rotulo_linha($linha);
$disponivel  = ($produto['disponivel'] ?? true) !== false;
$imagem      = dd_imagem($produto['imagem'] ?? null);
$url         = 'produto.php?slug=' . rawurlencode((string) $produto['slug']);
?>
<article
  class="card revelar group border border-base-300 bg-papel shadow-bandeja transition-shadow duration-300 hover:shadow-bandeja-alta<?= $disponivel ? '' : ' opacity-60' ?><?= !empty($produto['destaque']) ? ' border-t-4 border-t-brand' : '' ?>"
  data-produto
  data-categoria="<?= e($produto['categoria'] ?? '') ?>"
  data-linha="<?= e($linha) ?>"
  data-busca="<?= e(dd_indice_busca($produto)) ?>">

  <figure class="relative aspect-[4/3] overflow-hidden rounded-t-box">
    <?php if ($imagem): ?>
      <img src="<?= e($imagem) ?>"
           alt="<?= e($produto['nome']) ?>"
           class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.04]"
           <?= $eager ? '' : 'loading="lazy" decoding="async"' ?>
           width="640" height="480">
    <?php else: ?>
      <?php // INTEGRAÇÃO FUTURA: assim que o arquivo em 'imagem' existir, ele entra aqui sozinho. ?>
      <div class="massa flex h-full w-full flex-col items-center justify-center gap-2 text-crust/45" role="img"
           aria-label="Foto de <?= e($produto['nome']) ?> ainda não cadastrada">
        <?= dd_icone_categoria((string) ($produto['categoria'] ?? '')) ?>
        <span class="text-[0.7rem] font-semibold tracking-wide text-crust">foto em breve</span>
      </div>
    <?php endif; ?>

    <?php if ($rotuloLinha !== ''): ?>
      <span class="absolute left-3 top-3 rounded-full bg-papel/90 px-2.5 py-1 text-[0.7rem] font-bold text-crust backdrop-blur-sm">
        <?= e($rotuloLinha) ?>
      </span>
    <?php endif; ?>

    <?php if (!empty($produto['destaque'])): ?>
      <span class="absolute right-3 top-3 rounded-full bg-accent px-2.5 py-1 text-[0.7rem] font-bold text-accent-content shadow-sm">
        mais pedido
      </span>
    <?php endif; ?>
  </figure>

  <div class="card-body gap-2.5 p-5">
    <p class="text-xs font-semibold text-crust"><?= e($produto['categoria'] ?? '') ?></p>

    <h3 class="text-xl leading-tight">
      <a href="<?= e($url) ?>" class="rounded transition-colors hover:text-brand">
        <?= e($produto['nome']) ?>
      </a>
    </h3>

    <?php
    // O clamp fica no <span>, não no <p>: como .card-body é flex, o Chrome
    // "blockifica" display:-webkit-box no filho direto e o corte deixa de valer.
    ?>
    <p class="min-h-[2.85rem] text-sm leading-relaxed text-crust">
      <span class="duas-linhas"><?= e($produto['descricao'] ?? '') ?></span>
    </p>

    <!-- Preço: número grande, base pequena — como quadro de preço de balcão. -->
    <p class="mt-1 flex items-baseline gap-1.5">
      <span class="text-sm font-semibold text-crust">R$</span>
      <span class="fonte-display text-3xl leading-none text-brand"><?= e(number_format($faixa['valor'], 2, ',', '.')) ?></span>
      <span class="text-sm font-semibold text-crust">/ <?= e($faixa['porCurto']) ?></span>
    </p>

    <?php if ($faixa['temMinimo']): ?>
      <p class="w-fit rounded-full bg-base-200 px-2.5 py-1 text-xs font-semibold text-crust">
        a partir de <?= e((string) $faixa['min']) ?> un
      </p>
    <?php endif; ?>

    <?php // mt-auto alinha os botões na base, mesmo com descrições de tamanhos diferentes. ?>
    <div class="card-actions mt-auto items-center gap-2 pt-3">
      <?php if ($disponivel): ?>
        <button type="button"
                class="btn btn-primary flex-1 font-bold"
                data-add
                data-id="<?= e($produto['slug']) ?>"
                data-slug="<?= e($produto['slug']) ?>"
                data-nome="<?= e($produto['nome']) ?>"
                data-preco="<?= e(number_format($faixa['unitario'], 4, '.', '')) ?>"
                data-por="<?= e($faixa['exibicao']) ?>"
                data-min="<?= e((string) $faixa['min']) ?>"
                data-passo="<?= e((string) $faixa['passo']) ?>"
                data-base="<?= e((string) $faixa['base']) ?>"
                <?php // Miniatura do carrinho: a foto quando existir, senão o ícone da categoria. ?>
                data-imagem="<?= e((string) $imagem) ?>"
                data-categoria="<?= e($produto['categoria'] ?? '') ?>">
          Adicionar
        </button>
      <?php else: ?>
        <button type="button" class="btn btn-disabled flex-1" disabled>Indisponível hoje</button>
      <?php endif; ?>

      <a href="<?= e($url) ?>"
         class="btn btn-ghost border border-base-300 font-semibold text-crust hover:border-brand hover:text-brand">
        Detalhes<span class="sr-only"> de <?= e($produto['nome']) ?></span>
      </a>
    </div>
  </div>
</article>
