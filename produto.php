<?php

declare(strict_types=1);

/**
 * produto.php?slug=<slug> — página de um produto.
 * Slug inexistente cai numa página 404 amigável (com status HTTP correto).
 */

require_once __DIR__ . '/partials/bootstrap.php';

$slug    = isset($_GET['slug']) ? trim((string) $_GET['slug']) : '';
$produto = $slug !== '' ? dd_produto_por_slug($slug) : null;

/* -------------------------------------------------------------------------
 * 404 AMIGÁVEL
 * ---------------------------------------------------------------------- */
if ($produto === null) {
    http_response_code(404);
    $tituloPagina    = 'Produto não encontrado — Dolce Delícias';
    $descricaoPagina = 'Esse produto não está no catálogo. Veja o catálogo completo da Dolce Delícias.';
    include __DIR__ . '/partials/header.php';
    ?>
    <section class="mx-auto flex min-h-[60vh] max-w-xl flex-col items-center justify-center gap-4 px-4 py-20 text-center">
      <div class="massa flex h-24 w-24 items-center justify-center rounded-full text-crust/40">
        <svg class="h-11 w-11" viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M11 36c0-9 9-15 21-15s21 6 21 15-9 12-21 12-21-3-21-12Z"/><path d="M22 30l4 6M32 28l4 7M42 30l3 6"/>
        </svg>
      </div>
      <h1 class="text-3xl">Esse item saiu do cardápio</h1>
      <p class="max-w-md leading-relaxed text-crust">
        O endereço <code class="rounded bg-base-200 px-1.5 py-0.5 text-sm"><?= e($slug !== '' ? $slug : '(vazio)') ?></code>
        não corresponde a nenhum produto. Pode ter mudado de nome ou saído de linha.
      </p>
      <div class="mt-2 flex flex-wrap justify-center gap-3">
        <a href="index.php#catalogo" class="btn btn-primary font-bold">Ver o catálogo</a>
        <a href="index.php" class="btn btn-ghost border border-base-300 font-semibold text-crust">Voltar para a home</a>
      </div>
    </section>
    <?php
    include __DIR__ . '/partials/footer.php';
    exit;
}

/* -------------------------------------------------------------------------
 * PRODUTO ENCONTRADO
 * ---------------------------------------------------------------------- */
$faixas       = array_map('dd_faixa', $produto['precos'] ?? []);
$faixas       = $faixas ?: [dd_faixa(['valor' => 0, 'por' => 'unidade'])];
$faixa        = $faixas[0];
$temVariacoes = count($faixas) > 1;

$linha        = dd_linha($produto);
$rotuloLinha  = dd_rotulo_linha($linha);
$disponivel   = ($produto['disponivel'] ?? true) !== false;
$imagem       = dd_imagem($produto['imagem'] ?? null);
$sabores      = $produto['sabores'] ?? [];
$relacionados = dd_relacionados($produto);

$tituloPagina    = $produto['nome'] . ' — Dolce Delícias';
$descricaoPagina = dd_resumo((string) ($produto['descricao'] ?? ''), 155);

include __DIR__ . '/partials/header.php';
?>

