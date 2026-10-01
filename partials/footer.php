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
$zapMatriz  = preg_replace('/\D+/', '', (string) ($matrizRodape['whatsapp'] ?? ''));
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
?>
    </main>

    <footer id="contato" class="border-t border-base-300 bg-papel">
      <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 sm:grid-cols-2 sm:px-6 lg:grid-cols-[1.4fr_1fr_1fr_1.3fr_1.3fr] lg:gap-8 lg:px-8 lg:py-14">

        <div>
          <?= dd_logo('h-12 w-auto') ?>
          <p class="mt-4 max-w-xs text-sm leading-relaxed text-crust">
            Padaria e panificadora. Salgados, assados e doces para escolas, eventos e encomendas
            em <?= count($unidadesRodape) ?> unidades.
          </p>
        </div>

        <nav aria-labelledby="rodape-cardapio">
          <h2 id="rodape-cardapio" class="rodape-titulo">Cardápio</h2>
          <ul class="mt-4 space-y-2.5 text-sm text-crust">
            <li><a class="rounded hover:text-brand" href="/#catalogo">Catálogo</a></li>
            <?php if (dd_promocoes_visiveis() !== []): ?>
              <li><a class="rounded hover:text-brand" href="/#promocoes">Promoções</a></li>
            <?php endif; ?>
            <li><a class="rounded hover:text-brand" href="/carrinho">Meu pedido</a></li>
          </ul>
        </nav>

        <nav aria-labelledby="rodape-dolce">
          <h2 id="rodape-dolce" class="rodape-titulo">A Dolce</h2>
          <ul class="mt-4 space-y-2.5 text-sm text-crust">
            <li><a class="rounded hover:text-brand" href="/sobre">A empresa</a></li>
            <li><a class="rounded hover:text-brand" href="/unidades">Nossas unidades</a></li>
            <li><a class="rounded hover:text-brand" href="/sobre#empresas">Para empresas</a></li>
          </ul>
        </nav>

        <div>
          <h2 class="rodape-titulo">Matriz</h2>
          <?php if ($matrizRodape): ?>
            <address class="mt-4 space-y-2 text-sm not-italic text-crust">
              <p><?= e($matrizRodape['endereco']) ?></p>
              <p><?= e($matrizRodape['horario']) ?></p>
              <?php // RF-30 — o prazo aparece junto do horário em todo o site. ?>
              <?php if (trim((string) ($matrizRodape['preparo'] ?? '')) !== ''): ?>
                <p><?= e($matrizRodape['preparo']) ?></p>
              <?php endif; ?>
            </address>
            <a href="https://wa.me/<?= e($matrizRodape['whatsapp']) ?>"
               target="_blank" rel="noopener noreferrer"
               class="botao-amarelo mt-4">
              Falar no WhatsApp
            </a>
          <?php endif; ?>
        </div>

        <div>
          <?php // RF-27 — feedback de serviço. Ver o cálculo de $linkFeedback no topo. ?>
          <h2 class="rodape-titulo">Sua opinião</h2>
          <?php if ($linkFeedback !== ''): ?>
            <p class="mt-4 text-sm leading-relaxed text-crust">
              Conte o que achou do atendimento. Elogio e reclamação ajudam do mesmo jeito.
            </p>
            <a href="<?= e($linkFeedback) ?>" target="_blank" rel="noopener noreferrer"
               class="mt-3 inline-flex min-h-11 items-center text-sm font-bold text-brand underline underline-offset-4">
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
                     class="inline-flex min-h-11 items-center gap-1.5 rounded-full border border-base-300 px-3.5 text-sm font-semibold hover:border-campo">
                    <?= e((string) ($canalRodape['nome'] ?? 'Canal')) ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      </div>

      <div class="border-t border-base-300">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
          <p class="text-xs text-crust">
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
 * Ícones de placeholder, um por categoria — os MESMOS que o card do catálogo usa.
 * O carrinho lê daqui quando o item ainda não tem foto, em vez de o JavaScript
 * carregar uma segunda cópia dos SVGs.
 */
?>
<template id="icones-produto">
  <?php foreach (dd_categorias() as $categoriaIcone): ?>
    <div data-icone-categoria="<?= e($categoriaIcone) ?>"><?= dd_icone_categoria($categoriaIcone) ?></div>
  <?php endforeach; ?>
  <div data-icone-categoria=""><?= dd_icone_categoria('') ?></div>
</template>

<!-- Avisos curtos (item adicionado, item removido). Preenchido por assets/js/ui.js. -->
<div class="toast toast-end z-[80] p-4" data-toast-area aria-live="polite" aria-atomic="true"></div>

</body>
</html>
