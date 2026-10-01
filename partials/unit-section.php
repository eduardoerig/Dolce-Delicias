<?php

declare(strict_types=1);

/**
 * partials/unit-section.php — uma loja em card, em unidades.php.
 *
 * Foto, nome, endereço, horário, prazo e os caminhos de contato (WhatsApp,
 * catálogo em PDF, mapa). A matriz ganha o card largo (.unidade-matriz).
 *
 * Espera:
 *   $unidade  array  item vindo de dd_unidades()
 */

require_once __DIR__ . '/bootstrap.php';

/** @var array $unidade */
$ehMatriz = !empty($unidade['matriz']);
$foto     = dd_imagem($unidade['imagem'] ?? null);
$whatsapp = preg_replace('/\D+/', '', (string) ($unidade['whatsapp'] ?? ''));
// Número vazio ou de exemplo (ex.: 55000000000) não vira botão: seria um link para ninguém.
$temZap   = preg_match('/^\d{10,15}$/D', $whatsapp) === 1 && preg_match('/^\d{0,3}0{8,}$/D', $whatsapp) !== 1;
$pdf      = (string) ($unidade['catalogoPdf'] ?? '');
$temPdf   = dd_tem_pdf($pdf);
$mapa     = trim((string) ($unidade['mapaUrl'] ?? ''));
$endereco = trim((string) ($unidade['endereco'] ?? ''));
$horario  = trim((string) ($unidade['horario'] ?? ''));
$sobre    = trim((string) ($unidade['sobre'] ?? ''));
$preparo  = trim((string) ($unidade['preparo'] ?? '')); // RF-30
$canais   = array_values(array_filter(                  // RF-26
    (array) ($unidade['canais'] ?? []),
    static fn ($canal): bool => is_array($canal) && trim((string) ($canal['url'] ?? '')) !== ''
));

// Mensagem inicial do WhatsApp da unidade (sem carrinho — é o contato direto).
$msgUnidade = rawurlencode('Olá! Vim pelo site da Dolce Delícias e quero falar com a ' . ($unidade['nome'] ?? 'unidade') . '.');
?>
<article id="<?= e((string) $unidade['slug']) ?>" class="unidade-card<?= $ehMatriz ? ' unidade-matriz' : '' ?>"
         aria-labelledby="titulo-<?= e((string) $unidade['slug']) ?>">

  <figure class="unidade-foto">
    <?php if ($foto): ?>
      <img src="<?= e($foto) ?>" alt="Fachada da <?= e((string) $unidade['nome']) ?>"
           loading="lazy" decoding="async" width="800" height="450">
    <?php else: ?>
      <?php // INTEGRAÇÃO FUTURA: foto da fachada, cadastrada no painel em Unidades. ?>
      <span class="unidade-sem-foto" role="img" aria-label="Foto da fachada da <?= e((string) $unidade['nome']) ?> ainda não cadastrada">
        <svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M10 26 32 10l22 16"/><path d="M14 26v26h36V26"/><path d="M25 52V38h14v14"/>
        </svg>
      </span>
    <?php endif; ?>
  </figure>

  <div class="unidade-corpo">
    <?php if ($ehMatriz): ?>
      <p class="flex flex-wrap items-center gap-2 text-sm text-crust">
        <span class="pilula">Matriz</span>
        Os pedidos do site vão para esta loja.
      </p>
    <?php endif; ?>

    <h2 id="titulo-<?= e((string) $unidade['slug']) ?>" class="unidade-nome"><?= e((string) $unidade['nome']) ?></h2>

    <?php if ($sobre !== ''): ?>
      <p class="max-w-prose leading-relaxed text-crust"><?= e($sobre) ?></p>
    <?php endif; ?>

    <ul class="unidade-info">
      <?php if ($endereco !== ''): ?>
        <li>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
          <span><span class="sr-only">Endereço: </span><?= e($endereco) ?></span>
        </li>
      <?php endif; ?>
      <?php if ($horario !== ''): ?>
        <li>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
          <span><span class="sr-only">Horário: </span><?= e($horario) ?></span>
        </li>
      <?php endif; ?>
      <?php // RF-30 — tempo mínimo de preparo desta loja. Vazio esconde a linha. ?>
      <?php if ($preparo !== ''): ?>
        <li>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 22h14"/><path d="M5 2h14"/><path d="M17 22v-4.2a2 2 0 0 0-.6-1.4L12 12l-4.4 4.4a2 2 0 0 0-.6 1.4V22"/><path d="M7 2v4.2a2 2 0 0 0 .6 1.4L12 12l4.4-4.4a2 2 0 0 0 .6-1.4V2"/></svg>
          <span><span class="sr-only">Prazo: </span><?= e($preparo) ?></span>
        </li>
      <?php endif; ?>
    </ul>

    <?php // RF-26 — outros canais de venda desta loja (iFood e afins). Lista vazia não desenha nada. ?>
    <?php if ($canais !== []): ?>
      <div>
        <h3 class="font-sans text-sm font-bold">Peça também por</h3>
        <ul class="mt-2 flex flex-wrap gap-2">
          <?php foreach ($canais as $canal): ?>
            <li>
              <a href="<?= e((string) ($canal['url'] ?? '#')) ?>" target="_blank" rel="noopener noreferrer" class="pilula pilula-link">
                <?= e((string) ($canal['nome'] ?? 'Canal')) ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php // Não há "pedir por esta unidade": o carrinho do site fecha sempre com a
       // matriz. Esta loja atende por WhatsApp direto e tem o catálogo dela em PDF. ?>
    <div class="unidade-acoes">
      <?php if ($temZap): ?>
        <a href="https://wa.me/<?= e($whatsapp) ?>?text=<?= $msgUnidade ?>" target="_blank" rel="noopener noreferrer" class="botao-primario">
          <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 11.5a8.4 8.4 0 0 1-12.4 7.4L3 20.5l1.7-5.4A8.5 8.5 0 1 1 21 11.5Z"/></svg>
          Falar no WhatsApp
        </a>
      <?php else: ?>
        <span class="botao-desligado">WhatsApp em breve</span>
      <?php endif; ?>

      <?php if ($temPdf): ?>
        <a href="<?= e($pdf) ?>" download class="botao-secundario">
          <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v12"/><path d="m7 11 5 5 5-5"/><path d="M5 21h14"/></svg>
          Baixar catálogo
        </a>
      <?php else: ?>
        <?php // INTEGRAÇÃO FUTURA: envie o PDF pelo painel em Unidades e o botão liga sozinho. ?>
        <span class="botao-desligado">Catálogo em breve</span>
      <?php endif; ?>

      <?php if ($mapa !== ''): ?>
        <a href="<?= e($mapa) ?>" target="_blank" rel="noopener noreferrer" class="link-acao">Ver no mapa</a>
      <?php endif; ?>
    </div>
  </div>
</article>
