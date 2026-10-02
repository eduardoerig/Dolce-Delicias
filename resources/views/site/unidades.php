<?php

declare(strict_types=1);

/**
 * unidades.php — as lojas da rede, uma por card.
 *
 * Mesmo desenho do catálogo, do produto e do carrinho: faixa neutra com o
 * título, área clara com cards brancos. A matriz vem primeiro e ocupa a linha
 * inteira no computador, porque é ela que recebe os pedidos do site.
 *
 * O slug de cada unidade vira o id do card, então dá para linkar direto:
 * /unidades#unidade-3 (a página do produto usa isso em "Onde encontrar").
 */

require_once DD_BASE . '/partials/bootstrap.php';

$unidades = dd_unidades();

// Matriz primeiro — mesma ordem do menu de catálogos no topo.
usort($unidades, static fn (array $a, array $b): int => (int) !empty($b['matriz']) <=> (int) !empty($a['matriz']));

$tituloPagina    = 'Nossas unidades — Dolce Delícias';
$descricaoPagina = 'Endereço, horário e WhatsApp de cada loja da Dolce Delícias. Cada unidade tem o catálogo dela em PDF para baixar.';

include DD_BASE . '/partials/header.php';
?>

<div class="bg-farinha">

  <!-- Faixa: caminho, título, o que tem aqui e atalhos para cada loja -->
  <div class="faixa-pagina border-b border-linha bg-polvilho">
    <div class="mx-auto grid max-w-7xl items-center gap-8 px-4 py-8 sm:px-6 lg:grid-cols-[minmax(0,1fr)_20rem] lg:px-8">
      <div>
      <nav aria-label="Você está aqui" class="flex items-center gap-2 text-sm font-semibold text-crust">
        <a href="/" class="rounded hover:text-brand-escuro">Início</a>
        <span aria-hidden="true">/</span>
        <span class="text-base-content" aria-current="page">Unidades</span>
      </nav>
      <h1 class="mt-3 text-4xl sm:text-5xl">Nossas unidades</h1>
      <p class="mt-2 max-w-2xl text-crust">
        <?= e((string) count($unidades)) ?> lojas, cada uma com o catálogo dela em PDF e WhatsApp próprio.
        O pedido feito pelo site é fechado com a matriz.
      </p>

      <?php if (count($unidades) > 1): ?>
        <nav class="mt-5 flex flex-wrap gap-2" aria-label="Ir para uma unidade">
          <?php foreach ($unidades as $unidade): ?>
            <a href="#<?= e((string) $unidade['slug']) ?>" class="pilula pilula-link">
              <?= e(explode(' — ', (string) $unidade['nome'])[0]) ?>
            </a>
          <?php endforeach; ?>
        </nav>
      <?php endif; ?>
      </div>

      <?php // A frente de uma loja, desenhada: só no computador, onde sobra a lateral. ?>
      <?php $fachadaClasse = 'hidden w-full lg:block'; include DD_BASE . '/partials/ilustra-fachada.php'; ?>
    </div>
  </div>

  <!-- As unidades -->
  <div class="mx-auto grid max-w-7xl gap-5 px-4 py-8 sm:px-6 lg:grid-cols-2 lg:px-8 lg:py-10">
    <?php foreach ($unidades as $unidade): ?>
      <?php include DD_BASE . '/partials/unit-section.php'; ?>
    <?php endforeach; ?>
  </div>

  <!-- Fechamento: daqui, o caminho do pedido é o catálogo -->
  <section class="border-t border-linha bg-polvilho" aria-labelledby="titulo-pedido">
    <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-10 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
      <div>
        <h2 id="titulo-pedido" class="text-2xl sm:text-3xl">Quer fazer um pedido?</h2>
        <p class="mt-1 text-crust">Monte no catálogo e feche pelo WhatsApp da matriz.</p>
      </div>
      <a href="/#catalogo" class="botao-primario shrink-0">Ver catálogo</a>
    </div>
  </section>
</div>

<?php include DD_BASE . '/partials/footer.php'; ?>
