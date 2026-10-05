<?php

declare(strict_types=1);

/**
 * unidades.php — onde estão as lojas.
 *
 * Mesmo desenho de A empresa (cantos redondos, faixas com curvas). A
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

<?php
/*
 * Desenho arredondado, o mesmo de A empresa: abertura com a foto da loja de
 * fundo (escurecida onde fica o texto) e curva no pé, a matriz numa caixa
 * marrom de cantos redondos, as outras lojas em cartões numa faixa clara
 * entre curvas e o convite final numa caixa vermelha. Botões em pílula.
 */
$fotoLoja    = dd_imagem('/assets/img/heroi/loja-1600.webp');
$fotoLojaCel = dd_imagem('/assets/img/heroi/loja-celular-780.webp');
$fotoLojaCel3x = dd_imagem('/assets/img/heroi/loja-celular-1170.webp');

// WhatsApp da matriz para o convite final. Número vazio ou de exemplo não
// vira botão — ver dd_whatsapp().
$zapMatriz = dd_whatsapp(dd_matriz());
$linkZap   = $zapMatriz !== ''
    ? 'https://wa.me/' . $zapMatriz . '?text=' . rawurlencode('Olá! Vim pelo site da Dolce Delícias e quero fazer um pedido.')
    : '';
$iconeZap = '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 11.5a8.4 8.4 0 0 1-12.4 7.4L3 20.5l1.7-5.4A8.5 8.5 0 1 1 21 11.5Z"/></svg>';
?>

<div class="pagina-unidades bg-farinha">

  <section class="abertura abertura-com-foto" aria-labelledby="titulo-unidades">
    <?php // A foto da loja é o fundo: no celular a em pé, do computador em diante a deitada. O marrom escurece o lado do texto. ?>
    <?php if ($fotoLoja && $fotoLojaCel): ?>
      <picture class="abertura-fundo">
        <source media="(min-width: 48rem)" srcset="<?= e($fotoLoja) ?> 1600w" sizes="100vw" width="1600" height="1067">
        <img src="<?= e($fotoLojaCel) ?>" srcset="<?= e($fotoLojaCel) ?> 780w<?= $fotoLojaCel3x ? ', ' . e($fotoLojaCel3x) . ' 1170w' : '' ?>" sizes="100vw" alt="" width="780" height="1100" fetchpriority="high" decoding="async">
      </picture>
    <?php endif; ?>
    <div class="abertura-grade mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="abertura-texto">
        <nav aria-label="Você está aqui" class="flex items-center gap-2 text-sm font-semibold">
          <a href="/" class="rounded underline-offset-4 hover:underline">Início</a>
          <span aria-hidden="true">/</span>
          <span aria-current="page">Unidades</span>
        </nav>
        <h1 id="titulo-unidades" class="abertura-titulo">Nossas unidades</h1>
        <p class="abertura-frase">
          <?= e((string) count($unidades)) ?> lojas. O pedido feito pelo site vai para a matriz;
          nas outras você compra no balcão ou pelo WhatsApp de cada uma.
        </p>

        <div class="botoes-pilula">
          <?php foreach ($matrizes as $matrizAtalho): ?>
            <a href="#<?= e((string) $matrizAtalho['slug']) ?>" class="botao-pilula botao-pilula-amarelo">Ver a matriz</a>
          <?php endforeach; ?>
          <?php if ($lojas !== []): ?>
            <a href="#outras-lojas" class="botao-pilula botao-pilula-claro">Outras <?= e((string) count($lojas)) ?> lojas</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>

  <?php foreach ($matrizes as $unidade): ?>
    <?php include DD_BASE . '/partials/unit-section.php'; ?>
  <?php endforeach; ?>

  <?php if ($lojas !== []): ?>
    <div class="faixa-curva">
      <section id="outras-lojas" class="lojas-secao mx-auto max-w-7xl px-4 sm:px-6 lg:px-8" aria-labelledby="titulo-lojas">
        <div class="centro">
          <p class="rotulo-secao">Perto de você</p>
          <h2 id="titulo-lojas">Outras lojas</h2>
          <p class="centro-frase">Balcão e WhatsApp de cada loja. Cada uma tem o próprio catálogo.</p>
        </div>
        <ul class="lojas-cartoes">
          <?php foreach ($lojas as $unidade): ?>
            <?php include DD_BASE . '/partials/unit-section.php'; ?>
          <?php endforeach; ?>
        </ul>
      </section>
    </div>
  <?php endif; ?>

  <?php // Convite final: quem chegou até aqui pela loja pode fazer o pedido pelo site. ?>
  <section class="convite mx-auto max-w-7xl px-4 sm:px-6 lg:px-8" aria-labelledby="titulo-convite">
    <div class="convite-caixa">
      <h2 id="titulo-convite">Quer encomendar?</h2>
      <p>Monte o pedido no site e feche pelo WhatsApp da matriz, que prepara a encomenda.</p>
      <div class="botoes-pilula">
        <a href="/#catalogo" class="botao-pilula botao-pilula-amarelo">Ver o catálogo</a>
        <?php if ($linkZap !== ''): ?>
          <a href="<?= e($linkZap) ?>" target="_blank" rel="noopener noreferrer" class="botao-pilula botao-pilula-claro"><?= $iconeZap ?>Pedir no WhatsApp</a>
        <?php endif; ?>
      </div>
    </div>
  </section>
</div>

<?php include DD_BASE . '/partials/footer.php'; ?>
