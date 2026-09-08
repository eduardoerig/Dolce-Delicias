<?php

declare(strict_types=1);

/**
 * partials/catalog-menu.php — os <li> do menu "Catálogo".
 *
 * Cada unidade vende alguns produtos diferentes, então o catálogo é um PDF por
 * loja. O último item é o catálogo da rede inteira.
 *
 * Entra duas vezes em partials/header.php: dentro do dropdown do desktop e
 * achatado no menu do celular — por isso vive num arquivo só.
 *
 * Uma linha por item: o ícone de download já diz que baixa, então não há
 * subtítulo repetindo "PDF desta unidade" seis vezes.
 *
 * Espera:
 *   $itensCatalogo    array       montado no header
 *   $pdfCompleto      string|null caminho do PDF geral, ou null se não existir
 *   $somenteArquivos  bool        true esconde o item "Ver catálogo no site"
 *                                 (o menu do celular já tem esse link em cima)
 */

require_once __DIR__ . '/bootstrap.php';

/** @var array       $itensCatalogo */
/** @var string|null $pdfCompleto */
$somenteArquivos = $somenteArquivos ?? false;

/** Ícone de download, repetido em todos os itens que baixam arquivo. */
$setaBaixar = '<svg class="ml-auto h-4 w-4 shrink-0 opacity-45" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v12"/><path d="m7 11 5 5 5-5"/><path d="M5 21h14"/></svg>';
?>
<?php foreach ($itensCatalogo as $itemCatalogo): ?>
  <?php if ($somenteArquivos && empty($itemCatalogo['arquivo'])) { continue; } ?>
  <li>
    <?php if (empty($itemCatalogo['tem'])): ?>
      <?php // Sem arquivo no disco: mostra desabilitado em vez de link quebrado. ?>
      <span class="pointer-events-none flex items-center gap-2 rounded-xl px-3 py-2 opacity-50" aria-disabled="true">
        <?= e($itemCatalogo['texto']) ?>
        <span class="ml-auto text-xs">em breve</span>
      </span>
    <?php else: ?>
      <a href="<?= e($itemCatalogo['href']) ?>"<?= !empty($itemCatalogo['arquivo']) ? ' download' : '' ?>
         class="flex items-center gap-2 rounded-xl px-3 py-2 font-medium">
        <?= e($itemCatalogo['texto']) ?>
        <?= !empty($itemCatalogo['arquivo']) ? $setaBaixar : '' ?>
      </a>
    <?php endif; ?>
  </li>
<?php endforeach; ?>

<?php if ($pdfCompleto !== null): ?>
  <li class="mx-3 my-1 border-t border-base-300" aria-hidden="true"></li>
  <li>
    <a href="<?= e($pdfCompleto) ?>" download
       class="flex items-center gap-2 rounded-xl px-3 py-2 font-bold text-brand">
      Baixar PDF completo
      <?= $setaBaixar ?>
    </a>
  </li>
<?php endif; ?>
