<?php

declare(strict_types=1);

/**
 * partials/hero.php — o herói da home.
 *
 * Foto de salgados de fundo com a camada vermelha da marca por cima
 * (partials/heroi-foto.php, tom 'marca'): título de cartaz, uma frase, o botão
 * e, embaixo, atalhos para as categorias — quem já sabe o que quer entra
 * comprando, sem rolar até o catálogo.
 *
 * Tipografia: Alfa Slab One é a --font-display do site, então o h1 já nasce
 * com ela pela camada base.
 *
 * Espera:
 *   $heroLinhas    array        linhas do h1, cada uma
 *                                ['texto' => string, 'destaque' => bool]
 *                                'destaque' pinta de amarelo
 *   $heroTexto     string       parágrafo abaixo do título
 *   $heroCta       array|null   ['href','texto'] botão principal
 *   $heroLink      array|null   ['href','texto'] link discreto ao lado do botão
 *   $heroAtalhos   string[]     categorias para os atalhos; vazio esconde a fila
 */

require_once __DIR__ . '/bootstrap.php';

$heroLinhas  = $heroLinhas  ?? [];
$heroTexto   = $heroTexto   ?? '';
$heroCta     = $heroCta     ?? null;
$heroLink    = $heroLink    ?? null;
$heroAtalhos = $heroAtalhos ?? [];

ob_start();
?>
  <?php // leading abaixo de 1 é o que dá cara de cartaz: as linhas se tocam. ?>
  <h1 id="heroi-titulo" class="max-w-3xl text-[clamp(2.25rem,6vw,4.6rem)] uppercase leading-[0.9]">
    <?php foreach ($heroLinhas as $linha): ?>
      <span class="block<?= !empty($linha['destaque']) ? ' text-accent' : '' ?>"><?= e($linha['texto']) ?></span>
    <?php endforeach; ?>
  </h1>

  <p class="mt-5 max-w-md text-lg leading-relaxed"><?= e($heroTexto) ?></p>

  <div class="mt-7 flex flex-wrap items-center gap-x-6 gap-y-3">
    <?php if ($heroCta !== null): ?>
      <a href="<?= e($heroCta['href']) ?>" class="botao-amarelo heroi-cta"><?= e($heroCta['texto']) ?></a>
    <?php endif; ?>
    <?php if ($heroLink !== null): ?>
      <a href="<?= e($heroLink['href']) ?>" class="inline-flex min-h-11 items-center font-semibold underline decoration-white/60 decoration-2 underline-offset-4 hover:decoration-white"><?= e($heroLink['texto']) ?></a>
    <?php endif; ?>
  </div>

  <?php if ($heroAtalhos !== []): ?>
    <nav class="mt-8 flex flex-wrap items-center gap-2 border-t border-white/25 pt-5" aria-label="Categorias do catálogo">
      <span class="mr-1 text-sm font-semibold">Comece por</span>
      <?php foreach ($heroAtalhos as $categoriaAtalho): ?>
        <a href="/?categoria=<?= e(rawurlencode($categoriaAtalho)) ?>#catalogo" class="heroi-atalho"><?= e($categoriaAtalho) ?></a>
      <?php endforeach; ?>
    </nav>
  <?php endif; ?>
<?php
$heroiConteudo = (string) ob_get_clean();
$heroiFoto     = 'inicio';
$heroiTitulo   = 'heroi-titulo';
$heroiTom      = 'marca';
include __DIR__ . '/heroi-foto.php';
