<?php

declare(strict_types=1);

/**
 * partials/heroi-foto.php — o herói de cada página.
 *
 * Ocupa a primeira tela inteira (abaixo do topo fixo): é a chegada na página.
 * Dois desenhos:
 *
 *   - foto de fundo com uma camada escura por cima e o texto (Seu pedido).
 *     Mobile-first: no celular a foto vem em retrato
 *     (assets/img/heroi/<nome>-celular.jpg, 780 × 1400) e a camada escurece de
 *     baixo para cima; do tablet em diante entra a versão larga (<nome>.jpg,
 *     1920 × 1080) e a camada escurece da esquerda para a direita.
 *
 *   - recorte ($heroiRecorte): a comida recortada, sem fundo, flutuando sobre o
 *     marrom da marca com um halo vermelho atrás (home, A empresa, Unidades).
 *     No celular a arte fica em cima e o texto embaixo; do tablet em diante a
 *     arte vai para a direita. Ela entra uma vez e, ao rolar, sobe devagar
 *     (profundidade); quem prefere menos movimento vê tudo parado. As bordas
 *     dos recortes vieram de um fundo preto e somem sobre o marrom.
 *
 * A imagem é decorativa (alt vazio): quem lê a página recebe o título.
 *
 * Espera:
 *   $heroiFoto      string  nome da foto em assets/img/heroi/ (sem extensão)
 *   $heroiRecorte   array   opcional: ['src' => 'coxinhas'|'garfo'|'tigela'], o recorte
 *                           em assets/img/recortes/; quando vem, substitui a foto
 *   $heroiConteudo  string  HTML do texto (o título h1 vem dentro dele)
 *   $heroiTitulo    string  id do h1, para o aria-labelledby
 *   $heroiCaminho   string  opcional: nome da página no "Início / ..." acima do texto
 *   $heroiBaixo     bool    opcional: herói baixo em vez de tela cheia, para páginas de tarefa (Seu pedido)
 */

require_once __DIR__ . '/bootstrap.php';

// Larguras geradas de cada recorte (assets/img/recortes/<nome>-<largura>.webp).
$recortesLarguras = [
    'coxinhas' => [600, 1000],
    'garfo'    => [360, 600],
    'tigela'   => [520, 900],
];

$heroiRecorteNome = (string) ($heroiRecorte['src'] ?? '');
$heroiRecorteSrc  = [];
foreach ($recortesLarguras[$heroiRecorteNome] ?? [] as $largura) {
    $caminho = dd_imagem("/assets/img/recortes/{$heroiRecorteNome}-{$largura}.webp");
    if ($caminho) $heroiRecorteSrc[$largura] = $caminho;
}
$ehRecorte = $heroiRecorteNome !== '' && isset($recortesLarguras[$heroiRecorteNome]);

$heroiLarga   = $ehRecorte ? null : dd_imagem('/assets/img/heroi/' . ($heroiFoto ?? '') . '.jpg');
$heroiCelular = $ehRecorte ? null : (dd_imagem('/assets/img/heroi/' . ($heroiFoto ?? '') . '-celular.jpg') ?? $heroiLarga);

$heroiClasses = 'heroi-foto ' . ($ehRecorte ? 'heroi-foto-recorte heroi-recorte-' . $heroiRecorteNome : 'heroi-foto-escuro')
    . (!empty($heroiBaixo) ? ' heroi-foto-baixo' : '');
?>
<section class="<?= e($heroiClasses) ?>" aria-labelledby="<?= e((string) ($heroiTitulo ?? '')) ?>">
  <?php if ($ehRecorte): ?>
    <div class="heroi-recorte" aria-hidden="true">
      <?php if ($heroiRecorteSrc !== []): ?>
        <?php
        $srcset = implode(', ', array_map(static fn ($l, $c) => "{$c} {$l}w", array_keys($heroiRecorteSrc), $heroiRecorteSrc));
        $menor  = $heroiRecorteSrc[min(array_keys($heroiRecorteSrc))];
        ?>
        <img src="<?= e($menor) ?>" srcset="<?= e($srcset) ?>" sizes="(min-width: 48rem) 45vw, 80vw" alt="" fetchpriority="high" decoding="async">
      <?php endif; ?>
    </div>
  <?php elseif ($heroiCelular): ?>
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
