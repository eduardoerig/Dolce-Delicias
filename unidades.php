<?php

declare(strict_types=1);

/**
 * unidades.php — uma seção por loja.
 *
 * Saiu da home porque unidade é conteúdo de verdade: endereço, horário, o que
 * cada uma faz e o catálogo próprio (cada loja vende alguns itens diferentes).
 *
 * O slug de cada unidade vira o id da seção, então dá para linkar direto:
 * unidades.php#unidade-3.
 */

require_once __DIR__ . '/partials/bootstrap.php';

$unidades = dd_unidades();

// Matriz primeiro — mesma ordem do menu de catálogos no topo.
usort($unidades, static fn (array $a, array $b): int => (int) !empty($b['matriz']) <=> (int) !empty($a['matriz']));

$tituloPagina    = 'Nossas unidades — Dolce Delícias';
$descricaoPagina = 'Endereço, horário e WhatsApp de cada loja da Dolce Delícias. Cada unidade tem o catálogo dela em PDF para baixar.';

include __DIR__ . '/partials/header.php';
?>

<!-- ============================================================
     HERÓI — mesmo desenho da home, via partials/hero.php
     ============================================================ -->
<?php
$heroEtiqueta  = 'Nossas lojas · ' . count($unidades) . ' unidades';
$heroLinhas    = [
    ['texto' => 'Toda loja'],
    ['texto' => 'tem a dela.', 'destaque' => true],
];
$heroTexto     = 'Cada unidade vende alguns itens diferentes, então cada uma tem o catálogo dela. Baixe o da sua loja ou fale com ela direto no WhatsApp';
$heroTextoLink = ['href' => '#' . ($unidades[0]['slug'] ?? 'matriz'), 'texto' => 'comece pela matriz'];
// Sem faixa e sem botão: os atalhos em pastilha logo abaixo já fazem esse papel.
$heroCta       = null;
$heroTira      = [];

include __DIR__ . '/partials/hero.php';
?>

<div class="faixa-raios" aria-hidden="true"></div>

<!-- ============================================================
     ATALHOS — a lista é longa, então dá para pular direto
     ============================================================ -->
<nav class="border-b border-base-300 bg-base-200" aria-label="Ir para uma unidade">
  <div class="sem-barra mx-auto flex max-w-7xl gap-2 overflow-x-auto px-4 py-4 sm:px-6 lg:px-8">
    <?php foreach ($unidades as $unidade): ?>
      <a href="#<?= e($unidade['slug']) ?>" class="chip">
        <?php if (!empty($unidade['matriz'])): ?>
          <svg class="h-4 w-4 shrink-0 text-brand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 11 9-7 9 7"/><path d="M6 11v9h12v-9"/></svg>
        <?php endif; ?>
        <?= e(explode(' — ', (string) $unidade['nome'])[0]) ?>
      </a>
    <?php endforeach; ?>
  </div>
</nav>

<!-- ============================================================
     AS UNIDADES
     ============================================================ -->
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
  <?php foreach ($unidades as $indice => $unidade): ?>
    <?php include __DIR__ . '/partials/unit-section.php'; ?>
  <?php endforeach; ?>
</div>

<!-- ============================================================
     FECHAMENTO
     ============================================================ -->
<section class="border-t border-base-300 bg-base-200">
  <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-14 lg:px-8">
    <div class="flex flex-col gap-5 rounded-bandeja bg-neutral p-7 text-neutral-content sm:flex-row sm:items-center sm:p-9">
      <div class="flex-1">
        <h2 class="text-2xl">Não sabe qual escolher?</h2>
        <p class="mt-2 max-w-xl leading-relaxed opacity-85">
          O pedido pelo site é fechado com a matriz. Estas páginas servem para você ver
          o catálogo de cada loja e falar direto com ela quando precisar.
        </p>
      </div>
      <a href="index.php#catalogo" class="btn h-12 min-h-12 shrink-0 border-none bg-accent px-6 font-bold text-accent-content hover:bg-accent/85">
        Ver catálogo
      </a>
    </div>
  </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
