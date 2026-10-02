<?php

declare(strict_types=1);

/**
 * unidades.php — onde estão as lojas.
 *
 * A pergunta de quem chega é "qual fica perto, está aberta, como falo com
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

<div class="bg-farinha">

  <!-- Faixa: caminho, título e a regra que importa -->
  <div class="border-b border-linha bg-polvilho">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-10">
      <nav aria-label="Você está aqui" class="flex items-center gap-2 text-sm font-semibold text-crust">
        <a href="/" class="rounded hover:text-brand-escuro">Início</a>
        <span aria-hidden="true">/</span>
        <span class="text-base-content" aria-current="page">Unidades</span>
      </nav>
      <h1 class="mt-3 text-4xl sm:text-5xl">Nossas unidades</h1>
      <p class="mt-3 max-w-2xl text-lg leading-relaxed text-crust">
        <?= e((string) count($unidades)) ?> lojas. O pedido feito pelo site vai para a matriz;
        nas outras lojas você compra no balcão ou pelo WhatsApp de cada uma.
      </p>
    </div>
  </div>

  <div class="mx-auto grid max-w-7xl gap-12 px-4 py-10 sm:px-6 lg:px-8 lg:py-14">
    <?php foreach ($matrizes as $unidade): ?>
      <?php include DD_BASE . '/partials/unit-section.php'; ?>
    <?php endforeach; ?>

    <?php if ($lojas !== []): ?>
      <section aria-labelledby="titulo-lojas">
        <div class="flex flex-wrap items-baseline justify-between gap-x-6 gap-y-1">
          <h2 id="titulo-lojas" class="text-2xl sm:text-3xl">Outras lojas</h2>
          <p class="text-sm text-crust">Balcão e WhatsApp de cada loja. Cada uma tem o próprio catálogo.</p>
        </div>
        <ul class="lojas mt-5">
          <?php foreach ($lojas as $unidade): ?>
            <?php include DD_BASE . '/partials/unit-section.php'; ?>
          <?php endforeach; ?>
        </ul>
      </section>
    <?php endif; ?>
  </div>
</div>

<?php include DD_BASE . '/partials/footer.php'; ?>
