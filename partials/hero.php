<?php

declare(strict_types=1);

/**
 * partials/hero.php — o herói da home: "a vitrine".
 *
 * A comida aparece viva, sem filtro de cor: o escuro só existe onde há texto
 * (embaixo no celular, à esquerda do tablet em diante; partials/heroi-foto.php,
 * tom 'vitrine').
 *
 * Embaixo do título: a frase, o botão vermelho (a cor de ação do site) e três
 * garantias curtas. No pé, "Arraste para baixo" com uma seta: o herói ocupa a
 * tela inteira, e sem o aviso parece que a página acaba ali. A seta quica
 * devagar; movimento reduzido mostra a seta parada.
 *
 * Tipografia: Alfa Slab One é a --font-display do site, então o h1 já nasce
 * com ela pela camada base.
 *
 * Espera:
 *   $heroLinhas      array        linhas do h1, cada uma ['texto' => string]
 *   $heroTexto       string       frase abaixo do título
 *   $heroCta         array|null   ['href','texto'] botão principal
 *   $heroLink        array|null   ['href','texto'] link discreto ao lado do botão
 *   $heroGarantias   string[]     frases curtas sob o botão
 */

require_once __DIR__ . '/bootstrap.php';

$heroLinhas    = $heroLinhas    ?? [];
$heroTexto     = $heroTexto     ?? '';
$heroCta       = $heroCta       ?? null;
$heroLink      = $heroLink      ?? null;
$heroGarantias = $heroGarantias ?? [];

$iconeGarantia = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg>';

ob_start();
?>
  <div class="heroi-vitrine-texto">
    <?php // leading abaixo de 1 é o que dá cara de cartaz: as linhas se tocam. ?>
    <h1 id="heroi-titulo" class="text-[clamp(2.5rem,12vw,5.5rem)] uppercase leading-[0.92]">
      <?php foreach ($heroLinhas as $linha): ?>
        <span class="block"><?= e($linha['texto']) ?></span>
      <?php endforeach; ?>
    </h1>

    <p class="mt-3 max-w-lg text-[1.0625rem] leading-relaxed sm:mt-4 sm:text-xl"><?= e($heroTexto) ?></p>

    <div class="mt-6 flex flex-wrap items-center gap-x-6 gap-y-3">
      <?php if ($heroCta !== null): ?>
        <a href="<?= e($heroCta['href']) ?>" class="botao-primario heroi-cta w-full sm:w-auto"><?= e($heroCta['texto']) ?></a>
      <?php endif; ?>
      <?php if ($heroLink !== null): ?>
        <a href="<?= e($heroLink['href']) ?>" class="hidden min-h-11 items-center font-semibold underline decoration-white/60 decoration-2 underline-offset-4 hover:decoration-white sm:inline-flex"><?= e($heroLink['texto']) ?></a>
      <?php endif; ?>
    </div>

    <?php if ($heroGarantias !== []): ?>
      <ul class="heroi-garantias">
        <?php foreach ($heroGarantias as $garantia): ?>
          <li><?= $iconeGarantia ?><?= e($garantia) ?></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>

  <?php // No celular a pessoa arrasta; no computador ela rola. O link leva ao catálogo. ?>
  <a href="#catalogo" class="heroi-rolar">
    <span class="sm:hidden">Arraste para baixo</span>
    <span class="hidden sm:inline">Role para baixo</span>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 4v15"/><path d="m6 13 6 6 6-6"/></svg>
  </a>
<?php
$heroiConteudo = (string) ob_get_clean();
$heroiFoto     = 'inicio';
$heroiTitulo   = 'heroi-titulo';
$heroiTom      = 'vitrine';
include __DIR__ . '/heroi-foto.php';
