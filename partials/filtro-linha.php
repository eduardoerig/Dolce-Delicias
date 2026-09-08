<?php

declare(strict_types=1);

/**
 * partials/filtro-linha.php — encomendas x balcão, como controle segmentado.
 *
 * Entra duas vezes em index.php: na barra completa do catálogo e na barra fina
 * que gruda no topo quando ela sai de vista. Vive num arquivo só para as duas
 * cópias nunca divergirem.
 *
 * Não precisa de JS próprio: assets/js/ui.js escuta [data-filtro-linha] por
 * delegação no document e marca o aria-pressed de TODAS as cópias de uma vez.
 *
 * Espera:
 *   $compacto  bool  opcional. true aperta a trilha para a barra fina.
 */

$compacto = $compacto ?? false;
?>
<div class="flex shrink-0 rounded-full bg-base-200 <?= $compacto ? 'p-0.5' : 'p-1' ?>"
     role="group" aria-label="Tipo de venda">
  <button type="button" data-filtro-linha="" aria-pressed="true"
          class="segmento">Tudo</button>
  <button type="button" data-filtro-linha="atacado" aria-pressed="false"
          class="segmento">Encomendas</button>
  <button type="button" data-filtro-linha="varejo" aria-pressed="false"
          class="segmento">Balcão</button>
</div>
