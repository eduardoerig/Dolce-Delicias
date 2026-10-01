<?php

declare(strict_types=1);

/**
 * partials/promocoes.php — a faixa de ofertas da home.
 *
 * RF-14 (promoção de quarta) e RF-20 (baixa temporada) desenham igual: as duas
 * são "uma oferta com uma data". O que muda é só a agenda, e quem resolve isso
 * é dd_promocoes_visiveis() em partials/bootstrap.php — aqui não há nenhuma
 * regra de calendário.
 *
 * Nada cadastrado (ou tudo com 'ativo' => false) não desenha seção nenhuma: a
 * home não pode ter um buraco chamado "Promoções" com vazio dentro.
 *
 * Mesmo cartão da página do produto (.promo-produto): selo, "Vale hoje" ou
 * "Programe-se" e a regra numa frase. Aqui ele ainda lista os produtos da
 * oferta, que é a pergunta seguinte de quem lê "15% OFF".
 *
 * ATENÇÃO: include divide o escopo com a página (a home usa $produtos, $selo…),
 * então toda variável daqui leva o prefixo "promo".
 *
 * Espera:
 *   $promocoesEm  DateTimeInterface|null  opcional. A data que decide o que
 *                 aparece; o padrão é hoje. Existe para dar para conferir as
 *                 campanhas de julho e dezembro sem esperar julho e dezembro.
 */

require_once __DIR__ . '/bootstrap.php';

$promocoes = dd_promocoes_visiveis($promocoesEm ?? null);

if ($promocoes === []) {
    return;
}

$promoTemVigente = (bool) array_filter($promocoes, static fn (array $p): bool => !empty($p['vigente']));

// Produtos por cartão: passando disso, o resto vira "e mais N".
$promoMaxProdutos = 4;
?>
<section id="promocoes" class="border-b border-linha bg-farinha" aria-labelledby="titulo-promocoes">
  <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-10">

    <div class="flex flex-wrap items-baseline justify-between gap-x-6 gap-y-1">
      <h2 id="titulo-promocoes" class="text-3xl sm:text-4xl">Promoções</h2>
      <p class="text-crust">
        <?= $promoTemVigente ? 'Tem oferta valendo hoje.' : 'As ofertas da semana, para você se programar.' ?>
      </p>
    </div>

    <ul class="mt-5 grid gap-3 sm:gap-4 md:grid-cols-2 lg:grid-cols-3">
      <?php foreach ($promocoes as $promocao): ?>
        <?php
        $promoVigente  = !empty($promocao['vigente']);
        $promoSelo     = trim((string) ($promocao['selo'] ?? ''));
        $promoTexto    = trim((string) ($promocao['texto'] ?? ''));
        $promoLojas    = implode(', ', array_unique(array_column((array) ($promocao['pares'] ?? []), 'nome')));
        $promoProdutos = dd_promocao_produtos($promocao);
        $promoSobram   = count($promoProdutos) - $promoMaxProdutos;
        ?>
        <li class="promo-produto flex flex-col">
          <p class="flex flex-wrap items-center gap-2">
            <span class="selo selo-promo"><?= e($promoSelo !== '' ? $promoSelo : 'Promoção') ?></span>
            <span class="text-xs font-bold <?= $promoVigente ? 'text-success' : 'text-crust' ?>">
              <?= $promoVigente ? 'Vale hoje' : 'Programe-se' ?>
            </span>
          </p>

          <h3 class="mt-2 font-sans text-lg font-bold"><?= e((string) ($promocao['titulo'] ?? '')) ?></h3>
          <?php if ($promoTexto !== ''): ?>
            <p class="mt-1 text-sm leading-relaxed text-crust"><?= e($promoTexto) ?></p>
          <?php endif; ?>

          <p class="mt-2 text-xs leading-relaxed text-crust">
            <?= e(dd_promocao_regra($promocao)) ?>.<?php if ($promoLojas !== ''): ?> Vale em: <?= e($promoLojas) ?>.<?php endif; ?>
          </p>

          <?php // Sem botão de "adicionar": a promoção não é um item do carrinho. O
             // caminho é abrir o produto e pedir normalmente; a matriz aplica o
             // desconto na conversa. ?>
          <?php if ($promoProdutos !== []): ?>
            <ul class="mt-auto flex flex-wrap items-center gap-2 pt-3" aria-label="Produtos da promoção <?= e((string) ($promocao['titulo'] ?? '')) ?>">
              <?php foreach (array_slice($promoProdutos, 0, $promoMaxProdutos) as $promoProduto): ?>
                <li>
                  <a href="/produtos/<?= e(rawurlencode((string) $promoProduto['slug'])) ?>" class="pilula pilula-link">
                    <?= e((string) $promoProduto['nome']) ?>
                  </a>
                </li>
              <?php endforeach; ?>
              <?php if ($promoSobram > 0): ?>
                <li class="text-sm font-semibold text-crust">e mais <?= e((string) $promoSobram) ?></li>
              <?php endif; ?>
            </ul>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>

    <p class="mt-4 text-xs leading-relaxed text-crust">
      O site mostra o preço cheio; o desconto é aplicado pela matriz no fechamento pelo WhatsApp.
    </p>
  </div>
</section>
