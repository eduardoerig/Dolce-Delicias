<?php

declare(strict_types=1);

/**
 * index.php — home: herói, catálogo e como encomendar. As lojas ficam em unidades.php.
 */

require_once DD_BASE . '/partials/bootstrap.php';

$produtos = dd_produtos();

$tituloPagina    = 'Dolce Delícias — encomendas de salgados, assados e doces';
$descricaoPagina = 'Padaria e panificadora que atende escolas, faculdades, eventos e encomendas. Monte seu pedido no site e feche no WhatsApp da matriz.';

include DD_BASE . '/partials/header.php';
?>

<!-- ============================================================
     HERÓI
     ============================================================ -->
<?php
// Herói: a comida em primeiro plano, o cartaz diz a proposta, três garantias
// dizem como funciona e um botão leva ao catálogo.
$heroLinhas  = [
    ['texto' => 'Encomende'],
    ['texto' => 'o cento.'],
];
$heroTexto   = 'A gente cuida do resto: salgados, assados e doces feitos no dia da sua festa. Você só fecha pelo WhatsApp.';
$heroCta     = ['href' => '#catalogo', 'texto' => 'Montar meu pedido'];
$heroLink    = ['href' => '#encomendas', 'texto' => 'Como funciona'];

// Garantias: retirada ou entrega, o prazo da matriz e o pagamento fora do site.
$preparoMatriz = trim(explode(' · ', (string) (dd_matriz()['preparo'] ?? ''))[0]);
$heroGarantias = array_values(array_filter([
    'Retire ou receba em casa',
    $preparoMatriz !== '' ? mb_strtoupper(mb_substr($preparoMatriz, 0, 1)) . mb_substr($preparoMatriz, 1) : null,
    'Sem cadastro, sem pagar no site',
]));

include DD_BASE . '/partials/hero.php';
?>

<?php
/**
 * PROMOÇÕES — RF-14 (quarta-feira) e RF-20 (baixa temporada).
 *
 * Antes do catálogo de propósito: no celular é a segunda coisa depois do herói,
 * e oferta que aparece depois de dezoito cards já não muda a decisão de ninguém.
 * Sem promoção cadastrada, o partial não desenha nada.
 */
include DD_BASE . '/partials/promocoes.php';
?>

<?php
/* ============================================================
   CATÁLOGO — desenho de cardápio online: faixa com título e busca,
   filtros na lateral, grade com paginação.

   Tudo que filtra, ordena e pagina mora em assets/js/ui.js (seção 4) e lê
   os data-* abaixo. Sem JavaScript, a grade inteira aparece.
   ============================================================ */
$totalProdutos = count($produtos);

$porCategoria = [];
foreach ($produtos as $p) {
    $porCategoria[$p['categoria']] = ($porCategoria[$p['categoria']] ?? 0) + 1;
}

// Restrições que existem de fato no catálogo; filtro sem item seria beco sem saída.
$restricoes = array_values(array_filter(['vegano', 'sem lactose', 'sem glúten', 'sem carne'],
    static fn (string $tag): bool => (bool) array_filter($produtos, static fn (array $p): bool => in_array($tag, $p['tags'], true))));

