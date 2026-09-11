<?php

declare(strict_types=1);

/**
 * sobre.php — a empresa: quem é, de onde veio, o que faz e o que oferece a
 * outras empresas.
 *
 * Quatro requisitos numa página só, e de propósito:
 *
 *   RF-31  institucional      "quem somos"
 *   RF-21  história           "de onde viemos"
 *   RF-22  portfólio          "o que fazemos"
 *   RF-19  para empresas      "o que fazemos por você"
 *
 * São quatro respostas da MESMA conversa, na ordem em que alguém faria as
 * perguntas. Quatro páginas separadas dariam quatro páginas curtas e um menu
 * inchado — e no celular ninguém navega entre elas.
 *
 * Todo o texto vem de data/empresa.php. Aqui só existe estrutura: cada seção
 * some sozinha quando o dado dela está vazio.
 */

require_once __DIR__ . '/partials/bootstrap.php';

$empresa = dd_empresa();
$matriz  = dd_matriz();

$institucional = (array) ($empresa['institucional'] ?? []);
$numeros       = (array) ($empresa['numeros'] ?? []);
$historia      = (array) ($empresa['historia'] ?? []);
$portfolio     = (array) ($empresa['portfolio'] ?? []);
$paraEmpresas  = (array) ($empresa['empresas'] ?? []);

// O total de lojas é contado, nunca cadastrado: assim não diverge de
// data/units.php quando abrir a próxima.
$totalUnidades = count(dd_unidades());

// WhatsApp da matriz com a conversa de orçamento já começada (RF-19).
$zapMatriz = preg_replace('/\D+/', '', (string) ($matriz['whatsapp'] ?? ''));
$linkOrcamento = $zapMatriz !== ''
    ? 'https://wa.me/' . $zapMatriz . '?text=' . rawurlencode('Olá! Vim pelo site e quero um orçamento para a minha empresa.')
    : '';

$tituloPagina    = 'Sobre a Dolce Delícias — história, portfólio e atendimento a empresas';
$descricaoPagina = 'Quem é a Dolce Delícias, como a padaria começou, o que ela faz e como atende escolas, faculdades, empresas e indústrias.';

include __DIR__ . '/partials/header.php';
?>

<!-- ============================================================
     HERÓI — mesmo desenho da home, via partials/hero.php
     ============================================================ -->
<?php
$heroEtiqueta  = 'A empresa · ' . $totalUnidades . ' unidades';
$heroLinhas    = [
    ['texto' => 'Padaria'],
    ['texto' => 'de bairro.'],
    ['texto' => 'Cozinha',  'destaque' => true],
    ['texto' => 'de gente.', 'destaque' => true],
];
$heroTexto     = trim((string) ($institucional['chamada'] ?? '')) !== ''
    ? (string) $institucional['chamada']
    : 'Quem somos, de onde viemos e o que fazemos todo dia';
$heroTextoLink = ['href' => '#empresas', 'texto' => 'atendemos empresas'];
$heroCta       = ['href' => '#portfolio', 'texto' => 'O que fazemos'];
$heroTira      = [];

include __DIR__ . '/partials/hero.php';
?>

<div class="faixa-raios" aria-hidden="true"></div>

<!-- ============================================================
     RF-31 — INSTITUCIONAL
     ============================================================ -->
