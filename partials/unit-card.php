<?php

declare(strict_types=1);

/**
 * partials/unit-card.php — uma loja na seção "Nossas unidades".
 *
 * Espera:
 *   $unidade  array  item vindo de data/units.php
 */

require_once __DIR__ . '/bootstrap.php';

/** @var array $unidade */
$ehMatriz  = !empty($unidade['matriz']);
$foto      = dd_imagem($unidade['imagem'] ?? null);
$whatsapp  = preg_replace('/\D+/', '', (string) ($unidade['whatsapp'] ?? ''));
$temZap    = $whatsapp !== '' && $whatsapp !== '55000000000';
$pdf       = (string) ($unidade['catalogoPdf'] ?? '');
$temPdf    = $pdf !== '' && is_file(DD_BASE . '/' . ltrim($pdf, '/'));

// Mensagem inicial do WhatsApp da unidade (sem carrinho — é o contato direto).
$msgUnidade = rawurlencode('Olá! Vim pelo site da Dolce Delícias e quero falar com a ' . ($unidade['nome'] ?? 'unidade') . '.');
?>
<article class="card revelar overflow-hidden border border-base-300 bg-papel shadow-bandeja<?= $ehMatriz ? ' ring-2 ring-brand' : '' ?>">

  <figure class="relative aspect-[16/10] overflow-hidden">
    <?php if ($foto): ?>
      <img src="<?= e($foto) ?>" alt="Fachada da <?= e($unidade['nome']) ?>"
           class="h-full w-full object-cover" loading="lazy" decoding="async" width="640" height="400">
    <?php else: ?>
      <?php // INTEGRAÇÃO FUTURA: foto da fachada — caminho já cadastrado em data/units.php. ?>
      <div class="massa flex h-full w-full items-center justify-center text-crust/45"
           role="img" aria-label="Foto da fachada da <?= e($unidade['nome']) ?> ainda não cadastrada">
        <svg class="h-14 w-14" viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M10 26 32 10l22 16"/><path d="M14 26v26h36V26"/><path d="M25 52V38h14v14"/>
        </svg>
      </div>
    <?php endif; ?>

    <?php if ($ehMatriz): ?>
      <span class="absolute left-3 top-3 rounded-full bg-primary px-3 py-1 text-xs font-bold text-primary-content shadow-sm">Matriz</span>
    <?php endif; ?>
  </figure>

  <div class="card-body gap-3 p-5">
    <h3 class="text-lg leading-tight"><?= e($unidade['nome']) ?></h3>

    <address class="space-y-2 text-sm not-italic leading-relaxed text-crust">
      <p class="flex gap-2">
        <svg class="mt-0.5 h-4 w-4 shrink-0 text-brand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
        <span><?= e($unidade['endereco']) ?></span>
      </p>
      <p class="flex gap-2">
        <svg class="mt-0.5 h-4 w-4 shrink-0 text-brand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
        <span><?= e($unidade['horario']) ?></span>
      </p>
    </address>

    <div class="mt-1 flex flex-wrap gap-2">
      <a href="https://wa.me/<?= e($whatsapp) ?>?text=<?= $msgUnidade ?>"
         target="_blank" rel="noopener noreferrer"
         class="btn btn-primary btn-sm flex-1 font-bold">
        WhatsApp
      </a>

      <?php if ($temPdf): ?>
        <a href="<?= e($pdf) ?>" download
           class="btn btn-sm flex-1 border border-base-300 bg-papel font-semibold text-crust hover:border-brand hover:text-brand">
          Baixar catálogo
        </a>
      <?php else: ?>
        <?php // INTEGRAÇÃO FUTURA: coloque o PDF real em /catalogos/<slug>.pdf e o botão liga sozinho. ?>
        <span class="btn btn-sm btn-disabled flex-1" aria-disabled="true">Catálogo em breve</span>
      <?php endif; ?>
    </div>

    <?php if (!$temZap): ?>
      <p class="text-xs text-crust">
        Número ainda não cadastrado — preencha em <code>data/units.php</code>.
      </p>
    <?php endif; ?>

    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 pt-1 text-xs font-semibold">
      <a href="<?= e((string) ($unidade['mapaUrl'] ?? '#')) ?>" target="_blank" rel="noopener noreferrer"
         class="rounded text-crust underline decoration-base-300 underline-offset-4 hover:text-brand">
        Ver no mapa
      </a>
      <button type="button" data-unit-option="<?= e($unidade['slug']) ?>"
              class="rounded text-crust underline decoration-base-300 underline-offset-4 hover:text-brand">
        Pedir por esta unidade
      </button>
    </div>
  </div>
</article>