// Teto do filtro de preço: o maior preço por peça, arredondado para cima.
$unitarios = array_map(static fn (array $p): float => dd_faixa_principal($p)['unitario'], $produtos);
$precoTeto = $unitarios !== [] ? (int) ceil(max($unitarios)) : 0;
?>
<section id="catalogo" class="bg-farinha" aria-labelledby="catalogo-titulo">

  <!-- Faixa: título à esquerda, busca à direita -->
  <div class="border-b border-linha bg-polvilho">
    <div class="mx-auto flex max-w-7xl flex-col gap-5 px-4 py-8 sm:px-6 md:flex-row md:items-end md:justify-between lg:px-8 lg:py-10">
      <div>
        <h2 id="catalogo-titulo" class="text-4xl sm:text-5xl">Catálogo</h2>
        <p class="mt-2 text-crust"><?= e($totalProdutos === 1 ? '1 item feito' : $totalProdutos . ' itens feitos') ?> todo dia, por encomenda ou no balcão.</p>
      </div>

      <form class="busca-pilula w-full md:max-w-md" role="search" data-busca-form>
        <label for="busca" class="sr-only">Buscar no catálogo</label>
        <svg class="h-5 w-5 shrink-0 text-crust" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
        <input id="busca" type="search" data-busca-input
               placeholder="Buscar coxinha, cupcake, bebida…"
               autocomplete="off" enterkeyhint="search"
               aria-describedby="contagem-catalogo">
        <button type="submit" class="botao-primario">Buscar</button>
      </form>
    </div>
  </div>

  <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:grid lg:grid-cols-[15.5rem_minmax(0,1fr)] lg:items-start lg:gap-8 lg:px-8 lg:py-10">

    <!-- Filtros: coluna fixa no computador, painel por cima no celular -->
    <section id="filtros" class="filtros-painel" aria-labelledby="filtros-titulo" data-filtros-painel>
      <div class="filtros-cabeca">
        <h3 id="filtros-titulo" class="font-sans text-lg font-bold">Filtros</h3>
        <button type="button" class="filtros-limpar" data-limpar-filtros>Limpar</button>
        <button type="button" class="filtros-fechar" data-fechar-filtros aria-label="Fechar filtros">
          <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
        </button>
      </div>

      <div class="filtros-corpo">
        <fieldset class="filtro-grupo">
          <legend>Categoria</legend>
          <label class="filtro-opcao">
            <input type="checkbox" data-filter-category-todas checked>
            <span>Todas</span>
            <small><?= e((string) $totalProdutos) ?></small>
          </label>
          <?php foreach ($porCategoria as $categoria => $quantos): ?>
            <label class="filtro-opcao">
              <input type="checkbox" data-filter-category value="<?= e((string) $categoria) ?>">
              <span><?= e((string) $categoria) ?></span>
              <small><?= e((string) $quantos) ?></small>
            </label>
          <?php endforeach; ?>
        </fieldset>

        <?php if ($precoTeto > 1): ?>
          <div class="filtro-grupo">
            <label for="filtro-preco" class="filtro-titulo">Preço por peça</label>
            <input id="filtro-preco" type="range" class="faixa-preco" data-filter-preco
                   min="1" max="<?= e((string) $precoTeto) ?>" step="1" value="<?= e((string) $precoTeto) ?>"
                   aria-valuetext="Qualquer preço">
            <p class="filtro-dica"><span>R$ 1</span><output for="filtro-preco" data-filter-preco-saida>Qualquer preço</output></p>
          </div>
        <?php endif; ?>

        <fieldset class="filtro-grupo">
          <legend>Como comprar</legend>
          <div class="segmentos">
            <label><input type="radio" name="linha" value="" data-filter-line checked><span>Tudo</span></label>
            <label><input type="radio" name="linha" value="ENCOMENDA" data-filter-line><span>Encomenda</span></label>
            <label><input type="radio" name="linha" value="BALCAO" data-filter-line><span>Balcão</span></label>
          </div>
          <p class="filtro-dica">Encomenda é pedido antecipado; balcão, compra na loja.</p>
        </fieldset>

        <?php if ($restricoes !== []): ?>
          <fieldset class="filtro-grupo">
            <legend>Restrições alimentares</legend>
            <?php foreach ($restricoes as $tag): ?>
              <label class="filtro-opcao">
                <input type="checkbox" data-filter-tag value="<?= e($tag) ?>">
                <span class="first-letter:uppercase"><?= e($tag) ?></span>
              </label>
            <?php endforeach; ?>
          </fieldset>
        <?php endif; ?>
      </div>

      <?php // Só no celular: o painel cobre a grade, então precisa de uma saída que diga o resultado. ?>
      <div class="filtros-pe">
        <button type="button" class="botao-primario w-full" data-fechar-filtros data-ver-itens>Ver <?= e((string) $totalProdutos) ?> itens</button>
      </div>
    </section>
    <div class="filtros-fundo" data-fechar-filtros hidden></div>

    <div class="min-w-0">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <p id="contagem-catalogo" class="font-semibold" data-contagem aria-live="polite"><?= e((string) $totalProdutos) ?> itens</p>

        <div class="flex flex-wrap items-center gap-2">
          <button type="button" class="botao-filtros lg:hidden" data-abrir-filtros aria-controls="filtros" aria-expanded="false">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M7 12h10M10 18h4"/></svg>
            Filtros
            <span data-filtros-contagem class="oculto"></span>
          </button>
          <label for="ordenar" class="sr-only">Ordenar por</label>
          <select id="ordenar" class="seletor-ordem" data-ordenar>
            <option value="destaque">Ordenar: Recomendados</option>
            <option value="preco-asc">Menor preço por peça</option>
            <option value="preco-desc">Maior preço por peça</option>
            <option value="nome">Nome (A–Z)</option>
          </select>
        </div>
      </div>

      <div data-grade-produtos class="mt-4 grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-3 xl:grid-cols-4">
        <?php foreach ($produtos as $i => $produto): ?>
          <?php
            $eager = false; // o herói ocupa a primeira tela: os cards ficam abaixo dela e carregam sob demanda
            include DD_BASE . '/partials/product-card.php';
          ?>
        <?php endforeach; ?>
      </div>

      <!-- Busca sem resultado -->
      <div data-sem-resultado class="oculto flex flex-col items-center gap-3 rounded-bandeja border border-linha bg-papel px-6 py-14 text-center">
        <p class="text-xl font-bold">Nenhum item com esses filtros</p>
        <p class="max-w-sm text-sm leading-relaxed text-crust">
          Tente outra palavra ou limpe os filtros. Se for algo especial, a matriz faz sob encomenda pelo WhatsApp.
        </p>
        <button type="button" data-limpar-filtros class="botao-primario mt-1">Limpar filtros</button>
      </div>

      <nav class="paginacao" aria-label="Páginas do catálogo" data-paginacao hidden></nav>
    </div>
  </div>
</section>

<!-- ============================================================
     COMO ENCOMENDAR — os passos e o celular com a conversa do pedido
     ============================================================ -->
<?php
$passos = [
    ['titulo' => 'Monte o pedido',        'texto' => 'Adicione os itens. O mínimo de cada produto já vem respeitado.'],
    ['titulo' => 'Diga como quer receber', 'texto' => 'Retirada na matriz ou entrega, e a forma de pagamento. Nada é cobrado no site.'],
    // RF-18 fora do escopo: o site não fala em frete.
    ['titulo' => 'Feche no WhatsApp',     'texto' => 'A conversa abre com o pedido escrito. A data e o valor final são combinados ali.'],
];
include DD_BASE . '/partials/como-encomendar.php';
?>

<?php include DD_BASE . '/partials/footer.php'; ?>
