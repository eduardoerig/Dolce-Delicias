<?php

declare(strict_types=1);

/**
 * partials/promocoes.php — as ofertas da home.
 *
 * RF-14 (promoção de quarta) e RF-20 (baixa temporada) desenham igual: as duas
 * são "uma oferta com uma data". O que muda é só a agenda, e quem resolve isso
 * é dd_promocoes_visiveis() em partials/bootstrap.php — aqui não há nenhuma
 * regra de calendário.
 *
 * Nada cadastrado (ou tudo com 'ativo' => false) não desenha seção nenhuma: a
 * home não pode ter um buraco chamado "Promoções" com vazio dentro.
 *
 * Cada oferta é uma faixa larga, uma por linha, cantos retos como o resto do
 * site: à esquerda o desconto em letra de cartaz sobre o vermelho, com "Vale
 * hoje" ou "Programe-se"; no meio o nome, a frase, quando e onde vale; à
 * direita a foto dos primeiros produtos, que é a pergunta seguinte de quem lê
 * "20% OFF". Se a oferta tem mais produtos, um link leva ao catálogo, onde
 * cada card já mostra o selo.
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

// Fotos por oferta: passando disso, o resto vira o link para o catálogo.
$promoMaxProdutos = 4;

$promoIconeQuando = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3.5" y="5" width="17" height="15" rx="1.5"/><path d="M3.5 10h17M8 3v4M16 3v4"/></svg>';
$promoIconeOnde   = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-6.5-5.6-6.5-11a6.5 6.5 0 0 1 13 0c0 5.4-6.5 11-6.5 11Z"/><circle cx="12" cy="10" r="2.3"/></svg>';
?>
<section id="promocoes" class="ofertas" aria-labelledby="titulo-promocoes">
  <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14">

    <div class="flex flex-wrap items-baseline justify-between gap-x-6 gap-y-1">
      <h2 id="titulo-promocoes" class="text-3xl sm:text-4xl">Promoções</h2>
      <p class="text-crust">
        <?= $promoTemVigente ? 'Tem oferta valendo hoje.' : 'As ofertas da semana, para você se programar.' ?>
      </p>
    </div>

    <ul class="ofertas-lista">
      <?php foreach ($promocoes as $promocao): ?>
        <?php
        $promoVigente  = !empty($promocao['vigente']);
        $promoTitulo   = (string) ($promocao['titulo'] ?? '');
        $promoSelo     = trim((string) ($promocao['selo'] ?? ''));
        $promoTexto    = trim((string) ($promocao['texto'] ?? ''));
        $promoLojas    = implode(', ', array_unique(array_column((array) ($promocao['pares'] ?? []), 'nome')));
        $promoProdutos = dd_promocao_produtos($promocao);
        $promoTotal    = count($promoProdutos);
        ?>
        <li class="oferta">
          <div class="oferta-selo">
            <p class="oferta-desconto"><?= e($promoSelo !== '' ? $promoSelo : 'Oferta') ?></p>
            <p class="<?= $promoVigente ? 'oferta-quando oferta-quando-hoje' : 'oferta-quando' ?>">
              <?= $promoVigente ? 'Vale hoje' : 'Programe-se' ?>
            </p>
          </div>

          <div class="oferta-corpo">
            <h3 class="oferta-titulo"><?= e($promoTitulo) ?></h3>
            <?php if ($promoTexto !== ''): ?>
              <p class="oferta-texto"><?= e($promoTexto) ?></p>
            <?php endif; ?>
            <ul class="oferta-regras">
              <li><?= $promoIconeQuando ?><?= e(dd_promocao_regra($promocao)) ?></li>
              <?php if ($promoLojas !== ''): ?>
                <li><?= $promoIconeOnde ?>Vale em: <?= e($promoLojas) ?></li>
              <?php endif; ?>
            </ul>
          </div>

          <?php // Sem botão de "adicionar": a promoção não é um item do carrinho. O
             // caminho é abrir o produto e pedir normalmente; a matriz aplica o
             // desconto na conversa. ?>
          <?php if ($promoProdutos !== []): ?>
            <div class="oferta-produtos">
              <ul aria-label="Produtos da promoção <?= e($promoTitulo) ?>">
                <?php foreach (array_slice($promoProdutos, 0, $promoMaxProdutos) as $promoProduto): ?>
                  <?php $promoFoto = dd_imagem($promoProduto['imagem'] ?? null); ?>
                  <li>
                    <a href="/produtos/<?= e(rawurlencode((string) $promoProduto['slug'])) ?>" class="oferta-produto">
                      <span class="oferta-produto-foto">
                        <?php if ($promoFoto): ?>
                          <img src="<?= e($promoFoto) ?>" alt="" width="800" height="600" loading="lazy" decoding="async">
                        <?php else: ?>
                          <?= dd_icone_categoria((string) ($promoProduto['categoria'] ?? ''), (string) ($promoProduto['nome'] ?? '')) ?>
                        <?php endif; ?>
                      </span>
                      <span class="oferta-produto-nome"><?= e((string) $promoProduto['nome']) ?></span>
                    </a>
                  </li>
                <?php endforeach; ?>
              </ul>
              <?php if ($promoTotal > $promoMaxProdutos): ?>
                <a href="#catalogo" class="oferta-todos">
                  Ver os <?= e((string) $promoTotal) ?> produtos com desconto
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14"/><path d="m6 13 6 6 6-6"/></svg>
                </a>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>

    <p class="mt-4 text-sm leading-relaxed text-crust">
      O site mostra o preço cheio; o desconto é aplicado pela matriz no fechamento pelo WhatsApp.
    </p>
  </div>
</section>
