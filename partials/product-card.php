<?php

declare(strict_types=1);

/**
 * partials/product-card.php — um card do catálogo.
 *
 * Desenho de cardápio: foto em cima, selos no canto, nome, uma linha de
 * descrição, como se compra e o mínimo, e embaixo o preço com o botão "+".
 *
 * Espera:
 *   $produto  array  item vindo de dd_produtos()
 *   $eager    bool   opcional. true carrega a imagem sem lazy (use nos primeiros cards).
 *
 * Os data-* do <article> alimentam busca, filtros e ordenação em assets/js/ui.js;
 * os do botão "+" alimentam o carrinho em assets/js/cart.js. Não remova.
 */

require_once __DIR__ . '/bootstrap.php';

/** @var array $produto */
$eager ??= false;

$faixa      = dd_faixa_principal($produto);
$disponivel = ($produto['disponivel'] ?? true) !== false;
$imagem     = dd_imagem($produto['imagem'] ?? null);
$url        = '/produtos/' . rawurlencode((string) $produto['slug']);
$linhaCard  = (string) ($produto['linha'] ?? 'AMBOS');
$ofertas    = dd_promocoes_produto($produto);
$selo       = $ofertas !== [] ? (trim((string) ($ofertas[0]['selo'] ?? '')) ?: 'Promoção') : '';

// Como se compra, em uma palavra: é o que a referência chamava de "porção".
$comoCompra = match ($linhaCard) {
    'ENCOMENDA' => 'Encomenda',
    'BALCAO'    => 'Balcão',
    default     => 'Encomenda e balcão',
};
?>
<article
  class="cartao"
  data-produto
  data-slug="<?= e((string) $produto['slug']) ?>"
  data-categoria="<?= e((string) $produto['categoria']) ?>"
  data-linha="<?= e($linhaCard) ?>"
  data-tags="<?= e(implode(',', $produto['tags'])) ?>"
  data-busca="<?= e(dd_indice_busca($produto)) ?>"
  data-nome="<?= e(dd_ascii((string) $produto['nome'])) ?>"
  data-unitario="<?= e(number_format($faixa['unitario'], 4, '.', '')) ?>"
  data-destaque="<?= !empty($produto['destaque']) ? '1' : '0' ?>">

  <?php
  // A foto também leva ao produto (alvo grande para o dedo). tabindex/aria-hidden
  // porque é link repetido: teclado e leitor de tela chegam pelo nome.
  ?>
  <a href="<?= e($url) ?>" class="cartao-foto" tabindex="-1" aria-hidden="true">
    <?php if ($imagem): ?>
      <img src="<?= e($imagem) ?>" alt=""
           <?= $eager ? '' : 'loading="lazy" decoding="async"' ?>
           width="480" height="360">
    <?php else: ?>
      <?php // INTEGRAÇÃO FUTURA: assim que o arquivo em 'imagem' existir, ele entra aqui sozinho. ?>
      <span class="cartao-sem-foto"><?= dd_icone_categoria((string) ($produto['categoria'] ?? '')) ?></span>
    <?php endif; ?>
  </a>

  <?php if ($selo !== '' || !empty($produto['destaque'])): ?>
    <p class="cartao-selos">
      <?php if ($selo !== ''): ?><span class="selo selo-promo"><?= e($selo) ?></span><?php endif; ?>
      <?php if (!empty($produto['destaque'])): ?><span class="selo selo-destaque">Destaque</span><?php endif; ?>
    </p>
  <?php endif; ?>

  <div class="cartao-corpo">
    <h3 class="cartao-nome">
      <a href="<?= e($url) ?>"><?= e((string) $produto['nome']) ?></a>
    </h3>

    <?php if (trim((string) ($produto['descricao'] ?? '')) !== ''): ?>
      <p class="cartao-descricao"><?= e((string) $produto['descricao']) ?></p>
    <?php endif; ?>

    <p class="cartao-meta">
      <span>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9.5 12 4l9 5.5v9L12 23l-9-4.5z"/><path d="M3 9.5 12 15l9-5.5M12 15v8"/></svg>
        <?= e($comoCompra) ?>
      </span>
      <?php if ($faixa['temMinimo'] && $faixa['min'] > 1): ?>
        <span>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16M4 12h10M4 17h6"/></svg>
          mín. <?= e((string) $faixa['min']) ?> un
        </span>
      <?php endif; ?>
    </p>

    <div class="cartao-rodape">
      <p class="cartao-preco">
        <strong><?= e(str_replace(' ', "\u{00A0}", dd_moeda($faixa['valor']))) ?></strong>
        <span>/ <?= e($faixa['porCurto']) ?></span>
      </p>

      <?php if ($disponivel): ?>
        <button type="button"
                class="botao-mais"
                aria-label="Adicionar <?= e((string) $produto['nome']) ?> ao pedido"
                data-add
                data-id="<?= e((string) $produto['slug']) ?>"
                data-slug="<?= e((string) $produto['slug']) ?>"
                data-nome="<?= e((string) $produto['nome']) ?>"
                data-preco="<?= e(number_format($faixa['unitario'], 4, '.', '')) ?>"
                data-por="<?= e($faixa['exibicao']) ?>"
                data-min="<?= e((string) $faixa['min']) ?>"
                data-passo="<?= e((string) $faixa['passo']) ?>"
                data-base="<?= e((string) $faixa['base']) ?>"
                data-imagem="<?= e((string) $imagem) ?>"
                data-categoria="<?= e((string) ($produto['categoria'] ?? '')) ?>">
          <svg class="icone-mais" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
          <svg class="icone-feito" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 5 5 9-10"/></svg>
        </button>
      <?php else: ?>
        <span class="cartao-indisponivel">Indisponível hoje</span>
      <?php endif; ?>
    </div>
  </div>
</article>
