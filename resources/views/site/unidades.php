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

  <?php
  /*
   * Herói "balcão": a foto de uma loja (a vendedora arrumando os pães) ocupa
   * o herói inteiro; o texto e um atalho para cada loja ficam sobre o marrom
   * que sobe do lado esquerdo (no celular, a foto vem em cima e o texto
   * embaixo). A pessoa acha a loja dela já na primeira tela. A matriz vem
   * primeiro, marcada por receber os pedidos do site. Os links usam o slug,
   * que é o id de cada loja mais abaixo.
   */
  $todasLojas = array_merge($matrizes, $lojas);
  $nomeCurto  = static fn (array $u): string => trim(explode(' — ', (string) ($u['nome'] ?? ''), 2)[0]);
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

        <?php if ($todasLojas !== []): ?>
          <ul class="heroi-balcao-lojas" aria-label="Ir para uma loja">
            <?php foreach ($todasLojas as $lojaAtalho): ?>
              <li>
                <a href="#<?= e((string) $lojaAtalho['slug']) ?>" class="<?= !empty($lojaAtalho['matriz']) ? 'loja-atalho loja-atalho-matriz' : 'loja-atalho' ?>">
                  <?= e($nomeCurto($lojaAtalho)) ?>
                  <?php if (!empty($lojaAtalho['matriz'])): ?><small>recebe os pedidos do site</small><?php endif; ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </div>
  </section>

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