<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">

  <nav aria-label="Você está aqui" class="mb-6 flex flex-wrap items-center gap-2 text-sm font-semibold text-crust">
    <a href="index.php" class="rounded hover:text-brand">Início</a>
    <span aria-hidden="true">/</span>
    <a href="index.php#catalogo" class="rounded hover:text-brand"><?= e($produto['categoria'] ?? 'Catálogo') ?></a>
    <span aria-hidden="true">/</span>
    <span class="text-base-content"><?= e($produto['nome']) ?></span>
  </nav>

  <div class="grid gap-8 lg:grid-cols-2 lg:gap-12">

    <!-- ------------------------------------------------------------------
         GALERIA
         >>> INTEGRAÇÃO FUTURA <<<
         Hoje existe uma imagem só (o campo 'imagem' de data/products.php).
         Para virar galeria de verdade, troque por um array 'imagens' e faça
         um foreach nas miniaturas abaixo.
         ------------------------------------------------------------------ -->
    <div>
      <figure class="overflow-hidden rounded-bandeja border border-base-300 bg-papel shadow-bandeja">
        <div class="aspect-[4/3]">
          <?php if ($imagem): ?>
            <img src="<?= e($imagem) ?>" alt="<?= e($produto['nome']) ?>"
                 class="h-full w-full object-cover" width="960" height="720">
          <?php else: ?>
            <div class="massa flex h-full w-full flex-col items-center justify-center gap-2 text-crust/45"
                     role="img" aria-label="Foto de <?= e($produto['nome']) ?> ainda não cadastrada">
              <?= dd_icone_categoria((string) ($produto['categoria'] ?? '')) ?>
              <span class="text-sm font-semibold text-crust">foto em breve</span>
            </div>
          <?php endif; ?>
        </div>
      </figure>

      <div class="mt-3 grid grid-cols-3 gap-3" aria-hidden="true">
        <?php for ($i = 0; $i < 3; $i++): ?>
          <div class="massa aspect-square rounded-2xl border border-base-300 opacity-60"></div>
        <?php endfor; ?>
      </div>
      <p class="mt-2 text-xs text-crust">As miniaturas ficam prontas assim que as fotos chegarem.</p>
    </div>

    <!-- ------------------------------------------------------------------
         INFORMAÇÕES E PEDIDO
         ------------------------------------------------------------------ -->
    <div>
      <div class="flex flex-wrap items-center gap-2">
        <span class="badge border-none bg-base-200 font-semibold text-crust"><?= e($produto['categoria'] ?? '') ?></span>
        <?php if ($rotuloLinha !== ''): ?>
          <span class="badge border-none bg-base-300 font-semibold text-crust"><?= e($rotuloLinha) ?></span>
        <?php endif; ?>
        <?php if (!empty($produto['destaque'])): ?>
          <span class="badge border-none bg-accent font-bold text-accent-content">mais pedido</span>
        <?php endif; ?>
      </div>

      <h1 class="mt-3 text-4xl leading-[1.08] sm:text-5xl"><?= e($produto['nome']) ?></h1>

      <p class="mt-4 max-w-prose text-lg leading-relaxed text-crust"><?= e($produto['descricao'] ?? '') ?></p>

      <!-- Preço -->
      <div class="mt-7 rounded-bandeja border border-base-300 bg-papel p-6 shadow-bandeja">

        <?php
        /**
         * ESCOLHA DA EMBALAGEM
         *
         * Usa o mesmo .opcao da confirmação do pedido, e não o .chip: as duas
         * telas fazem a MESMA coisa — escolher uma entre várias — então têm de
         * ter o mesmo desenho. O chip é o filtro do catálogo, onde aceso quer
         * dizer "estou vendo só isto"; com o <input> em sr-only, não sobrava
         * nenhuma marca dizendo qual embalagem estava selecionada.
         *
         * Aqui o cartão ganha ainda mais do que lá: a embalagem é uma decisão
         * de preço, e a linha de ajuda mostra de uma vez o valor da caixa, o
         * preço por unidade e o pedido mínimo. No chip só cabia uma string.
         */
        ?>
        <?php if ($temVariacoes): ?>
          <fieldset>
            <legend class="text-base font-bold text-base-content">Escolha a embalagem</legend>

            <div class="mt-3 grid gap-2.5">
              <?php foreach ($faixas as $i => $f): ?>
                <?php
                // Quando a faixa tem nome próprio ("Caixa 200"), o preço desce
                // para a linha de ajuda — senão o cartão não mostraria quanto custa.
                $ajuda = [];
                if ($f['rotulo'] !== '') {
                    $ajuda[] = $f['exibicao'];
                }
                if ($f['base'] > 1) {
                    $ajuda[] = dd_moeda($f['unitario']) . ' por unidade';
                }
                if ($f['temMinimo']) {
                    $ajuda[] = 'mínimo ' . $f['min'] . ' un';
                }
                ?>
                <label class="opcao">
                  <input type="radio" name="faixa" class="radio radio-primary mt-0.5 shrink-0" data-faixa
                         value="<?= e((string) $i) ?>"
                         data-preco="<?= e(number_format($f['unitario'], 4, '.', '')) ?>"
                         data-por="<?= e($f['exibicao']) ?>"
                         data-min="<?= e((string) $f['min']) ?>"
                         data-passo="<?= e((string) $f['passo']) ?>"
                         data-valor="<?= e(number_format($f['valor'], 2, '.', '')) ?>"
                         <?= $i === 0 ? 'checked' : '' ?>>
                  <span class="min-w-0">
                    <span class="block font-bold leading-tight">
                      <?= e($f['rotulo'] !== '' ? $f['rotulo'] : $f['exibicao']) ?>
                    </span>
                    <?php if ($ajuda !== []): ?>
                      <span class="mt-1 block text-xs leading-relaxed text-crust">
                        <?= e(implode(' · ', $ajuda)) ?>
                      </span>
                    <?php endif; ?>
                  </span>
                </label>
              <?php endforeach; ?>
            </div>
          </fieldset>
          <div class="mt-5 h-px bg-base-300"></div>
        <?php endif; ?>

        <p class="<?= $temVariacoes ? 'mt-5 ' : '' ?>flex items-baseline gap-2">
          <span class="text-base font-semibold text-crust">R$</span>
          <span class="fonte-display text-5xl leading-none text-brand" data-preco-cheio><?= e(number_format($faixa['valor'], 2, ',', '.')) ?></span>
          <span class="text-base font-semibold text-crust" data-preco-por>/ <?= e($faixa['porCurto']) ?></span>
        </p>

        <?php if ($faixa['base'] > 1): ?>
          <p class="mt-1.5 text-sm text-crust" data-preco-unitario>
            Sai a <?= e(dd_moeda($faixa['unitario'])) ?> por unidade.
          </p>
        <?php endif; ?>

        <?php if ($faixa['temMinimo']): ?>
          <p class="mt-3 inline-flex items-center gap-2 rounded-full bg-base-200 px-3 py-1.5 text-sm font-semibold text-crust">
            <svg class="h-4 w-4 text-brand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M12 8v5M12 16.5h.01"/><circle cx="12" cy="12" r="9"/></svg>
            Pedido mínimo: <?= e((string) $faixa['min']) ?> unidades
          </p>
        <?php endif; ?>

        <?php if ($disponivel): ?>
          <!-- Quantidade + adicionar -->
          <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-end">
            <div>
              <label for="qtd" class="block text-sm font-semibold text-crust">Quantidade (unidades)</label>
              <div class="mt-1.5 flex h-13 w-fit items-center rounded-2xl border border-campo bg-base-200">
                <button type="button" data-qty-menos
                        class="btn btn-ghost h-12 w-12 rounded-2xl p-0 text-xl font-bold"
                        aria-label="Diminuir quantidade">−</button>
                <input id="qtd" type="number" data-qty-input
                       class="h-12 w-20 border-0 bg-transparent text-center text-lg font-bold focus:outline-none"
                       value="<?= e((string) $faixa['min']) ?>"
                       min="<?= e((string) $faixa['min']) ?>"
                       step="<?= e((string) $faixa['passo']) ?>"
                       inputmode="numeric"
                       aria-describedby="ajuda-qtd">
                <button type="button" data-qty-mais
                        class="btn btn-ghost h-12 w-12 rounded-2xl p-0 text-xl font-bold"
                        aria-label="Aumentar quantidade">+</button>
              </div>
              <p id="ajuda-qtd" class="mt-1.5 text-xs text-crust">
                Mínimo <?= e((string) $faixa['min']) ?> · de <?= e((string) $faixa['passo']) ?> em <?= e((string) $faixa['passo']) ?>
              </p>
            </div>

            <div class="flex-1">
              <p class="text-sm font-semibold text-crust">
                Subtotal: <span class="font-bold text-base-content" data-subtotal><?= e(dd_moeda($faixa['unitario'] * $faixa['min'])) ?></span>
              </p>
              <button type="button"
                      class="btn btn-primary mt-1.5 h-13 min-h-13 w-full text-base font-bold"
                      data-add
                      data-usa-qty
                      data-id="<?= e($produto['slug']) ?>"
                      data-slug="<?= e($produto['slug']) ?>"
                      data-nome="<?= e($produto['nome']) ?>"
                      data-preco="<?= e(number_format($faixa['unitario'], 4, '.', '')) ?>"
                      data-por="<?= e($faixa['exibicao']) ?>"
                      data-min="<?= e((string) $faixa['min']) ?>"
                      data-passo="<?= e((string) $faixa['passo']) ?>"
                      data-base="<?= e((string) $faixa['base']) ?>"
                      <?php // Miniatura do carrinho: a foto quando existir, senão o ícone da categoria. ?>
                      data-imagem="<?= e((string) $imagem) ?>"
                      data-categoria="<?= e($produto['categoria'] ?? '') ?>">
                Adicionar ao carrinho
              </button>
            </div>
          </div>
        <?php else: ?>
          <p class="mt-6 rounded-2xl bg-base-200 p-4 text-sm font-semibold text-crust">
            Este item está fora do cardápio hoje. Fale com a unidade para saber quando volta.
          </p>
        <?php endif; ?>
      </div>

      <?php if ($sabores): ?>
        <section class="mt-8">
          <h2 class="text-xl">Sabores</h2>
          <p class="mt-1.5 text-sm leading-relaxed text-crust">
            Você escolhe a combinação na conversa com a unidade — dá para misturar.
          </p>
          <ul class="mt-3 flex flex-wrap gap-2">
            <?php foreach ($sabores as $sabor): ?>
              <li class="rounded-full border border-base-300 bg-papel px-3.5 py-1.5 text-sm font-semibold text-crust">
                <?= e((string) $sabor) ?>
              </li>
            <?php endforeach; ?>
          </ul>
        </section>
      <?php endif; ?>

      <?php if (!empty($produto['tags'])): ?>
        <ul class="mt-6 flex flex-wrap gap-2" aria-label="Marcadores">
          <?php foreach ($produto['tags'] as $tag): ?>
            <li class="rounded-lg bg-base-200 px-2.5 py-1 text-xs font-semibold text-crust"><?= e((string) $tag) ?></li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </div>

  <?php if ($relacionados): ?>
    <section class="mt-16 border-t border-base-300 pt-12">
      <h2 class="text-2xl sm:text-3xl">Vai bem junto</h2>
      <p class="mt-2 text-crust">Outros itens de <?= e($produto['categoria'] ?? '') ?>.</p>
      <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($relacionados as $produtoRelacionado): ?>
          <?php
            $produtoOriginal = $produto;
            $produto = $produtoRelacionado;
            $eager = false;
            include __DIR__ . '/partials/product-card.php';
            $produto = $produtoOriginal;
          ?>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
