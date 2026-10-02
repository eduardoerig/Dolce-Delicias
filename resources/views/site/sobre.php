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
 * O desenho segue as páginas institucionais grandes: abertura com foto,
 * seções em zigue-zague e cartões de foto para empresas (detalhes abaixo, no
 * HTML). O pedido de orçamento é o único bloco vermelho, porque é o pedido que
 * a página existe para gerar.
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
$temInstitucional = !empty($institucional['texto']) || !empty($institucional['valores']);
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
 * Desenho inspirado nas páginas institucionais grandes (ex.: "Bayer Global"):
 * uma abertura com texto curto e foto grande; depois seções em zigue-zague,
 * cada uma com uma foto de um lado e um título, um texto e um único link do
 * outro, alternando o lado; por fim, os atendimentos para empresas em cartões
 * altos de foto com o título por cima.
 *
 * Cada foto vem de assets/img/; se o arquivo não existir, a seção mostra só o
 * texto (o zigue-zague vira uma coluna).
 */
$fotoSobre = static fn (string $caminho): ?string => dd_imagem($caminho);

$cartoesEmpresa = [
    '/assets/img/empresa/coffee-break.jpg',
    '/assets/img/produtos/salgados-fritos.jpg',
    '/assets/img/produtos/combo-festa.jpg',
];
?>

