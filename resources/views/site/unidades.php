<?php

declare(strict_types=1);

/**
 * unidades.php — onde estão as lojas.
 *
 * Mesmo desenho de A empresa (cantos retos, fotos até a borda da tela). A
 * pergunta de quem chega é "qual fica perto, está aberta, como falo com
 * ela". Então a página responde nessa ordem:
 *
 *   1. a matriz em destaque, numa faixa marrom com a foto — é ela que recebe
 *      o pedido feito pelo site;
 *   2. as outras lojas em cartões: nome e bairro, endereço, horário e os
 *      contatos. Sem foto repetida, sem texto longo;
 *   3. um fecho para quem quer encomendar pelo site.
 *
 * O slug de cada unidade vira o id do bloco, então dá para linkar direto:
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

<div class="pagina-institucional pagina-unidades bg-farinha">

  <?php
  /*
   * Herói "balcão": a foto de uma loja (a vendedora arrumando os pães) ocupa
   * o herói; o texto fica sobre o marrom que sobe do lado esquerdo (no
   * celular, a foto vem em cima e o texto embaixo). Dois caminhos: a matriz,
   * que recebe os pedidos do site, e a lista das outras lojas.
   */
  $fotoLarga  = dd_imagem('/assets/img/heroi/loja-2400.jpg');
  $fotoMedia  = dd_imagem('/assets/img/heroi/loja-1600.jpg');
  $fotoCel    = dd_imagem('/assets/img/heroi/loja-celular-780.jpg');
  $fotoCel3x  = dd_imagem('/assets/img/heroi/loja-celular-1170.jpg');
  ?>
  <section class="heroi-balcao" aria-labelledby="titulo-unidades">
    <?php if ($fotoCel && $fotoMedia): ?>
      <picture class="heroi-balcao-foto">
        <source media="(min-width: 48rem)" srcset="<?= e($fotoMedia) ?> 1600w<?= $fotoLarga ? ', ' . e($fotoLarga) . ' 2400w' : '' ?>" sizes="100vw" width="2400" height="1600">
        <img src="<?= e($fotoCel) ?>" srcset="<?= e($fotoCel) ?> 780w<?= $fotoCel3x ? ', ' . e($fotoCel3x) . ' 1170w' : '' ?>" sizes="100vw" alt="" width="780" height="1100" fetchpriority="high" decoding="async">
      </picture>
    <?php endif; ?>

    <div class="heroi-balcao-conteudo mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="heroi-balcao-texto">
        <nav aria-label="Você está aqui" class="flex items-center gap-2 text-sm font-semibold">
          <a href="/" class="rounded underline-offset-4 hover:underline">Início</a>
          <span aria-hidden="true">/</span>
          <span aria-current="page">Unidades</span>
        </nav>
        <h1 id="titulo-unidades" class="heroi-balcao-titulo">Nossas unidades</h1>
        <p class="heroi-balcao-frase">
          <?= e((string) count($unidades)) ?> lojas. O pedido feito pelo site vai para a matriz;
          nas outras você compra no balcão ou pelo WhatsApp de cada uma.
        </p>

        <div class="heroi-balcao-botoes">
          <?php foreach ($matrizes as $matrizAtalho): ?>
            <a href="#<?= e((string) $matrizAtalho['slug']) ?>" class="botao-amarelo">Ver a matriz</a>
          <?php endforeach; ?>
          <?php if ($lojas !== []): ?>
            <a href="#outras-lojas" class="botao-vazado">Outras <?= e((string) count($lojas)) ?> lojas</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>

  <?php foreach ($matrizes as $unidade): ?>
    <?php include DD_BASE . '/partials/unit-section.php'; ?>
  <?php endforeach; ?>

  <?php if ($lojas !== []): ?>
    <section id="outras-lojas" class="lojas-secao mx-auto max-w-7xl px-4 sm:px-6 lg:px-8" aria-labelledby="titulo-lojas">
      <div class="lojas-secao-cabeca">
        <h2 id="titulo-lojas">Outras lojas</h2>
        <p>Balcão e WhatsApp de cada loja. Cada uma tem o próprio catálogo.</p>
        <?php $fachadaClasse = 'lojas-secao-fachada'; include DD_BASE . '/partials/ilustra-fachada.php'; ?>
      </div>
      <ul class="lojas-cartoes">
        <?php foreach ($lojas as $unidade): ?>
          <?php include DD_BASE . '/partials/unit-section.php'; ?>
        <?php endforeach; ?>
      </ul>
    </section>
  <?php endif; ?>

  <?php // Fecho: quem chegou até aqui pela loja pode fazer o pedido pelo site. ?>
  <section class="unidades-fecho" aria-labelledby="titulo-fecho">
    <div class="unidades-fecho-caixa mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="unidades-fecho-texto">
        <h2 id="titulo-fecho">Quer encomendar?</h2>
        <p>Monte o pedido no site e feche pelo WhatsApp da matriz, que prepara a encomenda.</p>
        <a href="/#catalogo" class="botao-amarelo">Ver o catálogo</a>
      </div>
    </div>
  </section>
</div>

<?php include DD_BASE . '/partials/footer.php'; ?>
