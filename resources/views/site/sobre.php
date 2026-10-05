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
 * O desenho (detalhes abaixo, no HTML): herói claro com a foto da cozinha,
 * cartões com foto para os serviços, a faixa vermelha dos valores, a linha
 * do tempo e o pedido de orçamento, que é o que a página existe para gerar.
 *
 * Todo o texto vem de data/empresa.php. Aqui só existe estrutura: cada seção
 * some sozinha quando o dado dela está vazio.
 */

require_once DD_BASE . '/partials/bootstrap.php';

$empresa = dd_empresa();
$matriz  = dd_matriz();

$institucional = (array) ($empresa['institucional'] ?? []);
$numeros       = (array) ($empresa['numeros'] ?? []);
$historia      = (array) ($empresa['historia'] ?? []);
$portfolio     = (array) ($empresa['portfolio'] ?? []);
$paraEmpresas  = (array) ($empresa['empresas'] ?? []);

$chamada          = rtrim(trim((string) ($institucional['chamada'] ?? '')), '.');
$temEmpresas      = !empty($paraEmpresas['itens']) || trim((string) ($paraEmpresas['texto'] ?? '')) !== '';

// O total de lojas é contado, nunca cadastrado: assim não diverge de
// data/units.php quando abrir a próxima.
$totalUnidades = count(dd_unidades());

// WhatsApp da matriz com a conversa de orçamento já começada (RF-19). Número
// vazio ou de exemplo não vira botão — ver dd_whatsapp().
$zapMatriz     = dd_whatsapp($matriz);
$linkOrcamento = $zapMatriz !== ''
    ? 'https://wa.me/' . $zapMatriz . '?text=' . rawurlencode('Olá! Vim pelo site e quero um orçamento para a minha empresa.')
    : '';

/**
 * Desenho de cada valor da marca, pelo título (mesmo traço dos produtos):
 * coração para o amor, rolo de massa para o feito à mão, folha para o saudável.
 */
function dd_icone_valor(string $titulo): string
{
    $t = dd_ascii($titulo);
    $glifo = match (true) {
        str_contains($t, 'amor') => '<path d="M32 52S10 39 10 24a11 11 0 0 1 22-3 11 11 0 0 1 22 3c0 15-22 28-22 28Z" fill="#e1051e" stroke="#9e0315" stroke-width="1.8" stroke-linejoin="round"/><path d="M19 22a6 6 0 0 1 6-5" fill="none" stroke="#ff8a96" stroke-width="3" stroke-linecap="round"/>',
        str_contains($t, 'industrializ') => '<rect x="14" y="26" width="36" height="12" rx="6" fill="#e7a446" stroke="#a9561a" stroke-width="1.8"/><rect x="4" y="29" width="11" height="6" rx="3" fill="#7a2e12"/><rect x="49" y="29" width="11" height="6" rx="3" fill="#7a2e12"/><path d="M20 30h24" stroke="#f7c46a" stroke-width="2.5" stroke-linecap="round"/><ellipse cx="32" cy="50" rx="20" ry="5" fill="#fbf4ee" stroke="#c5ac98" stroke-width="1.6"/>',
        default => '<path d="M14 50C14 26 30 12 52 12c0 22-14 38-38 38Z" fill="#6aa84f" stroke="#3f7a32" stroke-width="1.8" stroke-linejoin="round"/><path d="M14 50 40 24" stroke="#3f7a32" stroke-width="2" stroke-linecap="round"/><path d="M24 40l-1-8M31 33l-1-8M24 40l8 1M31 33l8 1" stroke="#3f7a32" stroke-width="1.6" stroke-linecap="round"/>',
    };

    return '<svg viewBox="0 0 64 64" focusable="false">' . $glifo . '</svg>';
}

$tituloPagina    = 'Sobre a Dolce Delícias — história, portfólio e atendimento a empresas';
$descricaoPagina = 'Quem é a Dolce Delícias, como a padaria começou, o que ela faz e como atende escolas, faculdades, empresas e indústrias.';

include DD_BASE . '/partials/header.php';
?>

<?php
/*
 * Desenho: cantos retos e fotos grandes, como o resto do site. A ordem segue a
 * conversa de quem chega — quem são, o que fazem, no que acreditam, de onde
 * vieram e o que oferecem a empresas.
 *
 *   1. herói claro: a frase da marca à esquerda; à direita a foto das mãos na
 *      massa, com as coxinhas saltando do canto e a logo carimbada em cima.
 *      Embaixo, os números numa faixa marrom;
 *   2. "Quem somos": retrato da atendente e o texto ao lado;
 *   3. "O que fazemos": um cartão com foto para cada serviço;
 *   4. "No que acreditamos": a faixa vermelha, três colunas;
 *   5. "Nossa história": linha do tempo (deitada no computador);
 *   6. empresas: cartões com foto e o pedido de orçamento.
 *
 * Cada foto vem de assets/img/; se o arquivo não existir, o cartão fica só
 * com o texto. Todo o texto vem de data/empresa.php.
 */
