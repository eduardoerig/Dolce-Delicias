<?php

declare(strict_types=1);

/**
 * partials/unit-section.php — uma loja inteira, em faixa larga, em unidades.php.
 *
 * Substitui o antigo card espremido da home: aqui cada unidade tem espaço para
 * texto, foto grande e todos os caminhos de contato.
 *
 * Espera:
 *   $unidade  array  item vindo de data/units.php
 *   $indice   int    posição na lista, só para alternar o lado da foto
 */

require_once __DIR__ . '/bootstrap.php';

/** @var array $unidade */
$indice = $indice ?? 0;

$ehMatriz = !empty($unidade['matriz']);
$foto     = dd_imagem($unidade['imagem'] ?? null);
$whatsapp = preg_replace('/\D+/', '', (string) ($unidade['whatsapp'] ?? ''));
$temZap   = $whatsapp !== '' && $whatsapp !== '55000000000';
$pdf      = (string) ($unidade['catalogoPdf'] ?? '');
$temPdf   = dd_tem_pdf($pdf);
$sobre    = trim((string) ($unidade['sobre'] ?? ''));

// Foto à direita nas faixas ímpares: quebra o ritmo de uma lista longa.
$fotoDireita = $indice % 2 === 1;

// Mensagem inicial do WhatsApp da unidade (sem carrinho — é o contato direto).
$msgUnidade = rawurlencode('Olá! Vim pelo site da Dolce Delícias e quero falar com a ' . ($unidade['nome'] ?? 'unidade') . '.');
?>
<section id="<?= e($unidade['slug']) ?>" class="revelar border-t border-base-300 py-8 sm:py-12 lg:py-16"
         aria-labelledby="titulo-<?= e($unidade['slug']) ?>">
  <div class="grid items-center gap-5 sm:gap-8 lg:grid-cols-2 lg:gap-14">

    <figure class="<?= $fotoDireita ? 'lg:order-2' : '' ?> relative overflow-hidden rounded-bandeja border border-base-300 shadow-bandeja">
      <?php if ($foto): ?>
        <img src="<?= e($foto) ?>" alt="Fachada da <?= e($unidade['nome']) ?>"
             class="aspect-[16/10] h-full w-full object-cover" loading="lazy" decoding="async" width="800" height="500">
      <?php else: ?>
        <?php // INTEGRAÇÃO FUTURA: foto da fachada — caminho já cadastrado em data/units.php. ?>
        <div class="massa flex aspect-[16/10] w-full items-center justify-center text-crust/45"
             role="img" aria-label="Foto da fachada da <?= e($unidade['nome']) ?> ainda não cadastrada">
          <svg class="h-20 w-20" viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M10 26 32 10l22 16"/><path d="M14 26v26h36V26"/><path d="M25 52V38h14v14"/>
          </svg>
        </div>
      <?php endif; ?>

      <?php if ($ehMatriz): ?>
        <span class="absolute left-4 top-4 rounded-full bg-primary px-3.5 py-1.5 text-xs font-bold text-primary-content shadow-sm">Matriz</span>
      <?php endif; ?>
    </figure>

    <div class="<?= $fotoDireita ? 'lg:order-1' : '' ?>">
      <h2 id="titulo-<?= e($unidade['slug']) ?>" class="text-xl sm:text-3xl"><?= e($unidade['nome']) ?></h2>

      <?php if ($sobre !== ''): ?>
        <p class="mt-3 max-w-prose leading-relaxed text-crust"><?= e($sobre) ?></p>
      <?php endif; ?>

      <address class="mt-5 space-y-2.5 not-italic leading-relaxed text-crust">
        <p class="flex gap-2.5">
          <svg class="mt-0.5 h-5 w-5 shrink-0 text-brand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
          <span><?= e($unidade['endereco']) ?></span>
        </p>
        <p class="flex gap-2.5">
          <svg class="mt-0.5 h-5 w-5 shrink-0 text-brand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
          <span><?= e($unidade['horario']) ?></span>
        </p>
      </address>

      <div class="mt-6 flex flex-wrap gap-2.5">
        <a href="https://wa.me/<?= e($whatsapp) ?>?text=<?= $msgUnidade ?>"
           target="_blank" rel="noopener noreferrer"
           class="btn btn-primary h-12 min-h-12 gap-2 px-5 font-bold">
          <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 11.5a8.4 8.4 0 0 1-12.4 7.4L3 20.5l1.7-5.4A8.5 8.5 0 1 1 21 11.5Z"/></svg>
          Falar no WhatsApp
        </a>

        <?php if ($temPdf): ?>
          <a href="<?= e($pdf) ?>" download
             class="btn h-12 min-h-12 gap-2 border border-base-300 bg-papel px-5 font-semibold text-crust hover:border-brand hover:text-brand">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v12"/><path d="m7 11 5 5 5-5"/><path d="M5 21h14"/></svg>
            Baixar catálogo
          </a>
        <?php else: ?>
          <?php // INTEGRAÇÃO FUTURA: coloque o PDF real em /catalogos/<slug>.pdf e o botão liga sozinho. ?>
          <span class="btn btn-disabled h-12 min-h-12 px-5" aria-disabled="true">Catálogo em breve</span>
        <?php endif; ?>
      </div>

      <?php if (!$temZap): ?>
        <p class="mt-3 text-xs text-crust">
          Número ainda não cadastrado — preencha em <code>data/units.php</code>.
        </p>
      <?php endif; ?>

      <?php // Não há "pedir por esta unidade": o carrinho do site fecha sempre
         // com a matriz. Esta loja atende por WhatsApp direto e tem o
         // catálogo dela em PDF, que são os dois botões acima. ?>
      <div class="mt-5 text-sm font-semibold">
        <a href="<?= e((string) ($unidade['mapaUrl'] ?? '#')) ?>" target="_blank" rel="noopener noreferrer"
           class="rounded text-crust underline decoration-base-300 underline-offset-4 hover:text-brand">
          Ver no mapa
        </a>
      </div>
    </div>
  </div>
</section>
