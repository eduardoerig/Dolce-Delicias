<?php

declare(strict_types=1);

/**
 * partials/hero.php — o herói de cartaz, no topo das páginas.
 *
 * A ideia: tipo gordo de madeira em escala de viewport fazendo todo o
 * trabalho, como cartaz de preço de feira. Sem split 50/50, sem par de
 * botões, sem pílula de selo.
 *
 * Nasceu na home e virou partial para unidades.php usar o mesmo desenho —
 * duas páginas com o mesmo herói significa um arquivo, não dois parecidos
 * que divergem na primeira mudança.
 *
 * Tipografia: nada de especial aqui. Alfa Slab One é a --font-display do
 * site, então o h1 já nasce com ela pela camada base.
 *
 * >>> INTEGRAÇÃO FUTURA — FOTO <<<
 * A faixa de baixo ($heroTira) é o lugar da foto. Hoje ela carrega uma tira
 * de nomes, que é elemento de design de verdade e não buraco. Quando houver
 * foto, troque o miolo da faixa por uma <img> com object-cover na mesma
 * altura — nada mais no bloco precisa mudar.
 *
 * Espera:
 *   $heroEtiqueta   string       linha pequena em caixa alta acima do título
 *   $heroLinhas     array        linhas do h1, cada uma
 *                                ['texto' => string, 'destaque' => bool]
 *                                'destaque' pinta de amarelo
 *   $heroTexto      string       parágrafo abaixo da régua
 *   $heroTextoLink  array|null   ['href','texto'] anexado ao parágrafo após " — "
 *   $heroCta        array|null   ['href','texto'] botão à direita da régua
 *   $heroTira       array        nomes da faixa de baixo; vazio esconde a faixa
 */

require_once __DIR__ . '/bootstrap.php';

$heroEtiqueta  = $heroEtiqueta  ?? '';
$heroLinhas    = $heroLinhas    ?? [];
$heroTexto     = $heroTexto     ?? '';
$heroTextoLink = $heroTextoLink ?? null;
$heroCta       = $heroCta       ?? null;
$heroTira      = $heroTira      ?? [];

// Sem faixa embaixo, o bloco precisa fechar com o próprio respiro.
$respiroDeBaixo = $heroTira === [] ? ' pb-10 lg:pb-14' : '';
?>
<section class="relative overflow-hidden bg-primary text-primary-content">
  <div class="raios pointer-events-none absolute inset-0 opacity-70" style="--raios-x:88%;--raios-y:14%" aria-hidden="true"></div>

  <div class="relative mx-auto max-w-7xl px-4 pt-10 sm:px-6 lg:px-8 lg:pt-14<?= $respiroDeBaixo ?>">

    <?php if ($heroEtiqueta !== ''): ?>
      <p class="text-xs font-bold uppercase tracking-[0.22em] text-white/75">
        <?= e($heroEtiqueta) ?>
      </p>
    <?php endif; ?>

    <?php // leading abaixo de 1 é o que dá cara de cartaz: as linhas se tocam. ?>
    <h1 class="mt-5 text-[clamp(2.1rem,9.5vw,8rem)] uppercase leading-[0.86]">
      <?php foreach ($heroLinhas as $linha): ?>
        <span class="block<?= !empty($linha['destaque']) ? ' text-accent' : '' ?>"><?= e($linha['texto']) ?></span>
      <?php endforeach; ?>
    </h1>

    <div class="mt-8 flex flex-col gap-5 border-t-2 border-white/25 pt-6 sm:flex-row sm:items-center sm:justify-between lg:mt-10">
      <p class="max-w-md text-base leading-relaxed text-white/90">
        <?= e($heroTexto) ?><?php if ($heroTextoLink !== null): ?> —
          <a href="<?= e($heroTextoLink['href']) ?>" class="font-semibold text-accent underline decoration-accent/50 underline-offset-4 hover:decoration-accent"><?= e($heroTextoLink['texto']) ?></a>.
        <?php endif; ?>
      </p>

      <?php if ($heroCta !== null): ?>
        <a href="<?= e($heroCta['href']) ?>"
           class="btn h-14 min-h-14 shrink-0 gap-2 border-none bg-accent px-8 text-base font-bold text-accent-content hover:bg-accent/85">
          <?= e($heroCta['texto']) ?>
          <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m13 6 6 6-6 6"/></svg>
        </a>
      <?php endif; ?>
    </div>
  </div>

  <?php if ($heroTira !== []): ?>
    <?php /* FAIXA — lugar da foto no futuro. Ver docblock. */ ?>
    <div class="relative mt-10 select-none overflow-hidden border-t-2 border-white/20 py-4" aria-hidden="true">
      <p class="fonte-display whitespace-nowrap text-[clamp(2rem,5vw,3.75rem)] uppercase leading-none text-white/20">
        <?php foreach ($heroTira as $nomeDaTira): ?><?= e($nomeDaTira) ?> &nbsp;·&nbsp; <?php endforeach; ?>
      </p>
    </div>
  <?php endif; ?>
</section>
