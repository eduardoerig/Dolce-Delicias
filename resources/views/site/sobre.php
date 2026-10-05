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
 * Desenho: fotografia de salgado de verdade e faixas que se alternam —
 * marrom, claro, vermelho —, cantos retos e botões amarelos, como Unidades.
 * A ordem segue a conversa de quem chega:
 *
 *   1. herói marrom: a frase da marca à esquerda e a foto da coxinha sendo
 *      modelada à direita (no celular, a foto em cima). Fatos curtos no pé;
 *   2. "Feita do zero": a história contada pela própria marca, a foto
 *      oficial com um bloco vermelho atrás e a assinatura em destaque;
 *   3. "O que fazemos": um cartão com foto para cada frente;
 *   4. "No que acreditamos": a faixa vermelha, um valor por linha;
 *   5. "Nossa história": a linha do tempo — só aparece com datas reais;
 *   6. o vídeo da marca no Instagram, no player oficial do Instagram;
 *   7. o convite final: catálogo, WhatsApp e Instagram.
 *
 * Texto nunca fica sobre comida: a foto tem coluna própria em todo tamanho.
 * Se um arquivo de foto não existir, a seção fica só com o texto. Todo o
 * texto vem de data/empresa.php.
 */
$fotoSobre = static fn (string $caminho): ?string => dd_imagem($caminho);

// Uma foto por serviço do portfólio, na ordem de data/empresa.php.
$fotosPortfolio = [
    ['/assets/img/sobre/encomendas-800.webp', 'Coxinhas, uma aberta mostrando o recheio de frango'],
    ['/assets/img/sobre/escolar-800.webp', 'Crianças lanchando no pátio da escola'],
    ['/assets/img/sobre/balcao-800.webp', 'Vitrine do balcão com folhados'],
];

$iconeZap = '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 11.5a8.4 8.4 0 0 1-12.4 7.4L3 20.5l1.7-5.4A8.5 8.5 0 1 1 21 11.5Z"/></svg>';
$iconeInstagram = '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="0.5" fill="currentColor"/></svg>';

$fotoTopo     = dd_imagem('/assets/img/sobre/topo-1400.webp');
// Foto oficial da marca (post do Instagram da Dolce, nov/2025).
$fotoEquipe   = dd_imagem('/assets/img/sobre/oficial-960.webp');
?>

