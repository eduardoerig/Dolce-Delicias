<?php

declare(strict_types=1);

/**
 * sobre.php — a empresa: quem é, de onde veio e o que faz.
 *
 * Três requisitos numa página só, e de propósito:
 *
 *   RF-31  institucional      "quem somos"
 *   RF-21  história           "de onde viemos"
 *   RF-22  portfólio          "o que fazemos" (inclui eventos, escolas e empresas)
 *
 * São três respostas da MESMA conversa, na ordem em que alguém faria as
 * perguntas. Páginas separadas dariam páginas curtas e um menu inchado — e
 * no celular ninguém navega entre elas.
 *
 * O desenho (detalhes abaixo, no HTML): herói marrom com a coxinha sendo
 * modelada, cartões com foto para os serviços, a faixa vermelha dos valores,
 * a linha do tempo e o vídeo da marca.
 *
 * Todo o texto vem de data/empresa.php. Aqui só existe estrutura: cada seção
 * some sozinha quando o dado dela está vazio.
 */

require_once DD_BASE . '/partials/bootstrap.php';

$empresa = dd_empresa();

$institucional = (array) ($empresa['institucional'] ?? []);
$numeros       = (array) ($empresa['numeros'] ?? []);
$historia      = (array) ($empresa['historia'] ?? []);
$portfolio     = (array) ($empresa['portfolio'] ?? []);

$contato       = (array) ($empresa['contato'] ?? []);

$chamada    = rtrim(trim((string) ($institucional['chamada'] ?? '')), '.');
$assinatura = rtrim(trim((string) ($institucional['assinatura'] ?? '')), '.');
$instagram  = ltrim(trim((string) ($contato['instagram'] ?? '')), '@');
$lugares    = trim((string) ($contato['lugares'] ?? ''));

// WhatsApp da matriz com a conversa já começada. Número vazio ou de exemplo
// não vira botão — ver dd_whatsapp().
$zapMatriz = dd_whatsapp(dd_matriz());
$linkZap   = $zapMatriz !== ''
    ? 'https://wa.me/' . $zapMatriz . '?text=' . rawurlencode('Olá! Vim pelo site da Dolce Delícias e quero fazer um pedido.')
    : '';

$tituloPagina    = 'Sobre a Dolce Delícias — salgados artesanais feitos do zero em Toledo';
$descricaoPagina = 'A Dolce Delícias nasceu do zero, com receitas feitas em casa. Salgados artesanais, pães e mini pizzas para festas, lanche escolar e o café do dia a dia, em Toledo.';

include DD_BASE . '/partials/header.php';
?>

<?php
/*
 * Desenho inspirado nas páginas "Sobre" de referência (Colab, BGC, Econodata,
 * Sólides): abertura na cor da marca com a foto ao lado, fatos grandes
 * centralizados, a história com foto, as frentes em cartões, a crença da
 * marca centralizada, o vídeo e o convite para pedir. Cantos arredondados,
 * divisórias curvas entre as faixas e botões em pílula, com as cores e a
 * letra da Dolce.
 *
 *   1. abertura vermelha: a assinatura da marca, a frase e os botões; a foto
 *      oficial ao lado (no celular, embaixo). Termina numa curva;
 *   2. fatos: três itens grandes, centralizados;
 *   3. "Nossa história": a foto da coxinha sendo modelada e o texto da marca;
 *   4. "O que fazemos": um cartão com foto para cada frente;
 *   5. "No que acreditamos": a frase-tese e os valores em três colunas;
 *   6. "Nossa história" com datas (linha do tempo) — só com datas reais;
 *   7. o vídeo, centralizado;
 *   8. o convite final: catálogo, WhatsApp e Instagram.
 *
 * Texto nunca fica sobre comida: a foto tem coluna própria em todo tamanho.
 * Se um arquivo de foto não existir, a seção fica só com o texto. Todo o
 * texto vem de data/empresa.php.
 */
$fotoSobre = static fn (string $caminho): ?string => dd_imagem($caminho);

// Uma foto por frente do portfólio, na ordem de data/empresa.php.
$fotosPortfolio = [
    ['/assets/img/sobre/encomendas-800.webp', 'Coxinhas, uma aberta mostrando o recheio de frango'],
    ['/assets/img/sobre/escolar-800.webp', 'Crianças lanchando no pátio da escola'],
    ['/assets/img/sobre/balcao-800.webp', 'Vitrine do balcão com folhados'],
];

// Foto oficial da marca (post do Instagram da Dolce, nov/2025).
$fotoOficial = dd_imagem('/assets/img/sobre/oficial-960.webp');
$fotoCozinha = dd_imagem('/assets/img/sobre/topo-1400.webp');

$iconeZap = '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 11.5a8.4 8.4 0 0 1-12.4 7.4L3 20.5l1.7-5.4A8.5 8.5 0 1 1 21 11.5Z"/></svg>';
$iconeInstagram = '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="0.5" fill="currentColor"/></svg>';
?>

