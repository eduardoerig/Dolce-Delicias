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
     ABERTURA
     ============================================================ -->
<section class="relative overflow-hidden bg-primary text-primary-content">
  <div class="raios pointer-events-none absolute inset-0" style="--raios-x:82%;--raios-y:30%" aria-hidden="true"></div>

  <div class="relative mx-auto max-w-7xl px-4 py-9 sm:px-6 sm:py-12 lg:px-8 lg:py-16">
    <h1 class="max-w-2xl text-4xl leading-[1.05] sm:text-5xl">
      Nossas unidades.<br>
      <span class="text-accent">Escolha a sua.</span>
    </h1>
    <p class="mt-5 max-w-xl text-lg leading-relaxed text-white">
      São <?= e((string) count($unidades)) ?> lojas. Cada uma tem o catálogo dela — alguns itens mudam de
      uma para outra. Fale direto com quem vai preparar o seu pedido.
    </p>
  </div>
</section>

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
          Monte o pedido no catálogo e decida a loja depois — dá para trocar a qualquer
          momento aqui nesta página, antes de fechar no WhatsApp.
        </p>
      </div>
      <a href="index.php#catalogo" class="btn h-12 min-h-12 shrink-0 border-none bg-accent px-6 font-bold text-accent-content hover:bg-accent/85">
        Ver catálogo
      </a>
    </div>
  </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