<div class="pagina-institucional pagina-sobre bg-farinha">

  <section class="sobre-topo" aria-labelledby="titulo-sobre">
    <div class="sobre-topo-grade">
      <?php if ($fotoTopo): ?>
        <div class="sobre-topo-foto">
          <img src="<?= e($fotoTopo) ?>" srcset="<?= e((string) dd_imagem('/assets/img/sobre/topo-800.webp')) ?> 800w, <?= e($fotoTopo) ?> 1400w" sizes="(min-width: 64rem) 52vw, 100vw" alt="Mãos modelando coxinha, com a massa e as coxinhas prontas na tábua" width="1400" height="1400" fetchpriority="high" decoding="async">
        </div>
      <?php endif; ?>

      <div class="sobre-topo-texto">
        <nav aria-label="Você está aqui" class="flex items-center gap-2 text-sm font-semibold">
          <a href="/" class="rounded underline-offset-4 hover:underline">Início</a>
          <span aria-hidden="true">/</span>
          <span aria-current="page">A empresa</span>
        </nav>

        <h1 id="titulo-sobre" class="sobre-topo-titulo">
          <span class="sr-only">A Dolce Delícias: </span><?= e($chamada !== '' ? $chamada . '.' : 'A Dolce Delícias.') ?>
        </h1>

        <p class="sobre-topo-frase">
          Salgados artesanais, pães e mini pizzas feitos do zero em Toledo, para festas, escolas e o café do dia a dia.
        </p>

        <div class="sobre-topo-botoes">
          <a href="/#catalogo" class="botao-amarelo">Ver o catálogo</a>
          <?php if ($linkZap !== ''): ?>
            <a href="<?= e($linkZap) ?>" target="_blank" rel="noopener noreferrer" class="botao-vazado"><?= $iconeZap ?>Pedir no WhatsApp</a>
          <?php else: ?>
            <a href="/unidades" class="botao-vazado">Nossas unidades</a>
          <?php endif; ?>
        </div>

        <?php // Fatos curtos. No HTML o rótulo (dt) vem antes do valor (dd); o CSS põe o valor em cima. ?>
        <?php if ($numeros !== []): ?>
          <dl class="sobre-topo-numeros">
            <?php foreach ($numeros as $numero): ?>
              <div>
                <dt><?= e((string) ($numero['rotulo'] ?? '')) ?></dt>
                <dd><?= e((string) ($numero['valor'] ?? '')) ?></dd>
              </div>
            <?php endforeach; ?>
          </dl>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <?php // RF-31 — institucional: a história contada pela própria marca e a assinatura. ?>
  <?php if (!empty($institucional['texto'])): ?>
    <section id="institucional" class="sobre-quem mx-auto max-w-7xl px-4 sm:px-6 lg:px-8" aria-labelledby="titulo-institucional">
      <div class="sobre-quem-texto">
        <h2 id="titulo-institucional">Feita do zero</h2>
        <?php foreach ((array) $institucional['texto'] as $paragrafo): ?>
          <p><?= e((string) $paragrafo) ?></p>
        <?php endforeach; ?>
        <?php if ($assinatura !== ''): ?>
          <p class="sobre-assinatura"><?= e($assinatura) ?>.</p>
        <?php endif; ?>
      </div>
      <?php if ($fotoEquipe): ?>
        <div class="sobre-quem-foto">
          <img src="<?= e($fotoEquipe) ?>" srcset="<?= e((string) dd_imagem('/assets/img/sobre/oficial-640.webp')) ?> 640w, <?= e($fotoEquipe) ?> 960w" sizes="(min-width: 64rem) 30rem, 90vw" alt="Mulher de avental da Dolce Delícias na cozinha, de braços cruzados e sorrindo, com bolo, pão e salgados na bancada à frente" width="960" height="1200" loading="lazy" decoding="async">
        </div>
      <?php endif; ?>
    </section>
  <?php endif; ?>

  <?php // RF-22 — portfólio ?>
  <?php if ($portfolio !== []): ?>
    <section id="portfolio" class="sobre-servicos" aria-labelledby="titulo-portfolio">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="sobre-cabeca">
          <h2 id="titulo-portfolio">O que fazemos</h2>
          <a href="/#catalogo" class="botao-primario">Ver o catálogo</a>
        </div>
        <ul class="sobre-servicos-lista">
          <?php foreach (array_values($portfolio) as $i => $servico): ?>
            <?php [$caminhoFoto, $altFoto] = $fotosPortfolio[$i] ?? ['', '']; ?>
            <li class="sobre-servico">
              <?php if ($foto = $fotoSobre($caminhoFoto)): ?>
                <img src="<?= e($foto) ?>" alt="<?= e($altFoto) ?>" width="800" height="600" loading="lazy" decoding="async">
              <?php endif; ?>
              <h3><?= e((string) ($servico['titulo'] ?? '')) ?></h3>
              <p><?= e((string) ($servico['texto'] ?? '')) ?></p>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </section>
  <?php endif; ?>

  <?php // RNF-11 — os valores da marca: um por linha, sem ícone. ?>
  <?php if (!empty($institucional['valores'])): ?>
    <section id="valores" class="sobre-valores" aria-labelledby="titulo-valores">
      <div class="sobre-valores-grade mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <h2 id="titulo-valores">No que acreditamos</h2>
        <ul class="sobre-valores-lista">
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

  <?php // RF-21 — história. Linha do tempo em <ol>: é sequência de verdade. ?>
  <?php if ($historia !== []): ?>
    <section id="historia" class="sobre-historia mx-auto max-w-7xl px-4 sm:px-6 lg:px-8" aria-labelledby="titulo-historia">
      <h2 id="titulo-historia">Nossa história</h2>
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
    <div class="sobre-video-texto">
      <h2 id="titulo-video">A Dolce de perto</h2>
      <p>Nossa empresa é especializada em lanches escolares. Dá o play e conheça um pouco do nosso dia a dia.</p>
      <a href="https://www.instagram.com/p/<?= e($videoInstagram) ?>/" target="_blank" rel="noopener noreferrer" class="sobre-video-link">
        <?= $iconeInstagram ?>
        Ver no Instagram
      </a>
    </div>
    <div class="sobre-video-player">
      <iframe src="https://www.instagram.com/p/<?= e($videoInstagram) ?>/embed/" title="Vídeo da Dolce Delícias no Instagram" loading="lazy" allowfullscreen></iframe>
    </div>
  </section>

  <?php // O convite final: o que fazer depois de conhecer a Dolce. ?>
  <section class="sobre-cta mx-auto max-w-7xl px-4 sm:px-6 lg:px-8" aria-labelledby="titulo-cta">
    <div class="sobre-cta-caixa">
      <div class="sobre-cta-texto">
        <h2 id="titulo-cta">Vamos fazer o seu pedido?</h2>
        <p>
          <?php if ($lugares !== ''): ?><?= e($lugares) ?>. <?php endif; ?>Escolha no catálogo e feche pelo WhatsApp.
        </p>
      </div>
      <div class="sobre-cta-acoes">
        <a href="/#catalogo" class="botao-amarelo">Ver o catálogo</a>
        <?php if ($linkZap !== ''): ?>
          <a href="<?= e($linkZap) ?>" target="_blank" rel="noopener noreferrer" class="botao-vazado"><?= $iconeZap ?>Pedir no WhatsApp</a>
        <?php endif; ?>
        <?php if ($instagram !== ''): ?>
          <a href="https://www.instagram.com/<?= e($instagram) ?>/" target="_blank" rel="noopener noreferrer" class="sobre-cta-link"><?= $iconeInstagram ?>@<?= e($instagram) ?></a>
        <?php endif; ?>
      </div>
    </div>
  </section>
</div>

<?php include DD_BASE . '/partials/footer.php'; ?>
