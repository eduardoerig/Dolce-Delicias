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

$chamada          = rtrim(trim((string) ($institucional['chamada'] ?? '')), '.');

// O total de lojas é contado, nunca cadastrado: assim não diverge de
// data/units.php quando abrir a próxima.
$totalUnidades = count(dd_unidades());

$tituloPagina    = 'Sobre a Dolce Delícias — quem somos, o que fazemos e nossa história';
$descricaoPagina = 'Quem é a Dolce Delícias, como a padaria começou, o que ela faz e como atende escolas, faculdades, empresas e indústrias.';

include DD_BASE . '/partials/header.php';
?>

<?php
/*
 * Desenho: fotografia de salgado de verdade e faixas que se alternam —
 * marrom, claro, vermelho —, cantos retos e botões amarelos, como Unidades.
 * A ordem segue a conversa de quem chega:
 *
 *   1. herói marrom: a frase da marca à esquerda e a foto da coxinha sendo
 *      modelada à direita (no celular, a foto em cima). Os números no pé;
 *   2. "Quem somos": texto e a foto da equipe, com um bloco vermelho atrás;
 *   3. "O que fazemos": um cartão com foto por serviço;
 *   4. "No que acreditamos": a faixa vermelha em cartaz, um valor por linha;
 *   5. "Nossa história": a linha do tempo;
 *   6. o vídeo da marca no Instagram, no player oficial do Instagram.
 *
 * Texto nunca fica sobre comida: a foto tem coluna própria em todo tamanho.
 * Se um arquivo de foto não existir, a seção fica só com o texto. Todo o
 * texto vem de data/empresa.php.
 */
$fotoSobre = static fn (string $caminho): ?string => dd_imagem($caminho);

// Uma foto por serviço do portfólio, na ordem de data/empresa.php.
$fotosPortfolio = [
    ['/assets/img/sobre/encomendas-800.webp', 'Coxinhas, uma aberta mostrando o recheio de frango'],
    ['/assets/img/sobre/coffee-break-800.webp', 'Mesa de coffee break com doces'],
    ['/assets/img/sobre/balcao-800.webp', 'Vitrine do balcão com folhados'],
    ['/assets/img/sobre/escolar-800.webp', 'Crianças lanchando no pátio da escola'],
];

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
          Salgados, assados e doces para escolas, eventos e empresas, saindo da mesma cozinha para <?= e((string) $totalUnidades) ?> unidades.
        </p>

        <div class="sobre-topo-botoes">
          <a href="/#catalogo" class="botao-amarelo">Ver o catálogo</a>
          <a href="/unidades" class="botao-vazado">Nossas unidades</a>
        </div>

        <?php // Números da marca. No HTML o rótulo (dt) vem antes do número (dd); o CSS põe o número em cima. ?>
        <dl class="sobre-topo-numeros">
          <div>
            <dt>unidades</dt>
            <dd><?= e((string) $totalUnidades) ?></dd>
          </div>
          <?php foreach ($numeros as $numero): ?>
            <div>
              <dt><?= e((string) ($numero['rotulo'] ?? '')) ?></dt>
              <dd><?= e((string) ($numero['valor'] ?? '')) ?></dd>
            </div>
          <?php endforeach; ?>
        </dl>
      </div>
    </div>
  </section>

  <?php // RF-31 — institucional ?>
  <?php if (!empty($institucional['texto'])): ?>
    <section id="institucional" class="sobre-quem mx-auto max-w-7xl px-4 sm:px-6 lg:px-8" aria-labelledby="titulo-institucional">
      <div class="sobre-quem-texto">
        <h2 id="titulo-institucional">Quem somos</h2>
        <?php foreach ((array) $institucional['texto'] as $paragrafo): ?>
          <p><?= e((string) $paragrafo) ?></p>
        <?php endforeach; ?>
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

  <?php // RNF-11 — os valores da marca: cartaz tipográfico, um valor por linha, sem ícone. ?>
  <?php if (!empty($institucional['valores'])): ?>
    <section id="valores" class="sobre-valores" aria-labelledby="titulo-valores">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
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
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="0.5" fill="currentColor"/></svg>
        Ver no Instagram
      </a>
    </div>
    <div class="sobre-video-player">
      <iframe src="https://www.instagram.com/p/<?= e($videoInstagram) ?>/embed/" title="Vídeo da Dolce Delícias no Instagram" loading="lazy" allowfullscreen></iframe>
    </div>
  </section>
</div>

<?php include DD_BASE . '/partials/footer.php'; ?>
