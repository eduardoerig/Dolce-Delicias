<?php

declare(strict_types=1);

/**
 * produto.php?slug=<slug> — página de um produto.
 * Slug inexistente cai numa página 404 amigável (com status HTTP correto).
 *
 * Cantos retos como o resto do site: a foto de ponta a ponta no celular (no
 * computador, a coluna da esquerda), o nome, a promoção numa faixa vermelha,
 * a caixa de compra com a ficha (como compra, mínimo, preço por peça), os
 * sabores e as lojas, uma por linha.
 */

require_once DD_BASE . '/partials/bootstrap.php';

$slug    = isset($_GET['slug']) ? trim((string) $_GET['slug']) : '';
$produto = $slug !== '' ? dd_produto_por_slug($slug) : null;

/* -------------------------------------------------------------------------
 * 404 AMIGÁVEL
 * ---------------------------------------------------------------------- */
if ($produto === null) {
    http_response_code(404);
    $tituloPagina    = 'Produto não encontrado — Dolce Delícias';
    $descricaoPagina = 'Esse produto não está no catálogo. Veja o catálogo completo da Dolce Delícias.';
    include DD_BASE . '/partials/header.php';
    ?>
    <section class="bg-farinha">
      <div class="mx-auto flex min-h-[60vh] max-w-xl flex-col items-center justify-center gap-4 px-4 py-20 text-center">
        <span class="grid h-24 w-24 place-items-center rounded-full bg-polvilho text-farelo">
          <svg class="h-11 w-11" viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M11 36c0-9 9-15 21-15s21 6 21 15-9 12-21 12-21-3-21-12Z"/><path d="M22 30l4 6M32 28l4 7M42 30l3 6"/>
          </svg>
        </span>
        <h1 class="text-3xl">Esse item saiu do cardápio</h1>
        <p class="max-w-md leading-relaxed text-crust">
          O endereço <code class="rounded bg-polvilho px-1.5 py-0.5 text-sm"><?= e($slug !== '' ? $slug : '(vazio)') ?></code>
          não corresponde a nenhum produto. Pode ter mudado de nome ou saído de linha.
        </p>
        <div class="mt-2 flex flex-wrap justify-center gap-3">
          <a href="/#catalogo" class="botao-primario">Ver o catálogo</a>
          <a href="/" class="botao-secundario">Voltar para o início</a>
        </div>
      </div>
    </section>
    <?php
    include DD_BASE . '/partials/footer.php';
    exit;
}

/* -------------------------------------------------------------------------
 * PRODUTO ENCONTRADO
 * ---------------------------------------------------------------------- */
$faixas       = array_map('dd_faixa', $produto['precos'] ?? []);
$faixas       = $faixas ?: [dd_faixa(['valor' => 0, 'por' => 'unidade'])];
$faixa        = $faixas[0];
$temVariacoes = count($faixas) > 1;

$linhaProduto = (string) ($produto['linha'] ?? 'AMBOS');
$comoCompra   = match ($linhaProduto) {
    'ENCOMENDA' => 'Encomenda',
    'BALCAO'    => 'Balcão',
    default     => 'Encomenda e balcão',
};
$disponivel   = ($produto['disponivel'] ?? true) !== false;
$imagem       = dd_imagem($produto['imagem'] ?? null);
$sabores      = $produto['sabores'] ?? [];
$relacionados = dd_relacionados($produto, 4);
$ofertas      = dd_promocoes_produto($produto);

// "atacado"/"varejo" são calculadas a partir de "Como é vendido", e balcão/encomenda
// já estão na ficha ("Como compra"): aqui só repetiriam.
$etiquetas = array_values(array_filter(
    array_map('strval', $produto['tags'] ?? []),
    static fn (string $t): bool => !in_array(mb_strtolower($t), ['atacado', 'varejo', 'balcao', 'balcão', 'encomenda'], true)
));

// Unidades onde o produto está à venda, na ordem do cadastro (matriz primeiro).
$ondeTem = array_values(array_filter(
    dd_unidades(),
    static fn (array $u): bool => in_array($u['slug'], $produto['unidades'] ?? [], true)
));
usort($ondeTem, static fn (array $a, array $b): int => (int) !empty($b['matriz']) <=> (int) !empty($a['matriz']));

// Ajuda do seletor de quantidade, em frase: "Mínimo de 50 un, de 50 em 50."
$ajudaQtd = 'Mínimo de ' . $faixa['min'] . ' un';
$ajudaQtd .= $faixa['passo'] > 1 ? ', de ' . $faixa['passo'] . ' em ' . $faixa['passo'] . '.' : '.';

$tituloPagina    = $produto['nome'] . ' — Dolce Delícias';
$descricaoPagina = dd_resumo((string) ($produto['descricao'] ?? ''), 155);

include DD_BASE . '/partials/header.php';
?>

