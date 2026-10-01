<?php

declare(strict_types=1);

/**
 * partials/promocoes.php — a faixa de ofertas da home.
 *
 * RF-14 (promoção de quarta) e RF-20 (baixa temporada) desenham igual: as duas
 * são "uma oferta com uma data". O que muda é só a agenda, e quem resolve isso
 * é dd_promocoes_visiveis() em partials/bootstrap.php — aqui não há nenhuma
 * regra de calendário.
 *
 * Nada cadastrado (ou tudo com 'ativo' => false) não desenha seção nenhuma: a
 * home não pode ter um buraco chamado "Promoções" com vazio dentro.
 *
 * A oferta que vale HOJE ganha selo e borda; as outras ficam em cartão neutro.
 * É a diferença entre "aproveite agora" e "programe-se" — e é ela que faz a
 * divulgação da quarta-feira funcionar na segunda.
 *
 * Espera:
 *   $promocoesEm  DateTimeInterface|null  opcional. A data que decide o que
 *                 aparece; o padrão é hoje. Existe para dar para conferir as
 *                 campanhas de julho e dezembro sem esperar julho e dezembro.
 */

require_once __DIR__ . '/bootstrap.php';

$promocoes = dd_promocoes_visiveis($promocoesEm ?? null);

if ($promocoes === []) {
    return;
}

$temVigente = (bool) array_filter($promocoes, static fn (array $p): bool => !empty($p['vigente']));
?>
<section id="promocoes" class="border-b border-base-300 bg-base-200">
  <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-14 lg:px-8">

    <div class="flex flex-wrap items-end justify-between gap-4">
      <div class="max-w-2xl">
        <h2 class="text-3xl sm:text-4xl">Promoções</h2>
        <p class="mt-3 text-lg leading-relaxed text-crust">
          <?= $temVigente
              ? 'Tem oferta valendo hoje. As outras ficam aqui para você se programar.'
              : 'As ofertas da semana e da temporada ficam aqui — dá para já ir programando a encomenda.' ?>
        </p>
      </div>
    </div>

    <ul class="mt-8 grid gap-4 sm:gap-5 md:grid-cols-2 lg:grid-cols-3">
      <?php foreach ($promocoes as $promocao): ?>
        <?php
        $vigente = !empty($promocao['vigente']);
        $selo    = trim((string) ($promocao['selo'] ?? ''));
        $agenda  = trim((string) ($promocao['agenda'] ?? ''));
        ?>
        <li class="revelar relative flex flex-col rounded-bandeja border bg-papel p-5 shadow-bandeja sm:p-6<?= $vigente ? ' border-brand border-t-4' : ' border-base-300' ?>">

          <?php if ($vigente): ?>
            <span class="absolute -top-3 left-5 rounded-full bg-accent px-3 py-1 text-xs font-bold text-accent-content shadow-sm">
              é hoje
            </span>
          <?php endif; ?>

          <?php if ($agenda !== ''): ?>
            <p class="text-xs font-bold uppercase tracking-[0.14em] text-crust<?= $vigente ? ' mt-2' : '' ?>">
              <?= e($agenda) ?>
            </p>
          <?php endif; ?>

          <h3 class="mt-2 text-2xl leading-tight"><?= e((string) ($promocao['titulo'] ?? '')) ?></h3>

          <?php if ($selo !== ''): ?>
            <p class="fonte-display mt-2 text-4xl leading-none text-brand"><?= e($selo) ?></p>
          <?php endif; ?>

          <p class="mt-3 flex-1 text-sm leading-relaxed text-crust">
            <?= e((string) ($promocao['texto'] ?? '')) ?>
          </p>

          <?php // Sem botão próprio: a promoção não é um produto no carrinho. O
             // caminho é o mesmo de sempre — escolher no catálogo e fechar com
             // a matriz, que é quem aplica o desconto na conversa. ?>
          <?php // min-h-11: é a ação principal do cartão, então tem alvo de
             // dedo inteiro, e não os 20px de uma linha de texto solta. ?>
          <a href="#catalogo" class="mt-3 inline-flex min-h-11 w-fit items-center gap-1.5 rounded py-2 text-sm font-bold text-brand underline decoration-brand/40 underline-offset-4 hover:decoration-brand">
            Ver o catálogo
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m13 6 6 6-6 6"/></svg>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>

    <p class="mt-6 text-xs leading-relaxed text-crust">
      O desconto é aplicado pela matriz no fechamento do pedido — o total que o site mostra é sempre o preço cheio.
    </p>
  </div>
</section>
