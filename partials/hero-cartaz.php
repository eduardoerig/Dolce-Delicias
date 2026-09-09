<?php

declare(strict_types=1);

/**
 * partials/hero-cartaz.php — o herói da home.
 *
 * A ideia: tipo gordo de madeira em escala de viewport fazendo todo o
 * trabalho, como cartaz de preço de feira. Sem split 50/50, sem par de
 * botões, sem pílula de selo — as três marcas de template que o herói
 * antigo carregava.
 *
 * Tipografia: nada de especial aqui. Alfa Slab One virou a --font-display
 * do site inteiro, então h1 já nasce com ela pela camada base.
 *
 * >>> INTEGRAÇÃO FUTURA — FOTO <<<
 * A faixa lá embaixo é o lugar da foto. Hoje ela carrega a tira de nomes
 * do cardápio, que é elemento de design de verdade e não buraco. Quando
 * houver foto, troque o miolo da faixa por uma <img> com object-cover na
 * mesma altura — nada mais no bloco precisa mudar.
 *
 * Espera:
 *   $unidades        array  de dd_unidades(), para contar
 *   $produtos        array  de dd_produtos(), para a tira de nomes
 *   $totalEncomenda  int    itens na linha de encomenda
 */

require_once __DIR__ . '/bootstrap.php';

/** @var array $unidades */
/** @var array $produtos */
/** @var int $totalEncomenda */

// Nomes para a tira de baixo. Duplicados para a faixa não ter fim visível.
$nomesTira = array_slice(array_column($produtos, 'nome'), 0, 8);
$tira      = array_merge($nomesTira, $nomesTira);
?>
<section class="relative overflow-hidden bg-primary text-primary-content">
  <div class="raios pointer-events-none absolute inset-0 opacity-70" style="--raios-x:88%;--raios-y:14%" aria-hidden="true"></div>

  <div class="relative mx-auto max-w-7xl px-4 pt-10 sm:px-6 lg:px-8 lg:pt-14">

    <p class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs font-bold uppercase tracking-[0.22em] text-white/75">
      <span>Padaria e confeitaria</span>
      <span aria-hidden="true">·</span>
      <span><?= e((string) count($unidades)) ?> unidades</span>
    </p>

    <?php // leading abaixo de 1 é o que dá cara de cartaz: as linhas se tocam. ?>
    <h1 class="mt-5 text-[clamp(2.1rem,9.5vw,8rem)] uppercase leading-[0.86]">
      Encomende<br>
      o cento.<br>
      <span class="text-accent">A gente cuida<br>do resto.</span>
    </h1>

    <div class="mt-8 flex flex-col gap-5 border-t-2 border-white/25 pt-6 sm:flex-row sm:items-center sm:justify-between lg:mt-10">
      <p class="max-w-md text-base leading-relaxed text-white/90">
        <?= e((string) $totalEncomenda) ?> itens de encomenda, feitos todo dia.
        Monte o pedido e feche no WhatsApp da loja mais perto —
        <a href="#encomendas" class="font-semibold text-accent underline decoration-accent/50 underline-offset-4 hover:decoration-accent">veja como funciona</a>.
      </p>

      <a href="#catalogo"
         class="btn h-14 min-h-14 shrink-0 gap-2 border-none bg-accent px-8 text-base font-bold text-accent-content hover:bg-accent/85">
        Ver catálogo
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m13 6 6 6-6 6"/></svg>
      </a>
    </div>
  </div>

  <?php /* FAIXA — lugar da foto no futuro. Ver docblock. */ ?>
  <div class="relative mt-10 select-none overflow-hidden border-t-2 border-white/20 py-4" aria-hidden="true">
    <p class="fonte-display whitespace-nowrap text-[clamp(2rem,5vw,3.75rem)] uppercase leading-none text-white/20">
      <?php foreach ($tira as $nome): ?><?= e($nome) ?> &nbsp;·&nbsp; <?php endforeach; ?>
    </p>
  </div>
</section>
