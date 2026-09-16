<?php

declare(strict_types=1);

/**
 * index.php — home: herói, catálogo e como encomendar. As lojas ficam em unidades.php.
 */

require_once __DIR__ . '/partials/bootstrap.php';

$produtos   = dd_produtos();
$unidades   = dd_unidades(); // aqui só para contar: a lista vive em unidades.php

$totalEncomenda = count(array_filter($produtos, static fn (array $p): bool => dd_linha($p) === 'atacado'));

$tituloPagina    = 'Dolce Delícias — encomendas de salgados, assados e doces';
$descricaoPagina = 'Padaria e panificadora que atende escolas, faculdades, eventos e encomendas. Monte seu pedido no site e feche no WhatsApp da matriz.';

include __DIR__ . '/partials/header.php';
?>

<!-- ============================================================
     HERÓI
     ============================================================ -->
<?php
$heroEtiqueta  = 'Padaria e confeitaria · ' . count($unidades) . ' unidades';
$heroLinhas    = [
    ['texto' => 'Encomende'],
    ['texto' => 'o cento.'],
    ['texto' => 'A gente cuida', 'destaque' => true],
    ['texto' => 'do resto.',     'destaque' => true],
];
$heroTexto     = $totalEncomenda . ' itens de encomenda, feitos todo dia. Monte o pedido aqui e feche no WhatsApp da matriz';
$heroTextoLink = ['href' => '#encomendas', 'texto' => 'veja como funciona'];
$heroCta       = ['href' => '#catalogo',   'texto' => 'Ver catálogo'];
// O catálogo inteiro desfila na faixa. A cópia que fecha o ciclo sai do hero.
$heroTira      = array_column($produtos, 'nome');

include __DIR__ . '/partials/hero.php';
?>

<div class="faixa-raios" aria-hidden="true"></div>

<!-- Três fatos que o cliente pergunta antes de qualquer coisa. -->
<section class="border-b border-base-300 bg-base-100 px-4 sm:px-6 lg:px-8" aria-label="Resumo do atendimento">
  <dl class="mx-auto grid max-w-7xl gap-px bg-base-300 sm:grid-cols-3">
    <div class="bg-base-100 px-2 py-4 text-center sm:px-6 sm:py-6">
      <dt class="text-sm font-semibold text-crust">Encomendas</dt>
      <dd class="fonte-display mt-0.5 text-lg text-brand sm:mt-1 sm:text-2xl"><?= e((string) $totalEncomenda) ?> itens por cento</dd>
    </div>
    <div class="bg-base-100 px-2 py-4 text-center sm:px-6 sm:py-6">
      <dt class="text-sm font-semibold text-crust">Como receber</dt>
      <dd class="fonte-display mt-0.5 text-lg text-brand sm:mt-1 sm:text-2xl">Retira ou entrega</dd>
    </div>
    <div class="bg-base-100 px-2 py-4 text-center sm:px-6 sm:py-6">
      <dt class="text-sm font-semibold text-crust">Como fechar</dt>
      <dd class="fonte-display mt-0.5 text-lg text-brand sm:mt-1 sm:text-2xl">WhatsApp da matriz</dd>
    </div>
  </dl>
</section>

<?php
/**
 * PROMOÇÕES — RF-14 (quarta-feira) e RF-20 (baixa temporada).
 *
 * Antes do catálogo de propósito: no celular é a segunda coisa depois do herói,
 * e oferta que aparece depois de dezoito cards já não muda a decisão de ninguém.
 * Sem promoção cadastrada, o partial não desenha nada.
 */
include __DIR__ . '/partials/promocoes.php';
?>

<!-- ============================================================
     CATÁLOGO
     ============================================================ -->
<section id="catalogo" class="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-16 lg:px-8 lg:py-20">

  <div class="max-w-2xl">
    <h2 class="text-3xl sm:text-4xl">Catálogo</h2>
    <p class="mt-3 text-lg leading-relaxed text-crust">
      Os preços de encomenda são por cento. No balcão, a venda é por unidade.
      Adicione o que quiser e feche tudo numa conversa só.
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

      <p id="contagem-catalogo" class="ml-auto shrink-0 text-sm text-crust" data-contagem aria-live="polite">
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

  <!-- Grade -->
  <div data-grade-produtos class="mt-6 grid grid-cols-2 gap-3 sm:mt-8 sm:gap-5 lg:grid-cols-3 xl:grid-cols-4">
    <?php foreach ($produtos as $i => $produto): ?>
      <?php
        $eager = $i < 4; // os primeiros cards carregam sem lazy
        include __DIR__ . '/partials/product-card.php';
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
<section id="encomendas" class="border-y border-base-300 bg-base-200">
  <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-16 lg:px-8 lg:py-20">

    <div class="max-w-2xl">
      <h2 class="text-3xl sm:text-4xl">Como encomendar</h2>
      <p class="mt-3 text-lg leading-relaxed text-crust">
        Três passos, sem cadastro e sem pagamento pelo site. O acerto final é com a matriz.
      </p>
    </div>

    <ol class="mt-10 grid gap-6 md:grid-cols-3">
      <?php
      $passos = [
          [
              'titulo' => 'Monte o pedido',
              'texto'  => 'Adicione os itens do catálogo. A quantidade respeita o mínimo de cada produto e o total vai aparecendo.',
          ],
          [
              'titulo' => 'Confirme como recebe',
              'texto'  => 'Na revisão do pedido você diz se retira na matriz ou quer entrega, e como pretende pagar. Nada é cobrado aqui — o valor é referência até a matriz confirmar.',
          ],
          // RF-18 fora do escopo: o site não fala em frete. Escolher entrega é
          // RF-17; o combinado sobre ela acontece na conversa.
          [
              'titulo' => 'Feche no WhatsApp',
              'texto'  => 'O botão abre a conversa com o pedido já escrito, junto do que você escolheu. Vocês combinam a data e fecham por lá.',
          ],
      ];
      foreach ($passos as $i => $passo): ?>
        <li class="relative rounded-bandeja border border-base-300 bg-papel p-6">
          <span class="fonte-display absolute -top-4 left-6 flex h-10 w-10 items-center justify-center rounded-full bg-primary text-lg text-primary-content shadow-bandeja">
            <?= e((string) ($i + 1)) ?>
          </span>
          <h3 class="mt-4 text-xl"><?= e($passo['titulo']) ?></h3>
          <p class="mt-2 text-sm leading-relaxed text-crust"><?= e($passo['texto']) ?></p>
        </li>
      <?php endforeach; ?>
    </ol>

    <div class="mt-10 flex flex-col gap-5 rounded-bandeja bg-neutral p-7 text-neutral-content sm:flex-row sm:items-center sm:p-9">
      <div class="flex-1">
        <h3 class="text-2xl">Evento grande, escola ou empresa?</h3>
        <p class="mt-2 max-w-xl leading-relaxed opacity-85">
          Fazemos coffee break, formatura, feira e lanche escolar com bandeja montada.
          Fale com a matriz e a gente monta o orçamento com você.
        </p>
      </div>
      <?php // RF-19 — o atendimento a empresas tem página própria agora. ?>
      <a href="sobre.php#empresas" class="btn h-12 min-h-12 shrink-0 border-none bg-accent px-6 font-bold text-accent-content hover:bg-accent/85">
        Ver atendimento a empresas
      </a>
    </div>
  </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
