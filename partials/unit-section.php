<?php

declare(strict_types=1);

/**
 * partials/unit-section.php — uma loja em unidades.php.
 *
 * Dois desenhos, pela pergunta que cada um responde:
 *   - a matriz vem em destaque (.loja-matriz): é ela que recebe o pedido do
 *     site, então mostra os dados com rótulo e a foto (ou a fachada desenhada);
 *   - as outras lojas são uma linha da lista (.loja): nome e bairro, endereço,
 *     horário e os três caminhos de contato. Quem procura "a mais perto de
 *     mim" lê a lista de cima para baixo sem abrir nada.
 *
 * O id é o slug da loja: /unidades#unidade-3 cai direto nela (a página do
 * produto usa isso em "Onde encontrar"), e :target a realça.
 *
 * Espera:
 *   $unidade  array  item vindo de dd_unidades()
 */

require_once __DIR__ . '/bootstrap.php';

/** @var array $unidade */
$ehMatriz = !empty($unidade['matriz']);
$slug     = (string) $unidade['slug'];
$foto     = dd_imagem($unidade['imagem'] ?? null);
$whatsapp = dd_whatsapp($unidade); // '' quando vazio ou de exemplo: aí não vira botão
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

// "Unidade 2 — Centro" vira nome "Unidade 2" e bairro "Centro".
[$nomeLoja, $bairro] = array_pad(array_map('trim', explode(' — ', (string) $unidade['nome'], 2)), 2, '');

// Mensagem inicial do WhatsApp da unidade (sem carrinho — é o contato direto).
$msgUnidade = rawurlencode('Olá! Vim pelo site da Dolce Delícias e quero falar com a ' . ($unidade['nome'] ?? 'unidade') . '.');

$icone = [
    'endereco' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>',
    'horario'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>',
    'preparo'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 22h14"/><path d="M5 2h14"/><path d="M17 22v-4.2a2 2 0 0 0-.6-1.4L12 12l-4.4 4.4a2 2 0 0 0-.6 1.4V22"/><path d="M7 2v4.2a2 2 0 0 0 .6 1.4L12 12l4.4-4.4a2 2 0 0 0 .6-1.4V2"/></svg>',
    'zap'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 11.5a8.4 8.4 0 0 1-12.4 7.4L3 20.5l1.7-5.4A8.5 8.5 0 1 1 21 11.5Z"/></svg>',
    'pdf'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v12"/><path d="m7 11 5 5 5-5"/><path d="M5 21h14"/></svg>',
    'mapa'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 4 3 6v14l6-2 6 2 6-2V4l-6 2-6-2Z"/><path d="M9 4v14M15 6v14"/></svg>',
];
?>
<?php if ($ehMatriz): ?>
  <section id="<?= e($slug) ?>" class="loja-matriz" aria-labelledby="titulo-<?= e($slug) ?>">
    <div class="loja-matriz-corpo">
      <p class="loja-selo">Recebe os pedidos do site</p>
      <h2 id="titulo-<?= e($slug) ?>" class="mt-3 text-3xl sm:text-4xl"><?= e($nomeLoja) ?></h2>
      <?php if ($bairro !== ''): ?><p class="mt-1 font-semibold text-crust"><?= e($bairro) ?></p><?php endif; ?>
      <?php if ($sobre !== ''): ?><p class="mt-4 max-w-prose leading-relaxed text-crust"><?= e($sobre) ?></p><?php endif; ?>

      <dl class="loja-dados">
        <?php if ($endereco !== ''): ?>
          <div><dt><?= $icone['endereco'] ?>Endereço</dt><dd><?= e($endereco) ?></dd></div>
        <?php endif; ?>
        <?php if ($horario !== ''): ?>
          <div><dt><?= $icone['horario'] ?>Horário</dt><dd><?= e($horario) ?></dd></div>
        <?php endif; ?>
        <?php if ($preparo !== ''): ?>
          <div><dt><?= $icone['preparo'] ?>Prazo</dt><dd><?= e($preparo) ?></dd></div>
        <?php endif; ?>
      </dl>

      <div class="mt-6 flex flex-wrap items-center gap-3">
        <a href="/#catalogo" class="botao-primario">Fazer um pedido</a>
        <?php if ($whatsapp !== ''): ?>
          <a href="https://wa.me/<?= e($whatsapp) ?>?text=<?= $msgUnidade ?>" target="_blank" rel="noopener noreferrer" class="botao-secundario"><span class="h-5 w-5"><?= $icone['zap'] ?></span>WhatsApp</a>
        <?php endif; ?>
        <?php if ($temPdf): ?>
          <a href="<?= e($pdf) ?>" download class="link-acao"><span class="h-4 w-4"><?= $icone['pdf'] ?></span>Catálogo em PDF</a>
        <?php endif; ?>
        <?php if ($mapa !== ''): ?>
          <a href="<?= e($mapa) ?>" target="_blank" rel="noopener noreferrer" class="link-acao"><span class="h-4 w-4"><?= $icone['mapa'] ?></span>Ver no mapa</a>
        <?php endif; ?>
      </div>
    </div>

    <figure class="loja-matriz-foto">
      <?php if ($foto): ?>
        <img src="<?= e($foto) ?>" alt="Foto da <?= e((string) $unidade['nome']) ?>" loading="lazy" decoding="async" width="1600" height="1000">
      <?php else: ?>
        <?php // INTEGRAÇÃO FUTURA: foto da fachada, cadastrada no painel em Unidades. ?>
        <?php $fachadaClasse = 'w-full max-w-sm'; include __DIR__ . '/ilustra-fachada.php'; ?>
      <?php endif; ?>
    </figure>
  </section>
