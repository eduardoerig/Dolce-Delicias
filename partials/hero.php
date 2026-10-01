<?php

declare(strict_types=1);

/**
 * partials/hero.php — o herói da home.
 *
 * A placa vermelha da marca, com os raios do logo, em duas metades: à
 * esquerda o que se faz aqui (título de cartaz, uma frase, um botão); à
 * direita o que a pessoa recebe — a bandeja do cento, desenhada enquanto não
 * há foto (partials/hero-bandeja.php). Embaixo, atalhos para as categorias:
 * quem já sabe o que quer entra comprando, sem rolar até o catálogo.
 *
 * O único movimento sem clique do site está aqui: as peças entram na bandeja
 * uma vez, no carregamento. Movimento reduzido mostra a bandeja pronta.
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
?>
<section class="heroi relative overflow-hidden bg-primary text-primary-content" aria-labelledby="heroi-titulo">
  <div class="raios pointer-events-none absolute inset-0 opacity-70" style="--raios-x:72%;--raios-y:46%" aria-hidden="true"></div>

  <div class="relative mx-auto grid max-w-7xl items-center gap-6 px-4 pb-8 pt-9 sm:px-6 lg:grid-cols-[minmax(0,1.05fr)_minmax(0,1fr)] lg:gap-10 lg:px-8 lg:pb-10 lg:pt-12">
    <div>
      <?php // leading abaixo de 1 é o que dá cara de cartaz: as linhas se tocam. ?>
      <h1 id="heroi-titulo" class="text-[clamp(2.25rem,5.6vw,4.6rem)] uppercase leading-[0.9]">
        <?php foreach ($heroLinhas as $linha): ?>
          <span class="block<?= !empty($linha['destaque']) ? ' text-accent' : '' ?>"><?= e($linha['texto']) ?></span>
        <?php endforeach; ?>
      </h1>

      <p class="mt-5 max-w-md text-lg leading-relaxed text-white"><?= e($heroTexto) ?></p>

      <div class="mt-7 flex flex-wrap items-center gap-x-6 gap-y-3">
        <?php if ($heroCta !== null): ?>
          <a href="<?= e($heroCta['href']) ?>" class="botao-amarelo heroi-cta"><?= e($heroCta['texto']) ?></a>
        <?php endif; ?>
        <?php if ($heroLink !== null): ?>
          <a href="<?= e($heroLink['href']) ?>" class="inline-flex min-h-11 items-center font-semibold text-white underline decoration-white/60 decoration-2 underline-offset-4 hover:decoration-white"><?= e($heroLink['texto']) ?></a>
        <?php endif; ?>
      </div>
    </div>

    <div class="heroi-arte mx-auto w-full max-w-[38rem] lg:-mr-4">
      <?php include __DIR__ . '/hero-bandeja.php'; ?>
    </div>
  </div>

  <?php if ($heroAtalhos !== []): ?>
    <nav class="relative border-t border-white/20" aria-label="Categorias do catálogo">
      <div class="mx-auto flex max-w-7xl flex-wrap items-center gap-2 px-4 py-4 sm:px-6 lg:px-8">
        <span class="mr-1 text-sm font-semibold text-white">Comece por</span>
        <?php foreach ($heroAtalhos as $categoriaAtalho): ?>
          <a href="/?categoria=<?= e(rawurlencode($categoriaAtalho)) ?>#catalogo" class="heroi-atalho"><?= e($categoriaAtalho) ?></a>
        <?php endforeach; ?>
      </div>
    </nav>
  <?php endif; ?>
</section>
