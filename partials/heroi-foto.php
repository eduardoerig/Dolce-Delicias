<?php

declare(strict_types=1);

/**
 * partials/heroi-foto.php — herói com foto de fundo (Seu pedido).
 *
 * Foto de fundo com uma camada escura por cima e o texto. Mobile-first: no
 * celular a foto vem em retrato (assets/img/heroi/<nome>-celular.jpg,
 * 780 × 1400) e a camada escurece de baixo para cima; do tablet em diante
 * entra a versão larga (<nome>.jpg, 1920 × 1080) e a camada escurece da
 * esquerda para a direita.
 *
 * A home, A empresa e Unidades têm heróis próprios (partials/hero.php e as
 * próprias páginas).
 *
 * A imagem é decorativa (alt vazio): quem lê a página recebe o título.
 *
 * Espera:
 *   $heroiFoto      string  nome da foto em assets/img/heroi/ (sem extensão)
 *   $heroiConteudo  string  HTML do texto (o título h1 vem dentro dele)
 *   $heroiTitulo    string  id do h1, para o aria-labelledby
 *   $heroiCaminho   string  opcional: nome da página no "Início / ..." acima do texto
 *   $heroiBaixo     bool    opcional: herói baixo em vez de tela cheia, para páginas de tarefa (Seu pedido)
 */

require_once __DIR__ . '/bootstrap.php';

$heroiLarga   = dd_imagem('/assets/img/heroi/' . ($heroiFoto ?? '') . '.jpg');
$heroiCelular = dd_imagem('/assets/img/heroi/' . ($heroiFoto ?? '') . '-celular.jpg') ?? $heroiLarga;

$heroiClasses = 'heroi-foto heroi-foto-escuro' . (!empty($heroiBaixo) ? ' heroi-foto-baixo' : '');
?>
<section class="<?= e($heroiClasses) ?>" aria-labelledby="<?= e((string) ($heroiTitulo ?? '')) ?>">
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
