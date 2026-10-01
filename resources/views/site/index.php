<?php

declare(strict_types=1);

/**
 * index.php — home: herói, catálogo e como encomendar. As lojas ficam em unidades.php.
 */

require_once DD_BASE . '/partials/bootstrap.php';

$produtos   = dd_produtos();
$unidades   = dd_unidades(); // aqui só para contar: a lista vive em unidades.php

$totalEncomenda = count(array_filter($produtos, static fn (array $p): bool => dd_linha($p) === 'atacado'));

$tituloPagina    = 'Dolce Delícias — encomendas de salgados, assados e doces';
$descricaoPagina = 'Padaria e panificadora que atende escolas, faculdades, eventos e encomendas. Monte seu pedido no site e feche no WhatsApp da matriz.';

include DD_BASE . '/partials/header.php';
?>

<!-- ============================================================
     HERÓI
     ============================================================ -->
<?php
// Herói curto: o cartaz diz a proposta, o parágrafo diz como funciona, um botão leva ao catálogo.
// Sem etiqueta, sem faixa rolante e sem faixa de fatos: o que elas diziam cabe na frase abaixo.
$heroEtiqueta  = '';
$heroLinhas    = [
    ['texto' => 'Encomende o cento.'],
    ['texto' => 'A gente cuida do resto.', 'destaque' => true],
];
$heroTexto     = 'Monte o pedido aqui, escolha retirar ou receber e feche pelo WhatsApp da matriz. Sem cadastro e sem pagamento pelo site.';
$heroTextoLink = null;
$heroCta       = ['href' => '#catalogo', 'texto' => 'Ver catálogo'];
$heroTira      = [];

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

<!-- ============================================================
     CATÁLOGO
     ============================================================ -->
<section id="catalogo" class="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-16 lg:px-8 lg:py-20">

  <div class="max-w-2xl">
    <h2 class="text-3xl sm:text-4xl">Catálogo</h2>
    <p class="mt-3 text-lg leading-relaxed text-crust">
      Encomenda é por cento; balcão, por unidade.
    </p>
  </div>

  <?php // Busca + filtros. Já foi uma barra fixa com fundo creme, mas grudada
     // embaixo do header ela virava uma faixa de 180px cobrindo a grade.
     // Agora rola junto com a página, sem fundo e sem borda. ?>
  <div data-barra-filtros class="mt-8 flex flex-col gap-4">

    <div class="flex flex-wrap items-center gap-3">
      <div class="relative w-full min-w-0 sm:w-auto sm:max-w-sm sm:flex-1">
        <label for="busca" class="sr-only">Buscar no catálogo</label>
        <?php // z-10 obrigatorio: o .input do daisyUI e position:relative e vem
           // depois no DOM, entao sem z-index o fundo branco dele cobre a lupa. ?>
        <svg class="pointer-events-none absolute left-4 top-1/2 z-10 h-5 w-5 -translate-y-1/2 text-crust" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
        <input id="busca" type="search" data-busca-input
               class="input h-11 w-full rounded-full border-campo bg-papel pl-11 pr-4 text-base"
               placeholder="Buscar por coxinha, cupcake, bebida…"
               autocomplete="off"
               aria-describedby="contagem-catalogo">
      </div>

      <p id="contagem-catalogo" class="sr-only ml-auto shrink-0 text-sm text-crust sm:not-sr-only" data-contagem aria-live="polite">
        <?= e((string) count($produtos)) ?> itens
      </p>
    </div>

  </div>

  <?php
  /**
   * BARRA FINA
   * Entra grudada embaixo do header quando a barra completa passa do topo, e
   * sai quando o catálogo acaba — assim não paira sobre as outras seções.
   * Quem liga e desliga é assets/js/ui.js (seção 4), pela classe .oculto.
   *
   * Leva a lupa e a contagem. A busca é um botão que rola de volta e põe o
   * foco no campo de verdade — um campo só, sem duas caixas para manter em
   * sincronia.
   */
  ?>
  <div data-barra-fina
       class="oculto fixed inset-x-0 top-[4.5rem] z-40 border-b border-base-300 bg-base-100/90 backdrop-blur-md">
    <div class="mx-auto flex max-w-7xl items-center gap-2 px-4 py-1.5 sm:px-6 lg:px-8">

      <button type="button" data-focar-busca
              class="btn btn-ghost h-10 min-h-10 w-10 shrink-0 rounded-full p-0 text-crust hover:text-brand"
              aria-label="Ir para a busca do catálogo">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
      </button>

      <?php // Cópia visual da contagem. O aria-live fica só no original, senão
         // o leitor de tela anuncia a mesma mudança duas vezes. ?>
      <p class="ml-auto hidden shrink-0 text-sm text-crust sm:block" data-contagem aria-hidden="true">
        <?= e((string) count($produtos)) ?> itens
      </p>
    </div>
  </div>

  <?php
  // Categoria é o filtro que todo mundo usa: fica à vista, em chips.
  // Atendimento e restrições são casos de nicho: ficam dentro de "Filtros".
  // O campo escondido guarda a categoria escolhida; ui.js lê dele.
  $restricoes = array_values(array_filter(['vegano', 'sem lactose', 'sem glúten', 'sem carne'],
      static fn (string $tag): bool => (bool) array_filter($produtos, static fn (array $p): bool => in_array($tag, $p['tags'], true))));
  ?>
  <input type="hidden" data-filter-category value="">
  <div class="mt-5 flex items-center gap-2">
    <div class="sem-barra -mx-4 flex flex-1 gap-2 overflow-x-auto px-4 sm:mx-0 sm:flex-wrap sm:px-0" role="group" aria-label="Categorias">
      <button type="button" class="chip" data-chip-categoria="" aria-pressed="true">Tudo</button>
      <?php foreach (dd_categorias() as $categoria): ?>
        <button type="button" class="chip" data-chip-categoria="<?= e($categoria) ?>" aria-pressed="false"><?= e($categoria) ?></button>
      <?php endforeach; ?>
    </div>

    <details class="dropdown dropdown-end shrink-0">
      <summary class="chip cursor-pointer list-none [&::-webkit-details-marker]:hidden">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M7 12h10M10 18h4"/></svg>
        Filtros
        <span data-filtros-contagem class="oculto rounded-full bg-primary px-1.5 text-xs leading-5 text-primary-content"></span>
      </summary>
      <div class="dropdown-content z-30 mt-2 w-72 rounded-2xl border border-base-300 bg-papel p-4 shadow-bandeja-alta">
        <label for="filtro-linha" class="text-sm font-semibold">Atendimento</label>
        <select id="filtro-linha" data-filter-line class="select mt-1 w-full border-campo">
          <option value="">Encomenda e balcão</option>
          <option value="ENCOMENDA">Só encomenda (por cento)</option>
          <option value="BALCAO">Só balcão (por unidade)</option>
        </select>
        <?php if ($restricoes !== []): ?>
          <fieldset class="mt-4">
            <legend class="text-sm font-semibold">Restrições alimentares</legend>
            <?php foreach ($restricoes as $tag): ?>
              <label class="mt-2 flex min-h-9 cursor-pointer items-center gap-2.5">
                <input type="checkbox" class="checkbox checkbox-sm checkbox-primary" data-filter-tag value="<?= e($tag) ?>">
                <span class="first-letter:uppercase"><?= e($tag) ?></span>
              </label>
            <?php endforeach; ?>
          </fieldset>
        <?php endif; ?>
      </div>
    </details>
  </div>
  <!-- Grade -->
  <div data-grade-produtos class="mt-6 grid grid-cols-2 gap-3 sm:mt-8 sm:gap-5 lg:grid-cols-3 xl:grid-cols-4">
    <?php foreach ($produtos as $i => $produto): ?>
      <?php
        $eager = $i < 4; // os primeiros cards carregam sem lazy
        include DD_BASE . '/partials/product-card.php';
      ?>
    <?php endforeach; ?>
  </div>

  <!-- Busca sem resultado -->
  <div data-sem-resultado class="oculto flex flex-col items-center gap-3 py-16 text-center">
    <div class="massa flex h-20 w-20 items-center justify-center rounded-full text-crust/40">
      <svg class="h-9 w-9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
    </div>
    <p class="text-xl font-bold">Nada com esse nome por aqui</p>
    <p class="max-w-sm text-sm leading-relaxed text-crust">
      Tente outra palavra ou volte para o catálogo inteiro. Se for algo especial, a gente faz sob encomenda — é só chamar no WhatsApp.
    </p>
    <button type="button" data-limpar-filtros class="btn btn-primary btn-sm mt-1 font-bold">
      Mostrar tudo
    </button>
  </div>
