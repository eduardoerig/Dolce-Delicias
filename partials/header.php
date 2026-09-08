<?php

declare(strict_types=1);

/**
 * partials/header.php — abre o documento, o drawer do carrinho e desenha o topo.
 * Feche com partials/footer.php.
 *
 * Variáveis opcionais definidas ANTES do include:
 *   $tituloPagina    string  título da aba
 *   $descricaoPagina string  meta description
 */

require_once __DIR__ . '/bootstrap.php';

$unidades = dd_unidades();
// Matriz primeiro, na mesma ordem usada em unidades.php.
usort($unidades, static fn (array $a, array $b): int => (int) !empty($b['matriz']) <=> (int) !empty($a['matriz']));

$tituloPagina    ??= 'Dolce Delícias — encomendas de salgados, assados e doces';
$descricaoPagina ??= 'Padaria e panificadora que atende escolas, faculdades, eventos e encomendas. Peça o cento pelo WhatsApp da unidade mais perto de você.';

// Os atalhos-âncora vivem na home; nas outras páginas eles voltam para lá.
$naHome  = basename((string) ($_SERVER['SCRIPT_NAME'] ?? '')) === 'index.php';
$ancora  = $naHome ? '' : 'index.php';

/**
 * Itens do menu "Catálogo".
 * Cada unidade vende alguns produtos diferentes, então há um PDF por loja; o
 * primeiro item leva à grade do site e o último ao catálogo da rede inteira.
 * 'arquivo' => false marca o item que navega em vez de baixar.
 */
$itensCatalogo = [
    [
        'href'    => $ancora . '#catalogo',
        'texto'   => 'Ver catálogo no site',
        'arquivo' => false,
        'tem'     => true,
    ],
];

foreach ($unidades as $unidadeMenu) {
    $itensCatalogo[] = [
        'href'    => (string) ($unidadeMenu['catalogoPdf'] ?? ''),
        'texto'   => $unidadeMenu['nome'],
        'arquivo' => true,
        'tem'     => dd_tem_pdf($unidadeMenu['catalogoPdf'] ?? null),
    ];
}

$pdfCompleto = dd_catalogo_completo();

$menu = [
    ['href' => $ancora . '#catalogo',   'texto' => 'Catálogo', 'catalogos' => true],
    ['href' => $ancora . '#encomendas', 'texto' => 'Encomendas'],
    ['href' => 'unidades.php',          'texto' => 'Unidades'],
    ['href' => $ancora . '#contato',    'texto' => 'Contato'],
];
?>
<!doctype html>
<html lang="pt-BR" data-theme="dolce" class="sem-js">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($tituloPagina) ?></title>
<meta name="description" content="<?= e($descricaoPagina) ?>">
<meta name="theme-color" content="#E1051E">
<link rel="icon" href="/assets/img/logo.svg" type="image/svg+xml">

<?php // O site tem um tema só (claro). Isto apenas avisa que o JS está vivo:
      // sem a classe .sem-js, o conteúdo com .revelar já nasce visível. ?>
<script>document.documentElement.classList.remove('sem-js');</script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@500;600;700;800&family=Figtree:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

<link rel="stylesheet" href="/assets/css/app.css">

<script type="module" src="/assets/js/cart.js"></script>
<script type="module" src="/assets/js/ui.js"></script>
</head>
<body class="bg-base-100 text-base-content antialiased">

<a href="#conteudo"
   class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] focus:rounded-xl focus:bg-primary focus:px-4 focus:py-2 focus:text-primary-content">
  Pular para o conteúdo
</a>

<?php
/**
 * Unidades publicadas para o JavaScript.
 * O checkout lê daqui o WhatsApp da unidade escolhida em localStorage['dolce_unit'].
 */
