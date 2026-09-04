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
$matriz   = $unidades[0] ?? null;
foreach ($unidades as $u) {
    if (!empty($u['matriz'])) {
        $matriz = $u;
        break;
    }
}

$tituloPagina    ??= 'Dolce Delícias — encomendas de salgados, assados e doces';
$descricaoPagina ??= 'Padaria e panificadora que atende escolas, faculdades, eventos e encomendas. Peça o cento pelo WhatsApp da unidade mais perto de você.';

// Os atalhos-âncora vivem na home; nas outras páginas eles voltam para lá.
$naHome  = basename((string) ($_SERVER['SCRIPT_NAME'] ?? '')) === 'index.php';
$ancora  = $naHome ? '' : 'index.php';

$menu = [
    ['href' => $ancora . '#catalogo',   'texto' => 'Catálogo'],
    ['href' => $ancora . '#encomendas', 'texto' => 'Encomendas'],
    ['href' => $ancora . '#unidades',   'texto' => 'Unidades'],
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

<!-- Tema antes da primeira pintura: evita o flash branco no modo escuro. -->
<script>
  (function () {
    var raiz = document.documentElement;
    raiz.classList.remove('sem-js');
    try {
      var salvo = localStorage.getItem('dolce_theme');
      var escuro = salvo ? salvo === 'dolce-dark'
                         : matchMedia('(prefers-color-scheme: dark)').matches;
      raiz.dataset.theme = escuro ? 'dolce-dark' : 'dolce';
    } catch (e) { /* localStorage bloqueado: fica no tema claro */ }
  })();
</script>

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
                <a href="<?= e($item['href']) ?>"
                   class="rounded-xl px-3 py-2 text-[0.95rem] font-semibold text-crust transition-colors hover:bg-base-300/60 hover:text-brand">
                  <?= e($item['texto']) ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </nav>

        <div class="ml-auto flex items-center gap-1.5 sm:gap-2">

          <!-- ----------------------------------------------------------------
               SELETOR DE UNIDADE
               <details> nativo: abre/fecha e navega pelo teclado sem JS.
               A escolha vai para localStorage['dolce_unit'] e define o WhatsApp
               do checkout e o PDF do catálogo.
               ---------------------------------------------------------------- -->
          <details class="dropdown dropdown-end" data-unit-dropdown>
            <summary
              class="btn btn-ghost h-11 min-h-11 gap-1.5 rounded-2xl border border-base-300 bg-papel px-2.5 font-semibold shadow-bandeja sm:gap-2 sm:px-3"
              aria-label="Escolher a unidade que vai atender seu pedido">
              <svg class="h-[1.15rem] w-[1.15rem] shrink-0 text-brand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>
              </svg>
              <span class="hidden max-w-[9rem] truncate text-sm sm:inline" data-unit-label><?= e($matriz['nome'] ?? 'Escolher unidade') ?></span>
              <svg class="h-4 w-4 shrink-0 opacity-60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
            </summary>

            <div class="dropdown-content z-[60] mt-2 w-[19rem] rounded-2xl border border-base-300 bg-papel p-2 shadow-bandeja-alta">
              <p class="px-3 pb-2 pt-1 text-xs font-semibold text-crust">
                Qual unidade atende seu pedido?
              </p>
              <ul class="menu w-full gap-0.5 p-0" role="listbox" aria-label="Unidades da Dolce Delícias">
                <?php foreach ($unidades as $unidade): ?>
                  <li>
                    <button type="button"
                            role="option"
                            aria-selected="false"
                            data-unit-option="<?= e($unidade['slug']) ?>"
                            class="flex-col items-start gap-0.5 rounded-xl px-3 py-2 text-left">
                      <span class="flex w-full items-center gap-2 font-semibold">
                        <?= e($unidade['nome']) ?>
                        <?php if (!empty($unidade['matriz'])): ?>
                          <span class="badge badge-sm border-none bg-accent text-accent-content">matriz</span>
                        <?php endif; ?>
                        <svg class="oculto ml-auto h-4 w-4 text-brand" data-unit-check viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 13 4 4L19 7"/></svg>
                      </span>
                      <span class="text-xs font-normal text-crust"><?= e($unidade['endereco']) ?></span>
                    </button>
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
          </details>

          <!-- Alternar claro/escuro -->
          <button type="button"
                  data-theme-toggle
                  class="btn btn-ghost h-11 min-h-11 w-11 rounded-2xl border border-base-300 bg-papel p-0 shadow-bandeja"
                  aria-pressed="false"
                  aria-label="Ativar modo escuro">
            <svg class="dark-oculta h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z"/></svg>
            <svg class="dark-mostra hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
          </button>

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
            <ul class="menu dropdown-content z-[60] mt-2 w-56 gap-1 rounded-2xl border border-base-300 bg-papel p-2 shadow-bandeja-alta">
              <?php foreach ($menu as $item): ?>
                <li><a class="rounded-xl px-3 py-2.5 font-semibold" href="<?= e($item['href']) ?>"><?= e($item['texto']) ?></a></li>
              <?php endforeach; ?>
              <li><a class="rounded-xl px-3 py-2.5 font-semibold" href="/carrinho.php">Ver meu pedido</a></li>
            </ul>
          </details>

        </div>
      </div>
    </header>

    <main id="conteudo" class="flex-1">