</section>

<!-- ============================================================
     COMO ENCOMENDAR — sequência de verdade, por isso vai numerada
     ============================================================ -->
<section id="encomendas" class="border-t border-base-300 bg-base-200">
  <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
    <h2 class="text-3xl sm:text-4xl">Como encomendar</h2>

    <ol class="mt-8 grid gap-x-8 gap-y-6 md:grid-cols-3">
      <?php
      $passos = [
          ['titulo' => 'Monte o pedido',        'texto' => 'Adicione os itens. O mínimo de cada produto já vem respeitado.'],
          ['titulo' => 'Diga como quer receber', 'texto' => 'Retirada na matriz ou entrega, e a forma de pagamento. Nada é cobrado no site.'],
          // RF-18 fora do escopo: o site não fala em frete.
          ['titulo' => 'Feche no WhatsApp',     'texto' => 'A conversa abre com o pedido escrito. A data e o valor final são combinados ali.'],
      ];
      foreach ($passos as $i => $passo): ?>
        <li class="flex gap-4">
          <span class="fonte-display flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary text-lg text-primary-content">
            <?= e((string) ($i + 1)) ?>
          </span>
          <div>
            <h3 class="text-xl"><?= e($passo['titulo']) ?></h3>
            <p class="mt-1 leading-relaxed text-crust"><?= e($passo['texto']) ?></p>
          </div>
        </li>
      <?php endforeach; ?>
    </ol>

    <?php // RF-19 — atendimento a empresas tem página própria; aqui só o convite. ?>
    <p class="mt-10 border-t border-base-300 pt-6 text-crust">
      Evento, escola ou empresa?
      <a href="/sobre#empresas" class="font-semibold text-brand underline underline-offset-4">Veja o atendimento para grandes pedidos</a>
    </p>
  </div>
</section>

<?php include DD_BASE . '/partials/footer.php'; ?>
