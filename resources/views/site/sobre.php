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
 * Mesmo desenho de catálogo, produto, carrinho e unidades: faixa com o caminho
 * e o título, área clara com cards brancos. O bloco "Para empresas" é o único
 * vermelho da página, porque é o pedido que ela existe para gerar.
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

<div class="bg-farinha">

  <!-- Faixa: caminho, título e quem é, numa frase -->
  <div class="faixa-pagina border-b border-linha bg-polvilho">
    <div class="mx-auto grid max-w-7xl items-center gap-8 px-4 py-8 sm:px-6 lg:grid-cols-[minmax(0,1fr)_20rem] lg:px-8">
      <div>
      <nav aria-label="Você está aqui" class="flex items-center gap-2 text-sm font-semibold text-crust">
        <a href="/" class="rounded hover:text-brand-escuro">Início</a>
        <span aria-hidden="true">/</span>
        <span class="text-base-content" aria-current="page">A empresa</span>
      </nav>
      <h1 class="mt-3 text-4xl sm:text-5xl">A Dolce Delícias</h1>
      <p class="mt-2 max-w-2xl text-crust">
        <?php if ($chamada !== ''): ?><?= e($chamada) ?>. <?php endif; ?>
        Salgados, assados e doces para escolas, eventos e empresas, em <?= e((string) $totalUnidades) ?> unidades.
      </p>

      </div>

      <?php
      // A foto da cozinha (assets/img/empresa/); sem ela, a fachada desenhada.
      // Só no computador, onde sobra a lateral.
      $fotoEmpresa = dd_imagem('/assets/img/empresa/maos-na-massa.jpg');
      ?>
      <?php if ($fotoEmpresa): ?>
        <img src="<?= e($fotoEmpresa) ?>" alt="Mãos sovando massa sobre a bancada enfarinhada" class="faixa-foto hidden lg:block" width="1600" height="1000" decoding="async">
      <?php else: ?>
        <?php $fachadaClasse = 'hidden w-full lg:block'; include DD_BASE . '/partials/ilustra-fachada.php'; ?>
      <?php endif; ?>
    </div>
  </div>

  <?php
  /*
   * Cada seção é uma "trilha": à esquerda o título e uma frase que diz o que
   * ela responde; à direita o conteúdo. No computador o título fica parado
   * enquanto o conteúdo rola. Ordem pela pergunta de quem chega: quem são,
   * o que fazem, no que acreditam, de onde vieram — e, por fim, o convite
   * para empresas.
   */
  ?>
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

    <?php // RF-31 — institucional ?>
    <?php if ($temInstitucional): ?>
      <section id="institucional" class="trilha" aria-labelledby="titulo-institucional">
        <header class="trilha-cabeca">
          <h2 id="titulo-institucional">Quem somos</h2>
        </header>
        <div>
          <?php foreach ((array) ($institucional['texto'] ?? []) as $paragrafo): ?>
            <p class="mb-4 max-w-prose text-lg leading-relaxed text-crust"><?= e((string) $paragrafo) ?></p>
          <?php endforeach; ?>

          <?php // No HTML o rótulo (dt) vem antes do número (dd); o CSS põe o número em cima. ?>
          <dl class="numeros">
            <div>
              <dt>unidades</dt>
              <dd><?= e((string) $totalUnidades) ?></dd>
            </div>
            <?php foreach ($numeros as $numero): ?>
              <?php $valorNumero = (string) ($numero['valor'] ?? ''); ?>
              <div>
                <dt><?= e((string) ($numero['rotulo'] ?? '')) ?></dt>
                <?php // Valor comprido (ex.: "mais de 30") desce um tamanho para não quebrar no meio. ?>
                <dd<?= mb_strlen($valorNumero) > 6 ? ' class="numero-longo"' : '' ?>><?= e($valorNumero) ?></dd>
              </div>
            <?php endforeach; ?>
          </dl>
        </div>
      </section>
    <?php endif; ?>

    <?php // RF-22 — portfólio ?>
    <?php if ($portfolio !== []): ?>
      <section id="portfolio" class="trilha" aria-labelledby="titulo-portfolio">
        <header class="trilha-cabeca">
          <h2 id="titulo-portfolio">O que fazemos</h2>
        </header>
        <div>
          <ul class="servicos">
            <?php foreach ($portfolio as $servico): ?>
              <li>
                <h3 class="font-sans text-lg font-bold"><?= e((string) ($servico['titulo'] ?? '')) ?></h3>
                <p class="mt-2 text-[0.9375rem] leading-relaxed text-crust"><?= e((string) ($servico['texto'] ?? '')) ?></p>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </section>
    <?php endif; ?>

    <?php // RNF-11 — os valores da marca. ?>
    <?php if (!empty($institucional['valores'])): ?>
      <section id="valores" class="trilha" aria-labelledby="titulo-valores">
        <header class="trilha-cabeca">
          <h2 id="titulo-valores">No que acreditamos</h2>
        </header>
        <ul class="valores">
          <?php foreach ($institucional['valores'] as $valor): ?>
            <li>
              <span class="valor-icone" aria-hidden="true"><?= dd_icone_valor((string) ($valor['titulo'] ?? '')) ?></span>
              <h3 class="mt-3 font-sans text-lg font-bold"><?= e((string) ($valor['titulo'] ?? '')) ?></h3>
              <p class="mt-2 text-[0.9375rem] leading-relaxed text-crust"><?= e((string) ($valor['texto'] ?? '')) ?></p>
            </li>
          <?php endforeach; ?>
        </ul>
      </section>
    <?php endif; ?>

    <?php // RF-21 — história. Linha do tempo em <ol>: é sequência de verdade. ?>
    <?php if ($historia !== []): ?>
      <section id="historia" class="trilha" aria-labelledby="titulo-historia">
        <header class="trilha-cabeca">
          <h2 id="titulo-historia">Nossa história</h2>
        </header>
        <ol class="marcos">
          <?php foreach ($historia as $marco): ?>
            <li class="marco">
              <p class="marco-ano"><?= e((string) ($marco['ano'] ?? '')) ?></p>
              <h3 class="font-sans text-lg font-bold"><?= e((string) ($marco['titulo'] ?? '')) ?></h3>
              <p class="mt-2 max-w-prose text-[0.9375rem] leading-relaxed text-crust"><?= e((string) ($marco['texto'] ?? '')) ?></p>
            </li>
          <?php endforeach; ?>
        </ol>
      </section>
    <?php endif; ?>
  </div>

  <?php // RF-19 — para empresas e indústrias: a placa vermelha da marca. ?>
  <?php if ($temEmpresas): ?>
    <div class="mx-auto max-w-7xl px-4 pb-12 pt-4 sm:px-6 lg:px-8 lg:pb-16">
      <section id="empresas" class="placa-empresas" aria-labelledby="titulo-empresas">
        <div class="raios pointer-events-none absolute inset-0 opacity-50" style="--raios-x:100%;--raios-y:0%" aria-hidden="true"></div>

        <div class="relative">
          <h2 id="titulo-empresas" class="max-w-2xl text-3xl sm:text-4xl">
            <?= e((string) ($paraEmpresas['chamada'] ?? 'Para a sua empresa')) ?>
          </h2>
          <?php if (trim((string) ($paraEmpresas['texto'] ?? '')) !== ''): ?>
            <p class="mt-3 max-w-2xl text-lg leading-relaxed"><?= e((string) $paraEmpresas['texto']) ?></p>
          <?php endif; ?>

          <?php if (!empty($paraEmpresas['itens'])): ?>
            <ul class="mt-8 grid gap-6 md:grid-cols-3">
              <?php foreach ($paraEmpresas['itens'] as $item): ?>
                <li class="placa-empresas-item">
                  <h3 class="font-sans text-lg font-bold"><?= e((string) ($item['titulo'] ?? '')) ?></h3>
                  <p class="mt-2 text-[0.9375rem] leading-relaxed"><?= e((string) ($item['texto'] ?? '')) ?></p>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>

          <div class="mt-10 flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-5">
            <?php if ($linkOrcamento !== ''): ?>
              <a href="<?= e($linkOrcamento) ?>" target="_blank" rel="noopener noreferrer" class="botao-amarelo">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 11.5a8.4 8.4 0 0 1-12.4 7.4L3 20.5l1.7-5.4A8.5 8.5 0 1 1 21 11.5Z"/></svg>
                Pedir orçamento no WhatsApp
              </a>
            <?php else: ?>
              <?php // INTEGRAÇÃO FUTURA: cadastre o WhatsApp da matriz no painel e o botão liga sozinho. ?>
              <span class="botao-desligado">WhatsApp em breve</span>
            <?php endif; ?>
            <p class="text-sm">A matriz responde pelas <?= e((string) $totalUnidades) ?> unidades.</p>
          </div>
        </div>
      </section>
    </div>
  <?php endif; ?>
</div>

<?php include DD_BASE . '/partials/footer.php'; ?>
