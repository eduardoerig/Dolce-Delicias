<?php

declare(strict_types=1);

/**
 * partials/heroi-foto.php — o herói de cada página: foto de fundo, uma camada
 * escura por cima e o texto.
 *
 * Ocupa a primeira tela inteira (abaixo do topo fixo): é a chegada na página.
 *
 * Mobile-first: no celular a foto vem recortada em retrato
 * (assets/img/heroi/<nome>-celular.jpg, 780 × 1400) e a camada escurece de
 * baixo para cima, porque o texto fica embaixo; do tablet em diante entra a
 * versão larga (<nome>.jpg, 1920 × 1080) e a camada escurece da esquerda para
 * a direita, onde fica o texto. O navegador só baixa a versão do tamanho da tela.
 *
 * A foto é decorativa (alt vazio): quem lê a página recebe o título.
 *
 * Espera:
 *   $heroiFoto      string  nome da foto em assets/img/heroi/ (sem extensão)
 *   $heroiConteudo  string  HTML do texto (o título h1 vem dentro dele)
 *   $heroiTitulo    string  id do h1, para o aria-labelledby
 *   $heroiTom       string  'escuro' (padrão) ou 'vitrine' (a home: escuro só onde há
 *                           texto, a comida fica viva no resto)
 *   $heroiCaminho   string  opcional: nome da página no "Início / ..." acima do texto
 *   $heroiBaixo     bool    opcional: herói baixo em vez de tela cheia, para páginas de tarefa (Seu pedido)
 */

require_once __DIR__ . '/bootstrap.php';

$heroiTom      = ($heroiTom ?? 'escuro') === 'vitrine' ? 'vitrine' : 'escuro';
$heroiLarga    = dd_imagem('/assets/img/heroi/' . ($heroiFoto ?? '') . '.jpg');
$heroiCelular  = dd_imagem('/assets/img/heroi/' . ($heroiFoto ?? '') . '-celular.jpg') ?? $heroiLarga;
?>
<section class="heroi-foto heroi-foto-<?= e($heroiTom) ?><?= !empty($heroiBaixo) ? ' heroi-foto-baixo' : '' ?>" aria-labelledby="<?= e((string) ($heroiTitulo ?? '')) ?>">
  <?php if ($heroiCelular): ?>
    <picture class="heroi-foto-fundo">
      <?php if ($heroiLarga && $heroiLarga !== $heroiCelular): ?>
        <source media="(min-width: 48rem)" srcset="<?= e($heroiLarga) ?>" width="1920" height="1080">
      <?php endif; ?>
      <img src="<?= e($heroiCelular) ?>" alt="" width="780" height="1400" fetchpriority="high" decoding="async">
    </picture>
  <?php endif; ?>

  <div class="heroi-foto-conteudo mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
    <?php if (!empty($heroiCaminho)): ?>
      <nav aria-label="Você está aqui" class="mb-4 flex items-center gap-2 text-sm font-semibold">
        <a href="/" class="rounded underline-offset-4 hover:underline">Início</a>
        <span aria-hidden="true">/</span>
        <span aria-current="page"><?= e((string) $heroiCaminho) ?></span>
      </nav>
    <?php endif; ?>
    <?= $heroiConteudo ?? '' ?>
  </div>
</section>