<?php else: ?>
  <li id="<?= e($slug) ?>" class="loja">
    <div class="loja-nome">
      <h3 class="font-sans text-lg font-bold"><?= e($nomeLoja) ?></h3>
      <?php if ($bairro !== ''): ?><p class="text-sm font-semibold text-crust"><?= e($bairro) ?></p><?php endif; ?>
    </div>

    <p class="loja-dado"><?= $icone['endereco'] ?><span><span class="sr-only">Endereço: </span><?= e($endereco !== '' ? $endereco : 'Endereço em breve') ?></span></p>
    <p class="loja-dado"><?= $icone['horario'] ?><span><span class="sr-only">Horário: </span><?= e($horario !== '' ? $horario : 'Horário em breve') ?></span></p>

    <div class="loja-acoes">
      <?php if ($whatsapp !== ''): ?>
        <a href="https://wa.me/<?= e($whatsapp) ?>?text=<?= $msgUnidade ?>" target="_blank" rel="noopener noreferrer" class="loja-acao" aria-label="WhatsApp da <?= e((string) $unidade['nome']) ?>"><?= $icone['zap'] ?><span>WhatsApp</span></a>
      <?php else: ?>
        <span class="loja-acao loja-acao-off"><?= $icone['zap'] ?><span><span class="sr-only">WhatsApp: </span>Em breve</span></span>
      <?php endif; ?>
      <?php if ($temPdf): ?>
        <a href="<?= e($pdf) ?>" download class="loja-acao" aria-label="Baixar o catálogo da <?= e((string) $unidade['nome']) ?>"><?= $icone['pdf'] ?><span>Catálogo</span></a>
      <?php endif; ?>
      <?php if ($mapa !== ''): ?>
        <a href="<?= e($mapa) ?>" target="_blank" rel="noopener noreferrer" class="loja-acao" aria-label="Ver a <?= e((string) $unidade['nome']) ?> no mapa"><?= $icone['mapa'] ?><span>Mapa</span></a>
      <?php endif; ?>
    </div>

    <?php if ($canais !== []): ?>
      <p class="loja-canais">
        Também em:
        <?php foreach ($canais as $i => $canal): ?><?= $i > 0 ? ', ' : ' ' ?><a href="<?= e((string) ($canal['url'] ?? '#')) ?>" target="_blank" rel="noopener noreferrer" class="font-semibold underline underline-offset-2"><?= e((string) ($canal['nome'] ?? 'Canal')) ?></a><?php endforeach; ?>
      </p>
    <?php endif; ?>
  </li>
<?php endif; ?>