$fotoSobre = static fn (string $caminho): ?string => dd_imagem($caminho);

// Uma foto por serviço do portfólio e por atendimento a empresas, na ordem de data/empresa.php.
$fotosPortfolio = [
    ['/assets/img/produtos/combo-festa.jpg', 'Bandeja de salgados variados'],
    ['/assets/img/sobre/coffee-break-800.webp', 'Mesa de coffee break com bolinhos'],
    ['/assets/img/sobre/vitrine-800.webp', 'Vitrine de bolos e doces da loja'],
    ['/assets/img/produtos/mini-sanduiche.jpg', 'Mini sanduíches'],
];
$fotosEmpresa = [
    '/assets/img/produtos/mini-esfirras.jpg',
    '/assets/img/produtos/mini-hamburguer.jpg',
    '/assets/img/produtos/salgados-fritos.jpg',
];

$selo = null;
foreach (['svg', 'webp', 'png'] as $extensao) {
    $selo ??= dd_imagem("/assets/img/marca/selo.{$extensao}");
}
$selo ??= dd_imagem('/assets/img/logo.svg');
$fotoMassa    = dd_imagem('/assets/img/sobre/massa-1000.webp');
$coxinhas     = dd_imagem('/assets/img/recortes/coxinhas-760.webp');
$fotoQuemSomos = dd_imagem('/assets/img/sobre/atendente-960.webp');
?>

