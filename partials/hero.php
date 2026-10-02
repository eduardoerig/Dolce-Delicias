<?php

declare(strict_types=1);

/**
 * partials/hero.php — o herói da home: "o cento chegando".
 *
 * Um prato com um cento de salgados (coxinhas, bolinhas, risoles e quibes)
 * entra rolando da esquerda, girando como uma roda, e para no fim do herói
 * com só metade à mostra: no celular apoiado no pé do herói, do tablet em
 * diante cortado pela borda direita. O texto fica sempre fora do prato.
 *
 * A posição final é CSS puro: sem JavaScript, ou para quem pediu menos
 * movimento, o prato já aparece parado no lugar. O movimento é do GSAP
 * (assets/js/heroi-cento.js): a entrada uma vez e, ao rolar a página, o
 * prato continua rodando para a direita.
 *
 * Embaixo do título: a frase, o botão vermelho e três garantias curtas. Logo
 * acima do prato, "Arraste para baixo" com uma seta: o herói ocupa a tela
 * inteira, e sem o aviso parece que a página acaba ali.
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

// O prato (assets/img/recortes/cento-<largura>.webp): o maior tem 985px.
$pratoLarguras = [400 => 400, 640 => 640, 1000 => 985];
$pratoSrc      = [];
foreach ($pratoLarguras as $arquivo => $largura) {
    $caminho = dd_imagem("/assets/img/recortes/cento-{$arquivo}.webp");
    if ($caminho) $pratoSrc[$largura] = $caminho;
}

$gsap          = dd_imagem('/assets/js/vendor/gsap.min.js');
$scrollTrigger = dd_imagem('/assets/js/vendor/ScrollTrigger.min.js');

$iconeGarantia = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg>';
?>
<?php if ($pratoSrc !== [] && $gsap && $scrollTrigger): ?>
  <?php
  // Esconde o prato e o texto antes do primeiro quadro, para o GSAP começar
  // do zero sem piscar. Se o script não rodar em 3s, tudo aparece no lugar.
  ?>
  <script>
    if (matchMedia('(prefers-reduced-motion: no-preference)').matches) {
      document.documentElement.classList.add('cento-anima');
      setTimeout(function () { document.documentElement.classList.remove('cento-anima'); }, 3000);
    }
  </script>
  <?php dd_scripts_gsap(); ?>
  <script type="module" src="/assets/js/heroi-cento.js"></script>
<?php endif; ?>

<section class="heroi-cento" aria-labelledby="heroi-titulo" data-heroi-cento>
  <?php if ($pratoSrc !== []): ?>
    <?php
    $srcset = implode(', ', array_map(static fn ($l, $c) => "{$c} {$l}w", array_keys($pratoSrc), $pratoSrc));
    $menor  = $pratoSrc[min(array_keys($pratoSrc))];
    ?>
    <div class="heroi-cento-prato" aria-hidden="true" data-cento-prato>
      <img src="<?= e($menor) ?>" srcset="<?= e($srcset) ?>" sizes="(min-width: 48rem) min(100svh, 64vw), 118vw" alt="" width="985" height="985" fetchpriority="high" decoding="async">
    </div>
  <?php endif; ?>

  <div class="heroi-cento-conteudo mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="heroi-cento-texto">
      <?php // leading abaixo de 1 é o que dá cara de cartaz: as linhas se tocam. ?>
      <h1 id="heroi-titulo" class="heroi-cento-titulo">
        <?php foreach ($heroLinhas as $linha): ?>
          <span class="block" data-cento-revela><?= e($linha['texto']) ?></span>
        <?php endforeach; ?>
      </h1>

      <p class="heroi-cento-frase" data-cento-revela><?= e($heroTexto) ?></p>

      <div class="mt-6 flex flex-wrap items-center gap-x-6 gap-y-3" data-cento-revela>
        <?php if ($heroCta !== null): ?>
          <a href="<?= e($heroCta['href']) ?>" class="botao-primario heroi-cta w-full sm:w-auto"><?= e($heroCta['texto']) ?></a>
        <?php endif; ?>
        <?php if ($heroLink !== null): ?>
          <a href="<?= e($heroLink['href']) ?>" class="hidden min-h-11 items-center font-semibold underline decoration-white/60 decoration-2 underline-offset-4 hover:decoration-white sm:inline-flex"><?= e($heroLink['texto']) ?></a>
        <?php endif; ?>
      </div>

      <?php if ($heroGarantias !== []): ?>
        <ul class="heroi-garantias" data-cento-revela>
          <?php foreach ($heroGarantias as $garantia): ?>
            <li><?= $iconeGarantia ?><?= e($garantia) ?></li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </div>

  <?php // No celular a pessoa arrasta; no computador ela rola. O link leva ao catálogo. ?>
  <a href="#catalogo" class="heroi-rolar" data-cento-revela>
    <span class="sm:hidden">Arraste para baixo</span>
    <span class="hidden sm:inline">Role para baixo</span>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 4v15"/><path d="m6 13 6 6 6-6"/></svg>
  </a>
</section>
