<?php

declare(strict_types=1);

/**
 * partials/hero.php — o herói da home: "a vitrine".
 *
 * A comida aparece viva, sem filtro de cor: o escuro só existe onde há texto
 * (embaixo no celular, à esquerda do tablet em diante; partials/heroi-foto.php,
 * tom 'vitrine'). Por cima da foto, a única ousadia da página: um selo de
 * preço de padaria, amarelo, com o menor valor de um cento no catálogo. Ele
 * entra uma vez, carimbado; movimento reduzido mostra o selo parado.
 *
 * Embaixo do título: a frase, o botão vermelho (a cor de ação do site), três
 * garantias curtas e os atalhos de categoria numa linha só, que no celular
 * rola de lado em vez de empilhar.
 *
 * Tipografia: Alfa Slab One é a --font-display do site, então o h1 já nasce
 * com ela pela camada base.
 *
 * Espera:
 *   $heroLinhas      array        linhas do h1, cada uma ['texto' => string]
 *   $heroTexto       string       frase abaixo do título
 *   $heroCta         array|null   ['href','texto'] botão principal
 *   $heroLink        array|null   ['href','texto'] link discreto ao lado do botão
 *   $heroAtalhos     string[]     categorias para os atalhos; vazio esconde a fila
 *   $heroPrecoCento  float|null   menor preço de um cento; null esconde o selo
 *   $heroGarantias   string[]     frases curtas sob o botão
 */

require_once __DIR__ . '/bootstrap.php';

$heroLinhas     = $heroLinhas     ?? [];
$heroTexto      = $heroTexto      ?? '';
$heroCta        = $heroCta        ?? null;
$heroLink       = $heroLink       ?? null;
$heroAtalhos    = $heroAtalhos    ?? [];
$heroPrecoCento = $heroPrecoCento ?? null;
$heroGarantias  = $heroGarantias  ?? [];

// "R$ 85,00" vira "R$ 85"; centavos só aparecem quando existem.
$heroPrecoTexto = $heroPrecoCento !== null
    ? preg_replace('/,00$/', '', dd_moeda((float) $heroPrecoCento))
    : '';

$iconeGarantia = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg>';

ob_start();
?>
  <?php if ($heroPrecoTexto !== ''): ?>
    <p class="selo-cento">
      <span class="selo-cento-miolo">
        <span class="selo-cento-rotulo">a partir de</span>
        <span class="selo-cento-valor"><?= e($heroPrecoTexto) ?></span>
        <span class="selo-cento-rotulo">o cento</span>
      </span>
    </p>
  <?php endif; ?>

  <div class="heroi-vitrine-texto">
    <?php // leading abaixo de 1 é o que dá cara de cartaz: as linhas se tocam. ?>
    <h1 id="heroi-titulo" class="text-[clamp(2.25rem,5.4vw,4.25rem)] uppercase leading-[0.92]">
      <?php foreach ($heroLinhas as $linha): ?>
        <span class="block"><?= e($linha['texto']) ?></span>
      <?php endforeach; ?>
    </h1>

    <p class="mt-4 max-w-lg text-lg leading-relaxed sm:text-xl"><?= e($heroTexto) ?></p>

    <div class="mt-6 flex flex-wrap items-center gap-x-6 gap-y-3">
      <?php if ($heroCta !== null): ?>
        <a href="<?= e($heroCta['href']) ?>" class="botao-primario heroi-cta"><?= e($heroCta['texto']) ?></a>
      <?php endif; ?>
      <?php if ($heroLink !== null): ?>
        <a href="<?= e($heroLink['href']) ?>" class="inline-flex min-h-11 items-center font-semibold underline decoration-white/60 decoration-2 underline-offset-4 hover:decoration-white"><?= e($heroLink['texto']) ?></a>
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

  <?php if ($heroAtalhos !== []): ?>
    <nav class="heroi-categorias" aria-label="Categorias do catálogo">
      <span class="shrink-0 text-sm font-semibold">Comece por</span>
      <?php foreach ($heroAtalhos as $categoriaAtalho): ?>
        <a href="/?categoria=<?= e(rawurlencode($categoriaAtalho)) ?>#catalogo" class="heroi-atalho"><?= e($categoriaAtalho) ?></a>
      <?php endforeach; ?>
    </nav>
  <?php endif; ?>
<?php
$heroiConteudo = (string) ob_get_clean();
$heroiFoto     = 'inicio';
$heroiTitulo   = 'heroi-titulo';
$heroiTom      = 'vitrine';
include __DIR__ . '/heroi-foto.php';