<div class="pagina-produto bg-farinha">

  <!-- Faixa com o caminho até aqui; a categoria abre o catálogo já filtrado -->
  <div class="border-b border-linha bg-polvilho">
    <nav aria-label="Você está aqui" class="mx-auto flex max-w-7xl flex-wrap items-center gap-2 px-4 py-3 text-sm font-semibold text-crust sm:px-6 lg:px-8">
      <a href="/" class="rounded hover:text-brand-escuro">Início</a>
      <span aria-hidden="true">/</span>
      <a href="/?categoria=<?= e(rawurlencode((string) ($produto['categoria'] ?? ''))) ?>#catalogo" class="rounded hover:text-brand-escuro"><?= e((string) ($produto['categoria'] ?? 'Catálogo')) ?></a>
      <span aria-hidden="true">/</span>
      <span class="text-base-content" aria-current="page"><?= e((string) $produto['nome']) ?></span>
    </nav>
  </div>

  <div class="produto-grade mx-auto max-w-7xl lg:px-8">

    <!-- ------------------------------------------------------------------
         FOTO — de ponta a ponta no celular; no computador, a coluna da
         esquerda, parada enquanto o texto rola.
         >>> INTEGRAÇÃO FUTURA <<<
         Hoje existe uma imagem só (o campo 'imagem'). Para virar galeria,
         troque por um array 'imagens' e desenhe as miniaturas embaixo.
         ------------------------------------------------------------------ -->
    <figure class="produto-foto">
      <?php if ($imagem): ?>
        <img src="<?= e($imagem) ?>" alt="<?= e((string) $produto['nome']) ?>" width="960" height="720" fetchpriority="high">
      <?php else: ?>
        <span class="produto-sem-foto" role="img" aria-label="Foto de <?= e((string) $produto['nome']) ?> ainda não cadastrada">
          <?= dd_icone_categoria((string) ($produto['categoria'] ?? ''), (string) ($produto['nome'] ?? '')) ?>
        </span>
      <?php endif; ?>
      <?php if ($ofertas !== []): ?>
        <span class="produto-foto-selo"><?= e(trim((string) ($ofertas[0]['selo'] ?? '')) ?: 'Promoção') ?></span>
      <?php endif; ?>
    </figure>

    <!-- ------------------------------------------------------------------
         INFORMAÇÕES E PEDIDO
         ------------------------------------------------------------------ -->
    <div class="produto-info">
      <h1 class="produto-titulo"><?= e((string) $produto['nome']) ?></h1>

      <?php if (trim((string) ($produto['descricao'] ?? '')) !== ''): ?>
        <p class="produto-descricao"><?= e((string) $produto['descricao']) ?></p>
      <?php endif; ?>

      <?php if ($etiquetas !== []): ?>
        <ul class="produto-etiquetas" aria-label="Etiquetas">
          <?php foreach ($etiquetas as $etiqueta): ?>
            <li><?= e(mb_strtoupper(mb_substr($etiqueta, 0, 1)) . mb_substr($etiqueta, 1)) ?></li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>

      <?php include DD_BASE . '/partials/product-promotions.php'; ?>

      <!-- Compra -->
      <div class="produto-compra">

        <?php
        /**
         * ESCOLHA DA EMBALAGEM
         * Usa o mesmo .opcao da confirmação do pedido: as duas telas fazem a
         * mesma coisa (escolher uma entre várias), então têm o mesmo desenho.
         * A ajuda mostra de uma vez o valor da caixa, o preço por peça e o mínimo.
         */
        ?>
        <?php if ($temVariacoes): ?>
          <fieldset class="mb-5 border-b border-linha pb-5">
            <legend class="produto-subtitulo">Escolha a embalagem</legend>

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
                    $ajuda[] = dd_moeda($f['unitario']) . ' por peça';
                }
                if ($f['temMinimo']) {
                    $ajuda[] = 'mínimo de ' . $f['min'] . ' un';
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
                        <?= e(implode(', ', $ajuda)) ?>
                      </span>
                    <?php endif; ?>
                  </span>
                </label>
              <?php endforeach; ?>
            </div>
          </fieldset>
        <?php endif; ?>

        <p class="produto-preco">
          <span class="text-lg font-bold">R$</span>
          <span class="produto-preco-valor" data-preco-cheio><?= e(number_format($faixa['valor'], 2, ',', '.')) ?></span>
          <span class="text-base font-semibold text-crust" data-preco-por>/ <?= e($faixa['porCurto']) ?></span>
        </p>

        <?php // A ficha: o que a pessoa precisa saber antes de escolher a quantidade. ?>
        <dl class="produto-ficha">
          <div>
            <dt>Como compra</dt>
            <dd><?= e($comoCompra) ?></dd>
          </div>
          <div>
            <dt>Pedido mínimo</dt>
            <dd data-ficha-minimo><?= e((string) $faixa['min']) ?> un</dd>
          </div>
          <?php if ($faixa['base'] > 1): ?>
            <div>
              <dt>Por peça</dt>
              <dd data-preco-unitario><?= e(dd_moeda($faixa['unitario'])) ?></dd>
            </div>
          <?php endif; ?>
        </dl>

        <?php if ($disponivel): ?>
          <!-- Quantidade e subtotal na mesma linha (a ajuda vai embaixo para não alargar o seletor), botão por último -->
          <div class="mt-6">
            <label for="qtd" class="block text-sm font-semibold">Quantidade (peças)</label>
            <div class="mt-1.5 flex items-center justify-between gap-4">
              <div class="seletor-qtd">
                <button type="button" data-qty-menos aria-label="Diminuir quantidade">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14"/></svg>
                </button>
                <input id="qtd" type="number" data-qty-input
                       value="<?= e((string) $faixa['min']) ?>"
                       min="<?= e((string) $faixa['min']) ?>"
                       step="<?= e((string) $faixa['passo']) ?>"
                       inputmode="numeric"
                       aria-describedby="ajuda-qtd">
                <button type="button" data-qty-mais aria-label="Aumentar quantidade">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                </button>
              </div>

              <p class="text-right text-sm text-crust">
                Subtotal<br>
                <strong class="text-xl text-base-content sm:text-2xl" data-subtotal><?= e(dd_moeda($faixa['unitario'] * $faixa['min'])) ?></strong>
              </p>
            </div>
            <p id="ajuda-qtd" class="mt-1.5 text-xs text-crust"><?= e($ajudaQtd) ?></p>
          </div>

          <button type="button"
                  class="botao-primario mt-5 min-h-13 w-full text-base"
                  data-add
                  data-usa-qty
                  data-id="<?= e((string) $produto['slug']) ?>"
                  data-slug="<?= e((string) $produto['slug']) ?>"
                  data-nome="<?= e((string) $produto['nome']) ?>"
                  data-preco="<?= e(number_format($faixa['unitario'], 4, '.', '')) ?>"
                  data-por="<?= e($faixa['exibicao']) ?>"
                  data-min="<?= e((string) $faixa['min']) ?>"
                  data-passo="<?= e((string) $faixa['passo']) ?>"
                  data-base="<?= e((string) $faixa['base']) ?>"
                  <?php // Miniatura do carrinho: a foto quando existir, senão o ícone da categoria. ?>
                  data-imagem="<?= e((string) $imagem) ?>"
                  data-categoria="<?= e((string) ($produto['categoria'] ?? '')) ?>">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            Adicionar ao pedido
          </button>
          <p class="mt-3 text-center text-xs text-crust">Sem pagamento no site. O pedido é fechado pelo WhatsApp da matriz.</p>
        <?php else: ?>
          <p class="mt-6 bg-polvilho p-4 text-sm font-semibold text-crust">
            Este item está fora do cardápio hoje. Fale com a unidade para saber quando volta.
          </p>
        <?php endif; ?>
      </div>

      <?php if ($sabores): ?>
        <section class="produto-bloco" aria-labelledby="sabores-titulo">
          <h2 id="sabores-titulo" class="produto-subtitulo">Sabores</h2>
          <p class="mt-1 text-sm leading-relaxed text-crust">
            Você escolhe a combinação na conversa com a unidade. Dá para misturar.
          </p>
          <ul class="produto-sabores">
            <?php foreach ($sabores as $sabor): ?>
              <li><?= e((string) $sabor) ?></li>
            <?php endforeach; ?>
          </ul>
        </section>
      <?php endif; ?>

      <section class="produto-bloco" aria-labelledby="onde-titulo">
        <h2 id="onde-titulo" class="produto-subtitulo">Onde encontrar</h2>
        <?php if ($ondeTem !== []): ?>
          <ul class="produto-lojas">
            <?php foreach ($ondeTem as $unidadeProduto): ?>
              <li>
                <a href="/unidades#<?= e((string) $unidadeProduto['slug']) ?>">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/></svg>
                  <span>
                    <?= e((string) $unidadeProduto['nome']) ?>
                    <?php if (!empty($unidadeProduto['matriz'])): ?><small>recebe os pedidos do site</small><?php endif; ?>
                  </span>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <p class="mt-2 text-sm text-crust">Nenhuma unidade está vendendo este item agora.</p>
        <?php endif; ?>
      </section>
    </div>
  </div>

  <?php if ($relacionados): ?>
    <section class="border-t border-linha bg-polvilho" aria-labelledby="junto-titulo">
      <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-12">
        <h2 id="junto-titulo" class="text-3xl sm:text-4xl">Vai bem junto</h2>
        <p class="mt-2 text-crust">Outros itens de <?= e((string) ($produto['categoria'] ?? '')) ?>.</p>
        <div class="mt-6 grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-4">
          <?php foreach ($relacionados as $produtoRelacionado): ?>
            <?php
              $produtoOriginal = $produto;
              $produto = $produtoRelacionado;
              $eager = false;
              include DD_BASE . '/partials/product-card.php';
              $produto = $produtoOriginal;
            ?>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>
</div>

<?php include DD_BASE . '/partials/footer.php'; ?>