<div class="pagina-institucional bg-farinha">

  <?php
  /*
   * Herói "manifesto": a frase da marca em letras de cartaz, com o garfo e o
   * croquete espetados numa coluna à direita. A linha do meio leva uma faixa
   * vermelha que atravessa a tela inteira e passa por trás do garfo. Fundo
   * claro: é o único herói assim no site.
   *
   * A frase vem de data/empresa.php ('chamada'). "Comida de verdade, feita
   * todo dia" vira três linhas: "Comida de" / "verdade," (na faixa) / "feita
   * todo dia." — quebra na vírgula, e a palavra antes dela ganha a faixa.
   */
  $fraseManifesto = $chamada !== '' ? $chamada . '.' : 'A Dolce Delícias.';
  $partesManifesto = array_map('trim', explode(',', $fraseManifesto, 2));
  $linhasManifesto = [];
  if (count($partesManifesto) === 2) {
      $palavras = preg_split('/\s+/', $partesManifesto[0]);
      $faixa    = array_pop($palavras) . ',';
      if ($palavras !== []) $linhasManifesto[] = ['texto' => implode(' ', $palavras), 'faixa' => false];
      $linhasManifesto[] = ['texto' => $faixa, 'faixa' => true];
      $linhasManifesto[] = ['texto' => $partesManifesto[1], 'faixa' => false];
  } else {
      $linhasManifesto[] = ['texto' => $fraseManifesto, 'faixa' => true];
  }
  $garfo     = dd_imagem('/assets/img/recortes/garfo-600.webp');
  $garfoMini = dd_imagem('/assets/img/recortes/garfo-360.webp');
  ?>
  <section class="heroi-manifesto" aria-labelledby="titulo-sobre">
    <div class="heroi-manifesto-grade mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="heroi-manifesto-texto">
        <nav aria-label="Você está aqui" class="flex items-center gap-2 text-sm font-semibold text-crust">
          <a href="/" class="rounded underline-offset-4 hover:underline">Início</a>
          <span aria-hidden="true">/</span>
          <span class="text-base-content" aria-current="page">A empresa</span>
        </nav>

        <h1 id="titulo-sobre" class="heroi-manifesto-titulo">
          <span class="sr-only">A Dolce Delícias: </span>
          <?php foreach ($linhasManifesto as $linha): ?>
            <span class="<?= $linha['faixa'] ? 'linha linha-faixa' : 'linha' ?>"><?= e($linha['texto']) ?></span>
          <?php endforeach; ?>
        </h1>

        <p class="heroi-manifesto-frase">
          Salgados, assados e doces para escolas, eventos e empresas, saindo da mesma cozinha para <?= e((string) $totalUnidades) ?> unidades.
        </p>

        <div class="mt-6 flex flex-wrap gap-3">
          <a href="/#catalogo" class="botao-primario">Ver o catálogo</a>
          <a href="/unidades" class="botao-secundario">Nossas unidades</a>
        </div>
      </div>

      <?php if ($garfo): ?>
        <div class="heroi-manifesto-arte" aria-hidden="true">
          <img src="<?= e($garfo) ?>" srcset="<?= e((string) $garfoMini) ?> 360w, <?= e($garfo) ?> 600w" sizes="(min-width: 64rem) 20rem, 9rem" alt="" width="600" height="1159" fetchpriority="high" decoding="async">
        </div>
      <?php endif; ?>
    </div>

    <?php // Números da marca no pé do herói. No HTML o rótulo (dt) vem antes do número (dd); o CSS põe o número em cima. ?>
    <div class="heroi-manifesto-numeros mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <dl class="numeros">
        <div>
          <dt>unidades</dt>
          <dd><?= e((string) $totalUnidades) ?></dd>
        </div>
        <?php foreach ($numeros as $numero): ?>
          <?php $valorNumero = (string) ($numero['valor'] ?? ''); ?>
          <div>
            <dt><?= e((string) ($numero['rotulo'] ?? '')) ?></dt>
            <dd<?= mb_strlen($valorNumero) > 6 ? ' class="numero-longo"' : '' ?>><?= e($valorNumero) ?></dd>
          </div>
        <?php endforeach; ?>
      </dl>
    </div>
  </section>

  <div>

    <?php // RF-31 — institucional ?>
    <?php if ($temInstitucional): ?>
      <section id="institucional" class="zigue" aria-labelledby="titulo-institucional">
        <?php if ($foto = $fotoSobre('/assets/img/empresa/atendente.jpg')): ?>
          <img src="<?= e($foto) ?>" alt="Atendente servindo pães no balcão" class="zigue-foto" width="1600" height="1000" loading="lazy" decoding="async">
        <?php endif; ?>
        <div class="zigue-texto">
          <h2 id="titulo-institucional">Quem somos</h2>
          <?php foreach ((array) ($institucional['texto'] ?? []) as $paragrafo): ?>
            <p><?= e((string) $paragrafo) ?></p>
          <?php endforeach; ?>

        </div>
      </section>
    <?php endif; ?>

    <?php // RF-22 — portfólio ?>
    <?php if ($portfolio !== []): ?>
      <section id="portfolio" class="zigue" aria-labelledby="titulo-portfolio">
        <?php if ($foto = $fotoSobre('/assets/img/produtos/combo-festa.jpg')): ?>
          <img src="<?= e($foto) ?>" alt="Bandeja de salgados variados para festa" class="zigue-foto" width="1200" height="900" loading="lazy" decoding="async">
        <?php endif; ?>
        <div class="zigue-texto">
          <h2 id="titulo-portfolio">O que fazemos</h2>
          <ul class="lista-sobre">
            <?php foreach ($portfolio as $servico): ?>
              <li>
                <h3><?= e((string) ($servico['titulo'] ?? '')) ?></h3>
                <p><?= e((string) ($servico['texto'] ?? '')) ?></p>
              </li>
            <?php endforeach; ?>
          </ul>
          <a href="/#catalogo" class="botao-primario mt-8">Ver o catálogo</a>
        </div>
      </section>
    <?php endif; ?>

    <?php // RNF-11 — os valores da marca ?>
    <?php if (!empty($institucional['valores'])): ?>
      <section id="valores" class="zigue" aria-labelledby="titulo-valores">
        <?php if ($foto = $fotoSobre('/assets/img/empresa/vitrine.jpg')): ?>
          <img src="<?= e($foto) ?>" alt="Vitrine com bolos e doces" class="zigue-foto" width="1600" height="1000" loading="lazy" decoding="async">
        <?php endif; ?>
        <div class="zigue-texto">
          <h2 id="titulo-valores">No que acreditamos</h2>
          <ul class="lista-sobre">
            <?php foreach ($institucional['valores'] as $valor): ?>
              <li class="lista-sobre-icone">
                <span class="valor-icone" aria-hidden="true"><?= dd_icone_valor((string) ($valor['titulo'] ?? '')) ?></span>
                <div>
                  <h3><?= e((string) ($valor['titulo'] ?? '')) ?></h3>
                  <p><?= e((string) ($valor['texto'] ?? '')) ?></p>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </section>
    <?php endif; ?>

    <?php // RF-21 — história. Linha do tempo em <ol>: é sequência de verdade. ?>
    <?php if ($historia !== []): ?>
      <section id="historia" class="zigue" aria-labelledby="titulo-historia">
        <?php if ($foto = $fotoSobre('/assets/img/unidades/matriz.jpg')): ?>
          <img src="<?= e($foto) ?>" alt="Balcão da matriz com pães e doces" class="zigue-foto" width="1600" height="1000" loading="lazy" decoding="async">
        <?php endif; ?>
        <div class="zigue-texto">
          <h2 id="titulo-historia">Nossa história</h2>
          <ol class="marcos mt-6">
            <?php foreach ($historia as $marco): ?>
              <li class="marco">
                <p class="marco-ano"><?= e((string) ($marco['ano'] ?? '')) ?></p>
                <h3 class="font-sans text-lg font-bold"><?= e((string) ($marco['titulo'] ?? '')) ?></h3>
                <p class="mt-1 text-[0.9375rem] leading-relaxed text-crust"><?= e((string) ($marco['texto'] ?? '')) ?></p>
              </li>
            <?php endforeach; ?>
          </ol>
        </div>
      </section>
    <?php endif; ?>

    <?php // RF-19 — para empresas e indústrias: cartões de foto e o pedido de orçamento. ?>
    <?php if ($temEmpresas): ?>
      <section id="empresas" class="sobre-empresas mx-auto max-w-7xl px-4 sm:px-6 lg:px-8" aria-labelledby="titulo-empresas">
        <h2 id="titulo-empresas" class="max-w-2xl"><?= e((string) ($paraEmpresas['chamada'] ?? 'Para a sua empresa')) ?></h2>
        <?php if (trim((string) ($paraEmpresas['texto'] ?? '')) !== ''): ?>
          <p class="mt-4 max-w-2xl text-lg leading-relaxed text-crust"><?= e((string) $paraEmpresas['texto']) ?></p>
        <?php endif; ?>

        <?php if (!empty($paraEmpresas['itens'])): ?>
          <ul class="cartoes-empresa">
            <?php foreach (array_values($paraEmpresas['itens']) as $i => $item): ?>
              <?php $fotoCartao = $fotoSobre($cartoesEmpresa[$i] ?? ''); ?>
              <li class="cartao-empresa<?= $fotoCartao ? '' : ' cartao-empresa-sem-foto' ?>">
                <?php if ($fotoCartao): ?>
                  <img src="<?= e($fotoCartao) ?>" alt="" width="1200" height="900" loading="lazy" decoding="async">
                <?php endif; ?>
                <div class="cartao-empresa-texto">
                  <h3><?= e((string) ($item['titulo'] ?? '')) ?></h3>
                  <p><?= e((string) ($item['texto'] ?? '')) ?></p>
                </div>
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
</div>

<?php include DD_BASE . '/partials/footer.php'; ?>