<?php if (!empty($institucional['texto']) || !empty($institucional['valores'])): ?>
  <section id="institucional" class="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-16 lg:px-8 lg:py-20"
           aria-labelledby="titulo-institucional">

    <div class="grid gap-8 lg:grid-cols-[1.1fr_1fr] lg:gap-14">
      <div>
        <h2 id="titulo-institucional" class="text-3xl sm:text-4xl">Quem somos</h2>
        <?php foreach ((array) ($institucional['texto'] ?? []) as $paragrafo): ?>
          <p class="mt-4 max-w-prose text-lg leading-relaxed text-crust"><?= e((string) $paragrafo) ?></p>
        <?php endforeach; ?>
      </div>

      <?php // Os números ficam ao lado do texto, não numa faixa própria: são a
         // prova do parágrafo, e longe dele viram enfeite. ?>
      <?php if ($numeros !== []): ?>
        <dl class="grid grid-cols-2 gap-px self-start overflow-hidden rounded-bandeja border border-base-300 bg-base-300">
          <div class="bg-papel px-4 py-5 text-center sm:px-6 sm:py-7">
            <dd class="fonte-display text-3xl text-brand sm:text-4xl"><?= e((string) $totalUnidades) ?></dd>
            <dt class="mt-1 text-sm font-semibold text-crust">unidades</dt>
          </div>
          <?php foreach ($numeros as $numero): ?>
            <div class="bg-papel px-4 py-5 text-center sm:px-6 sm:py-7">
              <dd class="fonte-display text-3xl text-brand sm:text-4xl"><?= e((string) ($numero['valor'] ?? '')) ?></dd>
              <dt class="mt-1 text-sm font-semibold text-crust"><?= e((string) ($numero['rotulo'] ?? '')) ?></dt>
            </div>
          <?php endforeach; ?>
        </dl>
      <?php endif; ?>
    </div>

    <?php // RNF-11 — os valores da marca. ?>
    <?php if (!empty($institucional['valores'])): ?>
      <ul class="mt-10 grid gap-5 md:grid-cols-3">
        <?php foreach ($institucional['valores'] as $valor): ?>
          <li class="revelar rounded-bandeja border border-base-300 bg-papel p-6 shadow-bandeja">
            <h3 class="text-xl"><?= e((string) ($valor['titulo'] ?? '')) ?></h3>
            <p class="mt-2 text-sm leading-relaxed text-crust"><?= e((string) ($valor['texto'] ?? '')) ?></p>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </section>
<?php endif; ?>

<!-- ============================================================
     RF-21 — HISTÓRIA
     ============================================================ -->
<?php if ($historia !== []): ?>
  <section id="historia" class="border-y border-base-300 bg-base-200" aria-labelledby="titulo-historia">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-16 lg:px-8 lg:py-20">

      <div class="max-w-2xl">
        <h2 id="titulo-historia" class="text-3xl sm:text-4xl">Nossa história</h2>
        <p class="mt-3 text-lg leading-relaxed text-crust">
          Do primeiro forno até as <?= e((string) $totalUnidades) ?> lojas de hoje.
        </p>
      </div>

      <?php
      /**
       * Linha do tempo em <ol>: é sequência de verdade, e a ordem importa para
       * quem lê com leitor de tela também.
       *
       * A régua vertical é uma borda no <li>, não um elemento à parte — no
       * celular ela fica à esquerda e no desktop continua igual, sem precisar de
       * dois desenhos. O último item esconde a régua para a linha não sobrar
       * pendurada embaixo do texto.
       *
       * ATENÇÃO: o último item fica com a borda TRANSPARENTE, não sem borda.
       * Removê-la tira 2px da caixa e desalinha o ponto e o texto desse marco
       * em relação aos outros — dá para ver a olho nu.
       */
      $ultimoMarco = count($historia) - 1;
      ?>
      <ol class="mt-10 max-w-3xl">
        <?php foreach ($historia as $i => $marco): ?>
          <li class="revelar relative flex gap-4 border-l-2 pb-8 pl-4 sm:gap-6<?= $i === $ultimoMarco ? ' border-transparent' : ' border-base-300' ?>">
            <span class="absolute -left-[0.5625rem] top-1 h-4 w-4 rounded-full border-[3px] border-base-200 bg-primary" aria-hidden="true"></span>

            <div class="-mt-1 pl-4">
              <p class="fonte-display text-xl text-brand sm:text-2xl"><?= e((string) ($marco['ano'] ?? '')) ?></p>
              <h3 class="mt-1 text-xl sm:text-2xl"><?= e((string) ($marco['titulo'] ?? '')) ?></h3>
              <p class="mt-2 max-w-prose leading-relaxed text-crust"><?= e((string) ($marco['texto'] ?? '')) ?></p>
            </div>
          </li>
        <?php endforeach; ?>
      </ol>
    </div>
  </section>
<?php endif; ?>

<!-- ============================================================
     RF-22 — PORTFÓLIO
     ============================================================ -->