?>
<script type="application/json" id="units-data"><?= json_encode($unidades, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>

<div class="drawer drawer-end">
  <?php // O checkbox e so o mecanismo do drawer: quem controla e o botao
     // [data-cart-open]. Fora da tabulacao para nao virar "caixa de selecao"
     // sem rotulo no meio do cabecalho. ?>
  <input id="carrinho-toggle" type="checkbox" class="drawer-toggle" tabindex="-1" aria-hidden="true">

  <div class="drawer-content flex min-h-screen flex-col">

    <header class="sticky top-0 z-50 border-b border-base-300/70 bg-base-100/85 backdrop-blur-md">
      <div class="mx-auto flex h-[4.5rem] max-w-7xl items-center gap-2 px-3 sm:gap-3 sm:px-6 lg:px-8">

        <a href="/" class="flex shrink-0 items-center gap-2.5 rounded-xl" aria-label="Dolce Delícias, página inicial">
          <?= dd_logo('h-9 w-auto drop-shadow-sm sm:h-11') ?>
        </a>

        <!-- Navegação em telas grandes -->
        <nav class="ml-2 hidden lg:block" aria-label="Seções do site">
          <ul class="flex items-center gap-1">
            <?php foreach ($menu as $item): ?>
              <li>
                <?php if (!empty($item['catalogos'])): ?>
                  <?php // Catálogo é dropdown: cada unidade tem o PDF dela. ?>
                  <details class="dropdown" data-catalog-dropdown>
                    <summary class="flex cursor-pointer list-none items-center gap-1 rounded-xl px-3 py-2 text-[0.95rem] font-semibold text-crust transition-colors hover:bg-base-300/60 hover:text-brand [&::-webkit-details-marker]:hidden">
                      <?= e($item['texto']) ?>
                      <svg class="h-4 w-4 opacity-60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                    </summary>
                    <ul class="menu dropdown-content z-[60] mt-2 w-72 gap-0.5 rounded-2xl border border-base-300 bg-papel p-2 shadow-bandeja-alta">
                      <?php include __DIR__ . '/catalog-menu.php'; ?>
                    </ul>
                  </details>
                <?php else: ?>
                  <a href="<?= e($item['href']) ?>"
                     class="block rounded-xl px-3 py-2 text-[0.95rem] font-semibold text-crust transition-colors hover:bg-base-300/60 hover:text-brand">
                    <?= e($item['texto']) ?>
                  </a>
                <?php endif; ?>
              </li>
            <?php endforeach; ?>
          </ul>
        </nav>

        <div class="ml-auto flex items-center gap-1.5 sm:gap-2">
          <!-- Carrinho -->
          <button type="button"
                  data-cart-open
                  aria-controls="painel-carrinho"
                  aria-expanded="false"
                  class="btn btn-primary h-11 min-h-11 gap-2 rounded-2xl px-3 shadow-bandeja sm:px-3.5">
            <span class="relative inline-flex" data-cart-icon>
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M3 4h2l2.4 11.2a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.5L21 8H6"/><circle cx="10" cy="20" r="1.4"/><circle cx="18" cy="20" r="1.4"/>
              </svg>
              <span data-cart-badge
                    class="oculto absolute -right-2.5 -top-2 min-w-[1.35rem] rounded-full bg-accent px-1 text-center text-[0.7rem] font-bold leading-[1.35rem] text-accent-content">0</span>
            </span>
            <span class="hidden text-sm font-bold sm:inline">Pedido</span>
            <span class="sr-only" data-cart-sr aria-live="polite">Carrinho vazio</span>
          </button>

          <!-- Menu no mobile -->
          <details class="dropdown dropdown-end lg:hidden" data-menu-dropdown>
            <summary class="btn btn-ghost h-11 min-h-11 w-11 rounded-2xl border border-base-300 bg-papel p-0"
                     aria-label="Abrir menu de navegação">
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
            </summary>
            <ul class="menu dropdown-content z-[60] mt-2 max-h-[75vh] w-[19rem] flex-nowrap gap-1 overflow-y-auto rounded-2xl border border-base-300 bg-papel p-2 shadow-bandeja-alta">
              <?php foreach ($menu as $item): ?>
                <li><a class="rounded-xl px-3 py-2.5 font-semibold" href="<?= e($item['href']) ?>"><?= e($item['texto']) ?></a></li>
              <?php endforeach; ?>
              <li><a class="rounded-xl px-3 py-2.5 font-semibold" href="/carrinho.php">Ver meu pedido</a></li>

              <?php
                // No celular não dá para aninhar <details>: a lista de PDFs entra
                // achatada aqui embaixo. O link "Catálogo" acima já cobre a grade
                // do site, então esse item sai da lista.
                $somenteArquivos = true;
              ?>
              <li class="mx-3 my-1 border-t border-base-300" aria-hidden="true"></li>
              <li class="px-3 pb-1 pt-1 text-xs font-semibold text-crust">Catálogos em PDF</li>
              <?php include __DIR__ . '/catalog-menu.php'; ?>
              <?php $somenteArquivos = false; ?>
            </ul>
          </details>

        </div>
      </div>
    </header>

    <main id="conteudo" class="flex-1">