<div class="pagina-institucional pagina-sobre bg-farinha">

  <section class="sobre-heroi" aria-labelledby="titulo-sobre">
    <div class="sobre-heroi-grade mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="sobre-heroi-texto">
        <nav aria-label="Você está aqui" class="flex items-center gap-2 text-sm font-semibold text-crust">
          <a href="/" class="rounded underline-offset-4 hover:underline">Início</a>
          <span aria-hidden="true">/</span>
          <span class="text-base-content" aria-current="page">A empresa</span>
        </nav>

        <h1 id="titulo-sobre" class="sobre-heroi-titulo">
          <span class="sr-only">A Dolce Delícias: </span><?= e($chamada !== '' ? $chamada . '.' : 'A Dolce Delícias.') ?>
        </h1>

        <p class="sobre-heroi-frase">
          Salgados, assados e doces para escolas, eventos e empresas, saindo da mesma cozinha para <?= e((string) $totalUnidades) ?> unidades.
        </p>

        <div class="mt-7 flex flex-wrap gap-3">
          <a href="/#catalogo" class="botao-primario">Ver o catálogo</a>
          <a href="/unidades" class="botao-secundario">Nossas unidades</a>
        </div>
      </div>

      <?php if ($fotoMassa): ?>
        <div class="sobre-heroi-arte" aria-hidden="true">
          <img class="sobre-heroi-foto" src="<?= e($fotoMassa) ?>" srcset="<?= e((string) dd_imagem('/assets/img/sobre/massa-640.webp')) ?> 640w, <?= e($fotoMassa) ?> 1000w" sizes="(min-width: 64rem) 30rem, 82vw" alt="" width="1000" height="1250" fetchpriority="high" decoding="async">
          <?php if ($coxinhas): ?>
            <img class="sobre-heroi-coxinhas" src="<?= e($coxinhas) ?>" srcset="<?= e((string) dd_imagem('/assets/img/recortes/coxinhas-420.webp')) ?> 420w, <?= e($coxinhas) ?> 760w" sizes="(min-width: 64rem) 19rem, 46vw" alt="" width="760" height="725" decoding="async">
          <?php endif; ?>
          <?php if ($selo): ?>
            <div class="sobre-heroi-selo"><img src="<?= e($selo) ?>" alt="" width="300" height="160" decoding="async"></div>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>

    <?php // Números da marca. No HTML o rótulo (dt) vem antes do número (dd); o CSS põe o número em cima. ?>
    <div class="sobre-numeros">
      <dl class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
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
  </section>

  <?php // RF-31 — institucional ?>
  <?php if (!empty($institucional['texto'])): ?>
    <section id="institucional" class="sobre-quem mx-auto max-w-7xl px-4 sm:px-6 lg:px-8" aria-labelledby="titulo-institucional">
      <?php if ($fotoQuemSomos): ?>
        <img class="sobre-quem-foto" src="<?= e($fotoQuemSomos) ?>" srcset="<?= e((string) dd_imagem('/assets/img/sobre/atendente-640.webp')) ?> 640w, <?= e($fotoQuemSomos) ?> 960w" sizes="(min-width: 64rem) 32rem, 100vw" alt="Atendente servindo pães no balcão" width="960" height="1200" loading="lazy" decoding="async">
      <?php endif; ?>
      <div class="sobre-quem-texto">
        <h2 id="titulo-institucional">Quem somos</h2>
        <?php foreach ((array) $institucional['texto'] as $paragrafo): ?>
          <p><?= e((string) $paragrafo) ?></p>
        <?php endforeach; ?>
      </div>
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

  <?php // RNF-11 — os valores da marca ?>
  <?php if (!empty($institucional['valores'])): ?>
    <section id="valores" class="sobre-valores" aria-labelledby="titulo-valores">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <h2 id="titulo-valores">No que acreditamos</h2>
        <ul class="sobre-valores-lista">
          <?php foreach ($institucional['valores'] as $valor): ?>
            <li>
              <span class="valor-icone" aria-hidden="true"><?= dd_icone_valor((string) ($valor['titulo'] ?? '')) ?></span>
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

  <?php // RF-19 — para empresas e indústrias: cartões com foto e o pedido de orçamento. ?>
  <?php if ($temEmpresas): ?>
    <section id="empresas" class="sobre-empresas mx-auto max-w-7xl px-4 sm:px-6 lg:px-8" aria-labelledby="titulo-empresas">
      <h2 id="titulo-empresas" class="max-w-2xl"><?= e((string) ($paraEmpresas['chamada'] ?? 'Para a sua empresa')) ?></h2>
      <?php if (trim((string) ($paraEmpresas['texto'] ?? '')) !== ''): ?>
        <p class="mt-4 max-w-2xl text-lg leading-relaxed text-crust"><?= e((string) $paraEmpresas['texto']) ?></p>
      <?php endif; ?>

      <?php if (!empty($paraEmpresas['itens'])): ?>
        <ul class="sobre-empresas-lista">
          <?php foreach (array_values($paraEmpresas['itens']) as $i => $item): ?>
            <li class="sobre-servico">
              <?php if ($foto = $fotoSobre($fotosEmpresa[$i] ?? '')): ?>
                <img src="<?= e($foto) ?>" alt="" width="1200" height="900" loading="lazy" decoding="async">
              <?php endif; ?>
              <h3><?= e((string) ($item['titulo'] ?? '')) ?></h3>
              <p><?= e((string) ($item['texto'] ?? '')) ?></p>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>

      <?php // Pedido de orçamento: marrom com halo vermelho e a torre de nuggets
         // recortada saindo pelo topo, à direita. O texto fica à esquerda. ?>
      <div class="placa-empresas placa-empresas-recorte mt-16">
        <?php $nuggets = dd_imagem('/assets/img/recortes/nuggets-700.webp'); ?>
        <?php if ($nuggets): ?>
          <img class="placa-recorte" src="<?= e($nuggets) ?>" srcset="<?= e((string) dd_imagem('/assets/img/recortes/nuggets-420.webp')) ?> 420w, <?= e($nuggets) ?> 700w" sizes="(min-width: 48rem) 22rem, 9rem" alt="" width="700" height="797" loading="lazy" decoding="async">
        <?php endif; ?>
        <div class="placa-recorte-texto">
          <p class="fonte-display text-2xl sm:text-3xl">Peça um orçamento</p>
          <p class="mt-1">A matriz responde pelas <?= e((string) $totalUnidades) ?> unidades.</p>
          <div class="mt-5">
          <?php if ($linkOrcamento !== ''): ?>
            <a href="<?= e($linkOrcamento) ?>" target="_blank" rel="noopener noreferrer" class="botao-primario">
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 11.5a8.4 8.4 0 0 1-12.4 7.4L3 20.5l1.7-5.4A8.5 8.5 0 1 1 21 11.5Z"/></svg>
              Pedir orçamento no WhatsApp
            </a>
          <?php else: ?>
            <?php // INTEGRAÇÃO FUTURA: cadastre o WhatsApp da matriz no painel e o botão liga sozinho. ?>
            <span class="botao-desligado">WhatsApp em breve</span>
          <?php endif; ?>
          </div>
        </div>
      </div>
    </section>
  <?php endif; ?>
</div>

<?php include DD_BASE . '/partials/footer.php'; ?>
