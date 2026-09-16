<?php

declare(strict_types=1);

/**
 * partials/product-card.php — um card do catálogo.
 *
 * Espera:
 *   $produto  array  item vindo de data/products.php
 *   $eager    bool   opcional. true carrega a imagem sem lazy (use nos primeiros cards).
 *
 * A busca do catálogo lê o data-busca deste elemento — não o remova.
 */

require_once __DIR__ . '/bootstrap.php';

/** @var array $produto */
$eager ??= false;

$faixa       = dd_faixa_principal($produto);
$disponivel  = ($produto['disponivel'] ?? true) !== false;
$imagem      = dd_imagem($produto['imagem'] ?? null);
$url         = 'produto.php?slug=' . rawurlencode((string) $produto['slug']);
?>
<article
  class="card revelar group border border-base-300 bg-papel shadow-bandeja transition-shadow duration-300 hover:shadow-bandeja-alta<?= $disponivel ? '' : ' opacity-60' ?>"
  data-produto
  data-busca="<?= e(dd_indice_busca($produto)) ?>">

  <figure class="relative aspect-[4/3] overflow-hidden rounded-t-box">
    <?php
    // A foto leva ao produto. No celular o "Detalhes" some e o título tem só
    // 20px de altura — alvo pequeno demais para dedo. A foto dá 166x125px.
    // tabindex/aria-hidden porque é link repetido: quem usa teclado ou leitor
    // de tela já chega lá pelo título. Mesmo padrão da miniatura do carrinho.
    ?>
    <a href="<?= e($url) ?>" class="block h-full w-full" tabindex="-1" aria-hidden="true">
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
    </a>

    <?php // Sem selo na foto: todos os cards do catálogo saem iguais. A linha
       // (encomenda x balcão) aparece no preço, como "/ cento" ou "/ un". ?>
  </figure>

  <div class="card-body gap-2 p-3 sm:gap-2.5 sm:p-5">
    <h3 class="text-sm leading-tight sm:text-xl">
      <a href="<?= e($url) ?>" class="rounded transition-colors hover:text-brand">
        <?= e($produto['nome']) ?>
      </a>
    </h3>

    <?php
    // O clamp fica no <span>, não no <p>: como .card-body é flex, o Chrome
    // "blockifica" display:-webkit-box no filho direto e o corte deixa de valer.
    //
    // Some no celular: com dois cards por linha sobram ~137px de texto útil, e
    // duas linhas de descrição aí viram ruído em cima do que importa (preço).
    // Quem quiser ler abre o produto — o título é link.
    ?>
    <p class="hidden min-h-[2.85rem] text-sm leading-relaxed text-crust sm:block">
      <span class="duas-linhas"><?= e($produto['descricao'] ?? '') ?></span>
    </p>

    <?php // Preço: número grande, base pequena — como quadro de preço de balcão.
       // O mínimo entra na mesma linha em vez de virar um selo à parte. ?>
    <p class="mt-1 flex flex-wrap items-baseline gap-x-1.5">
      <span class="text-xs font-semibold text-crust sm:text-sm">R$</span>
      <span class="fonte-display text-xl leading-none text-brand sm:text-3xl"><?= e(number_format($faixa['valor'], 2, ',', '.')) ?></span>
      <span class="whitespace-nowrap text-xs font-semibold text-crust sm:text-sm">/ <?= e($faixa['porCurto']) ?></span>
      <?php if ($faixa['temMinimo']): ?>
        <?php // O mínimo sai no celular: no card estreito ele empurrava o preço
           // para duas linhas. Continua na página do produto. ?>
        <span class="hidden text-xs text-crust sm:inline">· mín. <?= e((string) $faixa['min']) ?> un</span>
      <?php endif; ?>
    </p>

    <?php // mt-auto alinha os botões na base, mesmo com descrições de tamanhos diferentes. ?>
    <div class="card-actions mt-auto items-center gap-2 pt-2 sm:pt-3">
      <?php if ($disponivel): ?>
        <button type="button"
                class="btn btn-primary h-11 min-h-11 w-full text-xs font-bold sm:h-12 sm:min-h-12 sm:w-auto sm:flex-1 sm:text-sm"
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
        <button type="button" class="btn btn-disabled h-11 min-h-11 w-full text-xs sm:h-12 sm:min-h-12 sm:w-auto sm:flex-1 sm:text-sm" disabled>Indisponível hoje</button>
      <?php endif; ?>

      <?php // "Detalhes" só a partir de sm: no card de dois por linha não cabe
         // ao lado do "Adicionar", e o título do card já leva ao produto. ?>
      <a href="<?= e($url) ?>"
         class="btn btn-ghost hidden px-3 font-semibold text-crust hover:bg-transparent hover:text-brand sm:inline-flex">
        Detalhes<span class="sr-only"> de <?= e($produto['nome']) ?></span>
      </a>
    </div>
  </div>
</article>