<div class="pagina-sobre bg-farinha">

  <section class="sobre-abertura" aria-labelledby="titulo-sobre">
    <div class="sobre-abertura-grade mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="sobre-abertura-texto">
        <nav aria-label="Você está aqui" class="flex items-center gap-2 text-sm font-semibold">
          <a href="/" class="rounded underline-offset-4 hover:underline">Início</a>
          <span aria-hidden="true">/</span>
          <span aria-current="page">A empresa</span>
        </nav>

        <h1 id="titulo-sobre" class="sobre-abertura-titulo">
          <span class="sr-only">A Dolce Delícias: </span><?= e($assinatura !== '' ? $assinatura . '.' : ($chamada !== '' ? $chamada . '.' : 'A Dolce Delícias.')) ?>
        </h1>

        <p class="sobre-abertura-frase">
          Salgados artesanais, pães e mini pizzas feitos do zero em Toledo, para festas, escolas e o café do dia a dia.
        </p>

        <div class="sobre-botoes">
          <a href="/#catalogo" class="sobre-botao sobre-botao-amarelo">Ver o catálogo</a>
          <?php if ($linkZap !== ''): ?>
            <a href="<?= e($linkZap) ?>" target="_blank" rel="noopener noreferrer" class="sobre-botao sobre-botao-claro"><?= $iconeZap ?>Pedir no WhatsApp</a>
          <?php endif; ?>
        </div>
      </div>

      <?php if ($fotoOficial): ?>
        <div class="sobre-abertura-foto">
          <img src="<?= e($fotoOficial) ?>" srcset="<?= e((string) dd_imagem('/assets/img/sobre/oficial-640.webp')) ?> 640w, <?= e($fotoOficial) ?> 960w" sizes="(min-width: 64rem) 28rem, 86vw" alt="Mulher de avental da Dolce Delícias na cozinha, de braços cruzados e sorrindo, com bolo, pão e salgados na bancada à frente" width="960" height="1200" fetchpriority="high" decoding="async">
        </div>
      <?php endif; ?>
    </div>
  </section>

  <?php // Fatos curtos. No HTML o rótulo (dt) vem antes do valor (dd); o CSS põe o valor em cima. ?>
  <?php if ($numeros !== []): ?>
    <section class="sobre-fatos mx-auto max-w-7xl px-4 sm:px-6 lg:px-8" aria-label="A Dolce em poucas palavras">
      <dl>
        <?php foreach ($numeros as $numero): ?>
          <div>
            <dt><?= e((string) ($numero['rotulo'] ?? '')) ?></dt>
            <dd><?= e((string) ($numero['valor'] ?? '')) ?></dd>
          </div>
        <?php endforeach; ?>
      </dl>
    </section>
  <?php endif; ?>

  <?php // RF-31 — institucional: a história contada pela própria marca. ?>
  <?php if (!empty($institucional['texto'])): ?>
    <section id="institucional" class="sobre-cozinha" aria-labelledby="titulo-institucional">
      <div class="sobre-cozinha-grade mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <?php if ($fotoCozinha): ?>
          <div class="sobre-cozinha-foto">
            <img src="<?= e($fotoCozinha) ?>" srcset="<?= e((string) dd_imagem('/assets/img/sobre/topo-800.webp')) ?> 800w, <?= e($fotoCozinha) ?> 1400w" sizes="(min-width: 64rem) 34rem, 92vw" alt="Mãos modelando coxinha, com a massa e as coxinhas prontas na tábua" width="1400" height="1400" loading="lazy" decoding="async">
          </div>
        <?php endif; ?>
        <div class="sobre-cozinha-texto">
          <p class="sobre-rotulo">Nossa história</p>
          <h2 id="titulo-institucional">Começou com receitas feitas em casa</h2>
          <?php foreach ((array) $institucional['texto'] as $paragrafo): ?>
            <p><?= e((string) $paragrafo) ?></p>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <?php // RF-22 — portfólio ?>
  <?php if ($portfolio !== []): ?>
    <section id="portfolio" class="sobre-frentes" aria-labelledby="titulo-portfolio">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="sobre-centro">
          <p class="sobre-rotulo">O que fazemos</p>
          <h2 id="titulo-portfolio">Da festa ao lanche da escola</h2>
        </div>
        <ul class="sobre-frentes-lista">
          <?php foreach (array_values($portfolio) as $i => $frente): ?>
            <?php [$caminhoFoto, $altFoto] = $fotosPortfolio[$i] ?? ['', '']; ?>
            <li class="sobre-frente">
              <?php if ($foto = $fotoSobre($caminhoFoto)): ?>
                <img src="<?= e($foto) ?>" alt="<?= e($altFoto) ?>" width="800" height="600" loading="lazy" decoding="async">
              <?php endif; ?>
              <div class="sobre-frente-texto">
                <h3><?= e((string) ($frente['titulo'] ?? '')) ?></h3>
                <p><?= e((string) ($frente['texto'] ?? '')) ?></p>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
        <div class="sobre-centro mt-10">
          <a href="/#catalogo" class="sobre-botao sobre-botao-vermelho">Ver o catálogo</a>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <?php // RNF-11 — os valores da marca: a frase-tese e três colunas, sem ícones. ?>
  <?php if (!empty($institucional['valores'])): ?>
    <section id="valores" class="sobre-crencas" aria-labelledby="titulo-valores">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="sobre-centro">
          <p class="sobre-rotulo">No que acreditamos</p>
          <h2 id="titulo-valores"><?= e($chamada !== '' ? $chamada . '.' : 'No que acreditamos') ?></h2>
        </div>
        <ul class="sobre-crencas-lista">
          <?php foreach ($institucional['valores'] as $valor): ?>
            <li>
              <h3><?= e((string) ($valor['titulo'] ?? '')) ?></h3>
              <p><?= e((string) ($valor['texto'] ?? '')) ?></p>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </section>
  <?php endif; ?>

  <?php // RF-21 — história com datas. Linha do tempo em <ol>: é sequência de verdade. Só aparece com datas reais em data/empresa.php. ?>
  <?php if ($historia !== []): ?>
    <section id="historia" class="sobre-historia mx-auto max-w-7xl px-4 sm:px-6 lg:px-8" aria-labelledby="titulo-historia">
      <h2 id="titulo-historia">Nossa trajetória</h2>
      <ol class="linha-tempo">
        <?php foreach ($historia as $marco): ?>
          <li>
            <p class="linha-tempo-ano"><?= e((string) ($marco['ano'] ?? '')) ?></p>
            <h3><?= e((string) ($marco['titulo'] ?? '')) ?></h3>
            <p class="linha-tempo-texto"><?= e((string) ($marco['texto'] ?? '')) ?></p>
          </li>
        <?php endforeach; ?>
      </ol>
    </section>
  <?php endif; ?>

  <?php
  /*
   * Vídeo da marca (Reels do Instagram). Fica no player oficial do Instagram
   * porque o arquivo não está no site; o iframe só carrega quando a pessoa
   * chega perto dele (loading="lazy"). Para trocar o vídeo, troque o código
   * do post em $videoInstagram.
   */
  $videoInstagram = 'DFtaWFkPLeO';
  ?>
  <section id="video" class="sobre-video mx-auto max-w-7xl px-4 sm:px-6 lg:px-8" aria-labelledby="titulo-video">
    <div class="sobre-centro">
      <p class="sobre-rotulo">A Dolce de perto</p>
      <h2 id="titulo-video">Especialistas em lanche escolar</h2>
      <p class="sobre-centro-frase">Dá o play e conheça um pouco do nosso dia a dia.</p>
    </div>
    <div class="sobre-video-player">
      <iframe src="https://www.instagram.com/p/<?= e($videoInstagram) ?>/embed/" title="Vídeo da Dolce Delícias no Instagram" loading="lazy" allowfullscreen></iframe>
    </div>
    <p class="sobre-centro mt-5">
      <a href="https://www.instagram.com/p/<?= e($videoInstagram) ?>/" target="_blank" rel="noopener noreferrer" class="sobre-link"><?= $iconeInstagram ?>Ver no Instagram</a>
    </p>
  </section>

  <?php // O convite final: o que fazer depois de conhecer a Dolce. ?>
  <section class="sobre-convite mx-auto max-w-7xl px-4 sm:px-6 lg:px-8" aria-labelledby="titulo-convite">
    <div class="sobre-convite-caixa">
      <h2 id="titulo-convite">Vamos fazer o seu pedido?</h2>
      <p>
        <?php if ($lugares !== ''): ?><?= e($lugares) ?>. <?php endif; ?>Escolha no catálogo e feche pelo WhatsApp.
      </p>
      <div class="sobre-botoes">
        <a href="/#catalogo" class="sobre-botao sobre-botao-amarelo">Ver o catálogo</a>
        <?php if ($linkZap !== ''): ?>
          <a href="<?= e($linkZap) ?>" target="_blank" rel="noopener noreferrer" class="sobre-botao sobre-botao-claro"><?= $iconeZap ?>Pedir no WhatsApp</a>
        <?php endif; ?>
      </div>
      <?php if ($instagram !== ''): ?>
        <a href="https://www.instagram.com/<?= e($instagram) ?>/" target="_blank" rel="noopener noreferrer" class="sobre-link sobre-link-claro"><?= $iconeInstagram ?>@<?= e($instagram) ?></a>
      <?php endif; ?>
    </div>
  </section>
</div>

<?php include DD_BASE . '/partials/footer.php'; ?>
