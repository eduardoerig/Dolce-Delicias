<?php

declare(strict_types=1);

/**
 * partials/footer.php — fecha o <main>, desenha o rodapé, fecha o drawer e o documento.
 * Sempre depois de partials/header.php.
 */

require_once __DIR__ . '/bootstrap.php';

$unidadesRodape = dd_unidades();
$matrizRodape   = dd_matriz();
$anoAtual       = date('Y');

/**
 * RF-27 — feedback de SERVIÇO (o requisito foi generalizado: não é avaliação
 * produto a produto).
 *
 * Sem back-end, o site não guarda avaliação nenhuma — então ele leva a pessoa
 * para onde a avaliação de fato existe. Se a matriz tem link próprio
 * cadastrado, é ele; senão, cai no WhatsApp dela com a frase já escrita, que é
 * o canal que a padaria já lê todo dia.
 */
$zapMatriz    = dd_whatsapp($matrizRodape); // '' quando vazio ou de exemplo
$linkFeedback = trim((string) ($matrizRodape['avaliacao'] ?? ''));

if ($linkFeedback === '' && $zapMatriz !== '') {
    $linkFeedback = 'https://wa.me/' . $zapMatriz . '?text='
        . rawurlencode('Olá! Quero deixar um comentário sobre o atendimento da Dolce Delícias.');
}

/** RF-26 — canais de venda da matriz, para o rodapé. */
$canaisRodape = array_values(array_filter(
    (array) ($matrizRodape['canais'] ?? []),
    static fn ($canal): bool => is_array($canal) && trim((string) ($canal['url'] ?? '')) !== ''
));

