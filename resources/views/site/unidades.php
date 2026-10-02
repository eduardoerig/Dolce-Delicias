<?php

declare(strict_types=1);

/**
 * unidades.php — onde estão as lojas.
 *
 * Mesmo desenho de A empresa (cantos retos, fotos até a borda da tela). A
 * pergunta de quem chega é "qual fica perto, está aberta, como falo com
 * ela". Então a página responde nessa ordem:
 *
 *   1. a matriz em destaque — é ela que recebe o pedido feito pelo site;
 *   2. as outras lojas numa lista enxuta, uma linha cada: nome e bairro,
 *      endereço, horário e os contatos. Sem foto repetida, sem texto longo.
 *
 * O slug de cada unidade vira o id da linha, então dá para linkar direto:
 * /unidades#unidade-3 (a página do produto usa isso em "Onde encontrar").
 */

require_once DD_BASE . '/partials/bootstrap.php';

$unidades = dd_unidades();
$matrizes = array_values(array_filter($unidades, static fn (array $u): bool => !empty($u['matriz'])));
$lojas    = array_values(array_filter($unidades, static fn (array $u): bool => empty($u['matriz'])));

$tituloPagina    = 'Nossas unidades — Dolce Delícias';
$descricaoPagina = 'Endereço, horário e WhatsApp de cada loja da Dolce Delícias. O pedido feito pelo site é preparado pela matriz.';

include DD_BASE . '/partials/header.php';
?>

<div class="pagina-institucional bg-farinha">

  <?php // Herói: foto de fundo, camada escura e a regra que importa. ?>
  <?php ob_start(); ?>
    <h1 id="titulo-unidades" class="text-4xl sm:text-5xl lg:text-6xl">Nossas unidades</h1>
    <p class="mt-4 max-w-xl text-lg leading-relaxed lg:text-xl">
      <?= e((string) count($unidades)) ?> lojas. O pedido feito pelo site vai para a matriz;
      nas outras lojas você compra no balcão ou pelo WhatsApp de cada uma.
    </p>
    <div class="mt-7 flex flex-wrap gap-3">
      <?php foreach ($matrizes as $matrizAtalho): ?>
        <a href="#<?= e((string) $matrizAtalho['slug']) ?>" class="botao-primario">Ver a matriz</a>
      <?php endforeach; ?>
      <?php if ($lojas !== []): ?>
        <a href="#outras-lojas" class="botao-secundario">Outras lojas</a>
      <?php endif; ?>
    </div>
  <?php
  $heroiConteudo = (string) ob_get_clean();
  $heroiFoto     = 'unidades';
  $heroiTitulo   = 'titulo-unidades';
  $heroiCaminho  = 'Unidades';
  include DD_BASE . '/partials/heroi-foto.php';
  ?>

  <div>
    <?php foreach ($matrizes as $unidade): ?>
      <?php include DD_BASE . '/partials/unit-section.php'; ?>
    <?php endforeach; ?>
  </div>

  <?php if ($lojas !== []): ?>
    <section id="outras-lojas" class="secao-lojas mx-auto max-w-7xl px-4 sm:px-6 lg:px-8" aria-labelledby="titulo-lojas">
      <h2 id="titulo-lojas">Outras lojas</h2>
      <p class="mt-3 max-w-2xl text-lg leading-relaxed text-crust">Balcão e WhatsApp de cada loja. Cada uma tem o próprio catálogo.</p>
      <ul class="lojas mt-8">
        <?php foreach ($lojas as $unidade): ?>
          <?php include DD_BASE . '/partials/unit-section.php'; ?>
        <?php endforeach; ?>
      </ul>
    </section>
  <?php endif; ?>
</div>

<?php include DD_BASE . '/partials/footer.php'; ?>