<?php if ($portfolio !== []): ?>
  <section id="portfolio" class="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-16 lg:px-8 lg:py-20"
           aria-labelledby="titulo-portfolio">

    <div class="max-w-2xl">
      <h2 id="titulo-portfolio" class="text-3xl sm:text-4xl">O que fazemos</h2>
      <p class="mt-3 text-lg leading-relaxed text-crust">
        Quatro frentes, a mesma cozinha. O catálogo do site cobre as encomendas e o balcão;
        o resto sai sob medida na conversa.
      </p>
    </div>

    <ul class="mt-8 grid gap-5 sm:grid-cols-2">
      <?php foreach ($portfolio as $servico): ?>
        <li class="revelar flex flex-col rounded-bandeja border border-base-300 bg-papel p-6 shadow-bandeja">
          <h3 class="text-2xl"><?= e((string) ($servico['titulo'] ?? '')) ?></h3>
          <p class="mt-2 leading-relaxed text-crust"><?= e((string) ($servico['texto'] ?? '')) ?></p>

          <?php if (!empty($servico['itens'])): ?>
            <ul class="mt-4 flex flex-wrap gap-2">
              <?php foreach ($servico['itens'] as $item): ?>
                <li class="rounded-full border border-base-300 bg-base-200 px-3 py-1.5 text-sm font-semibold text-crust">
                  <?= e((string) $item) ?>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>

    <div class="mt-8 flex flex-wrap gap-3">
      <a href="index.php#catalogo" class="btn btn-primary h-12 min-h-12 px-6 font-bold">Ver o catálogo</a>
      <a href="unidades.php" class="btn h-12 min-h-12 border border-base-300 bg-papel px-6 font-semibold text-crust hover:border-brand hover:text-brand">
        Onde a gente está
      </a>
    </div>
  </section>
<?php endif; ?>

<!-- ============================================================
     RF-19 — PARA EMPRESAS E INDÚSTRIAS
     ============================================================ -->
<?php if (!empty($paraEmpresas['itens']) || trim((string) ($paraEmpresas['texto'] ?? '')) !== ''): ?>
  <section id="empresas" class="border-t border-base-300 bg-neutral text-neutral-content"
           aria-labelledby="titulo-empresas">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-16 lg:px-8 lg:py-20">

      <div class="max-w-2xl">
        <p class="text-xs font-bold uppercase tracking-[0.22em] opacity-70">Para empresas e indústrias</p>
        <h2 id="titulo-empresas" class="mt-4 text-3xl sm:text-4xl">
          <?= e((string) ($paraEmpresas['chamada'] ?? 'Para a sua empresa')) ?>
        </h2>
        <?php if (trim((string) ($paraEmpresas['texto'] ?? '')) !== ''): ?>
          <p class="mt-3 text-lg leading-relaxed opacity-85"><?= e((string) $paraEmpresas['texto']) ?></p>
        <?php endif; ?>
      </div>

      <?php if (!empty($paraEmpresas['itens'])): ?>
        <ul class="mt-10 grid gap-5 md:grid-cols-3">
          <?php foreach ($paraEmpresas['itens'] as $item): ?>
            <li class="revelar rounded-bandeja bg-white/10 p-6">
              <h3 class="text-xl"><?= e((string) ($item['titulo'] ?? '')) ?></h3>
              <p class="mt-2 text-sm leading-relaxed opacity-85"><?= e((string) ($item['texto'] ?? '')) ?></p>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>

      <?php // Os passos do orçamento, numerados: é sequência, igual ao "Como
         // encomendar" da home. ?>
      <?php if (!empty($paraEmpresas['comoFunciona'])): ?>
        <div class="mt-10">
          <h3 class="text-2xl">Como pedir um orçamento</h3>
          <ol class="mt-5 grid gap-4 md:grid-cols-3">
            <?php foreach ($paraEmpresas['comoFunciona'] as $i => $passo): ?>
              <li class="flex gap-3">
                <span class="fonte-display flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-accent text-accent-content">
                  <?= e((string) ($i + 1)) ?>
                </span>
                <p class="text-sm leading-relaxed opacity-85"><?= e((string) $passo) ?></p>
              </li>
            <?php endforeach; ?>
          </ol>
        </div>
      <?php endif; ?>

      <?php if ($linkOrcamento !== ''): ?>
        <div class="mt-10 flex flex-wrap items-center gap-4">
          <a href="<?= e($linkOrcamento) ?>" target="_blank" rel="noopener noreferrer"
             class="btn h-13 min-h-13 gap-2 border-none bg-accent px-7 text-base font-bold text-accent-content hover:bg-accent/85">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 11.5a8.4 8.4 0 0 1-12.4 7.4L3 20.5l1.7-5.4A8.5 8.5 0 1 1 21 11.5Z"/></svg>
            Pedir orçamento no WhatsApp
          </a>
          <p class="text-sm opacity-75">Falamos com a matriz — ela responde por todas as unidades.</p>
        </div>
      <?php endif; ?>
    </div>
  </section>
<?php endif; ?>

<?php include __DIR__ . '/partials/footer.php'; ?>