// Sem link de avaliação nem outro canal, "Sua opinião" não teria para onde
// mandar ninguém: a coluna some e a grade fica com quatro.
$temOpiniao = $linkFeedback !== '' || $canaisRodape !== [];
?>
    </main>

    <?php // Marrom torrado, como no rodapé original: fecha a página com cor e
       // deixa a placa vermelha do logo saltar. Estilos em .rodape (input.css). ?>
    <footer id="contato" class="rodape">
      <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 sm:grid-cols-2 sm:px-6 lg:gap-8 lg:px-8 lg:py-14 <?= $temOpiniao ? 'lg:grid-cols-[1.4fr_1fr_1fr_1.3fr_1.3fr]' : 'lg:grid-cols-[1.6fr_1fr_1fr_1.4fr]' ?>">

        <div>
          <?= dd_logo('h-12 w-auto') ?>
          <p class="mt-4 max-w-xs text-sm leading-relaxed">
            Padaria e panificadora. Salgados, assados e doces para escolas, eventos e encomendas
            em <?= count($unidadesRodape) ?> unidades.
          </p>
        </div>

        <nav aria-labelledby="rodape-cardapio">
          <h2 id="rodape-cardapio" class="rodape-titulo">Cardápio</h2>
          <ul class="mt-4 space-y-2.5 text-sm">
            <li><a class="rodape-link" href="/#catalogo">Catálogo</a></li>
            <?php if (dd_promocoes_visiveis() !== []): ?>
              <li><a class="rodape-link" href="/#promocoes">Promoções</a></li>
            <?php endif; ?>
            <li><a class="rodape-link" href="/carrinho">Meu pedido</a></li>
          </ul>
        </nav>

        <nav aria-labelledby="rodape-dolce">
          <h2 id="rodape-dolce" class="rodape-titulo">A Dolce</h2>
          <ul class="mt-4 space-y-2.5 text-sm">
            <li><a class="rodape-link" href="/sobre">A empresa</a></li>
            <li><a class="rodape-link" href="/unidades">Nossas unidades</a></li>
          </ul>
        </nav>

        <div>
          <h2 class="rodape-titulo">Matriz</h2>
          <?php if ($matrizRodape): ?>
            <address class="mt-4 space-y-2 text-sm not-italic">
              <p><?= e($matrizRodape['endereco']) ?></p>
              <p><?= e($matrizRodape['horario']) ?></p>
              <?php // RF-30 — o prazo aparece junto do horário em todo o site. ?>
              <?php if (trim((string) ($matrizRodape['preparo'] ?? '')) !== ''): ?>
                <p><?= e($matrizRodape['preparo']) ?></p>
              <?php endif; ?>
            </address>
            <?php if ($zapMatriz !== ''): ?>
              <a href="https://wa.me/<?= e($zapMatriz) ?>"
                 target="_blank" rel="noopener noreferrer"
                 class="botao-primario mt-4">
                Falar no WhatsApp
              </a>
            <?php else: ?>
              <?php // INTEGRAÇÃO FUTURA: cadastre o WhatsApp da matriz no painel e o botão liga sozinho. ?>
              <span class="botao-desligado mt-4">WhatsApp em breve</span>
            <?php endif; ?>
          <?php endif; ?>
        </div>

        <?php if ($temOpiniao): ?>
          <div>
            <?php // RF-27 — feedback de serviço. Ver o cálculo de $linkFeedback no topo. ?>
            <h2 class="rodape-titulo">Sua opinião</h2>
            <?php if ($linkFeedback !== ''): ?>
              <p class="mt-4 text-sm leading-relaxed">
                Conte o que achou do atendimento. Elogio e reclamação ajudam do mesmo jeito.
              </p>
              <a href="<?= e($linkFeedback) ?>" target="_blank" rel="noopener noreferrer"
                 class="rodape-link mt-3 inline-flex min-h-11 items-center text-sm font-bold text-cream underline underline-offset-4">
                Deixar meu comentário
              </a>
            <?php endif; ?>

            <?php // RF-26 — outras plataformas onde a matriz vende. Lista vazia não desenha nada. ?>
            <?php if ($canaisRodape !== []): ?>
              <ul class="mt-3 flex flex-wrap gap-2">
                <?php foreach ($canaisRodape as $canalRodape): ?>
                  <li>
                    <a href="<?= e((string) ($canalRodape['url'] ?? '#')) ?>"
                       target="_blank" rel="noopener noreferrer"
                       class="rodape-canal">
                      <?= e((string) ($canalRodape['nome'] ?? 'Canal')) ?>
                    </a>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>

      <div class="rodape-base">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
          <p class="text-xs">
            © <?= e((string) $anoAtual) ?> Dolce Delícias. Os pedidos são fechados pelo WhatsApp da matriz.
          </p>
          <a href="https://instagram.com/dolcedeliciasoficial" target="_blank" rel="noopener noreferrer"
             class="icone-redondo" aria-label="Instagram da Dolce Delícias (@dolcedeliciasoficial)">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
              <rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/>
            </svg>
          </a>
        </div>
      </div>
    </footer>

  </div><!-- /drawer-content -->

  <?php include __DIR__ . '/cart-drawer.php'; ?>

</div><!-- /drawer -->

<?php
/**
 * Desenhos de placeholder, um por produto e um por categoria (reserva) — os
 * MESMOS que o card do catálogo usa.
 * O carrinho lê daqui quando o item ainda não tem foto, em vez de o JavaScript
 * carregar uma segunda cópia dos SVGs.
 */
?>
<template id="icones-produto">
  <?php foreach (dd_categorias() as $categoriaIcone): ?>
    <div data-icone-categoria="<?= e($categoriaIcone) ?>"><?= dd_icone_categoria($categoriaIcone) ?></div>
  <?php endforeach; ?>
  <div data-icone-categoria=""><?= dd_icone_categoria('') ?></div>
  <?php foreach (dd_produtos() as $produtoIcone): ?>
    <div data-icone-produto="<?= e((string) $produtoIcone['slug']) ?>"><?= dd_icone_categoria((string) $produtoIcone['categoria'], (string) $produtoIcone['nome']) ?></div>
  <?php endforeach; ?>
</template>

<!-- Avisos curtos (item adicionado, item removido). Preenchido por assets/js/ui.js.
     O leitor de tela ouve a frase pela região aria-live ao lado, não os botões. -->
<section class="avisos" data-toast-area aria-label="Avisos"></section>
<p class="sr-only" data-aviso-leitor aria-live="polite" aria-atomic="true"></p>

</body>
</html>
