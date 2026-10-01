<?php

declare(strict_types=1);

/**
 * partials/hero-bandeja.php — a bandeja do cento, desenhada.
 *
 * Enquanto não há foto de produto, o herói mostra o que a pessoa vai receber:
 * uma bandeja cheia, vista de cima, com coxinha, bolinha, empada e pão de
 * queijo. As peças são posicionadas aqui no PHP (grade dentro da elipse da
 * bandeja), não à mão: mudar o tamanho da bandeja redistribui tudo.
 *
 * Decorativa (aria-hidden): o texto do herói já diz o que ela mostra.
 *
 * >>> INTEGRAÇÃO FUTURA — FOTO <<<
 * Quando houver foto boa de uma bandeja de verdade, troque o <svg> por uma
 * <img> com o mesmo tamanho; o resto do herói não muda.
 */

// Bandeja: elipse centrada em (260, 236).
$cx = 260;
$cy = 248;
$rx = 228;
$ry = 158;

$tipos = ['coxinha', 'bolinha', 'empada', 'paoqueijo'];
$pecas = [];
$linha = 0;
for ($y = $cy - $ry + 34; $y <= $cy + $ry - 26; $y += 31, $linha++) {
    $desloca = $linha % 2 === 0 ? 0 : 18; // fileiras alternadas, como se arruma na bandeja
    for ($x = $cx - $rx + 28 + $desloca; $x <= $cx + $rx - 24; $x += 36) {
        // Só entra se couber inteira dentro da borda da bandeja.
        $dentro = (($x - $cx) ** 2) / (($rx - 24) ** 2) + (($y - $cy) ** 2) / (($ry - 20) ** 2);
        if ($dentro <= 1) {
            // Giro e folga pequenos e fixos (nada de rand): ninguém arruma salgado em régua.
            $n = count($pecas);
            $pecas[] = [$x + (($n * 7) % 5) - 2, $y + (($n * 3) % 4) - 1.5, $tipos[$linha % 4], (($n * 37) % 41) - 20];
        }
    }
}
?>
<svg class="bandeja" viewBox="20 30 490 400" role="presentation" aria-hidden="true" focusable="false">
  <defs>
    <symbol id="s-coxinha" viewBox="-20 -24 40 48" overflow="visible">
      <path d="M0 -21C8 -12 16 -1 15 8 14 16 7 20 0 20-7 20-14 16-15 8-16-1-8-12 0-21Z" fill="#e39a3b" stroke="#a9561a" stroke-width="1.6"/>
      <path d="M-6 -2C-8 4-7 10-3 13" fill="none" stroke="#f7c46a" stroke-width="3" stroke-linecap="round"/>
      <circle cx="4" cy="6" r="1.2" fill="#b8661f"/><circle cx="7" cy="12" r="1" fill="#b8661f"/><circle cx="-2" cy="15" r="1" fill="#b8661f"/>
    </symbol>
    <symbol id="s-bolinha" viewBox="-20 -20 40 40" overflow="visible">
      <circle r="15" fill="#e7a446" stroke="#a9561a" stroke-width="1.6"/>
      <circle cx="-5" cy="-5" r="5" fill="#f7c46a" opacity=".85"/>
      <circle cx="5" cy="3" r="1.2" fill="#b8661f"/><circle cx="-2" cy="8" r="1" fill="#b8661f"/><circle cx="8" cy="-4" r="1" fill="#b8661f"/>
    </symbol>
    <symbol id="s-empada" viewBox="-20 -20 40 40" overflow="visible">
      <circle r="16" fill="#eab35a" stroke="#a9561a" stroke-width="1.6" stroke-dasharray="3.2 2.2"/>
      <circle r="10.5" fill="#c97b2d"/>
      <path d="M-5 -3C-2 -6 3 -6 6 -2" fill="none" stroke="#e9a54b" stroke-width="2.2" stroke-linecap="round"/>
    </symbol>
    <symbol id="s-paoqueijo" viewBox="-20 -20 40 40" overflow="visible">
      <circle r="15" fill="#f2d48a" stroke="#c08a2e" stroke-width="1.6"/>
      <circle cx="-4" cy="-3" r="2.4" fill="#d9a441"/><circle cx="5" cy="-6" r="1.6" fill="#d9a441"/><circle cx="3" cy="6" r="2" fill="#d9a441"/><circle cx="-6" cy="7" r="1.3" fill="#d9a441"/>
    </symbol>
  </defs>

  <!-- sombra e bandeja -->
  <ellipse cx="<?= $cx ?>" cy="<?= $cy + 18 ?>" rx="<?= $rx + 4 ?>" ry="<?= $ry - 4 ?>" fill="#7a0412" opacity=".35"/>
  <ellipse cx="<?= $cx ?>" cy="<?= $cy ?>" rx="<?= $rx ?>" ry="<?= $ry ?>" fill="#fbf4ee" stroke="#eadacb" stroke-width="3"/>
  <ellipse cx="<?= $cx ?>" cy="<?= $cy ?>" rx="<?= $rx - 16 ?>" ry="<?= $ry - 14 ?>" fill="none" stroke="#f0e1d2" stroke-width="2" stroke-dasharray="2 7" stroke-linecap="round"/>

  <!-- o cento -->
  <?php foreach ($pecas as $i => [$x, $y, $tipo, $giro]): ?>
    <g transform="translate(<?= $x ?> <?= $y ?>) rotate(<?= $giro ?>)"><use class="peca" style="--i:<?= $i ?>" href="#s-<?= $tipo ?>" x="-23" y="-23" width="46" height="46"/></g>
  <?php endforeach; ?>

  <!-- etiqueta de papel presa na bandeja -->
  <g class="etiqueta" transform="translate(410 82) rotate(10) scale(1.15)">
    <path d="M-28 34C-34 18-36 4-34-6" fill="none" stroke="#fff" stroke-width="2" stroke-dasharray="3 3"/>
    <path d="M-30 -24h86a8 8 0 0 1 8 8v40a8 8 0 0 1-8 8h-86l-16-28z" fill="#fce24c" stroke="#2a1710" stroke-width="2"/>
    <circle cx="-26" cy="4" r="4" fill="#e1051e" stroke="#2a1710" stroke-width="1.5"/>
    <text x="16" y="12" text-anchor="middle" font-family="'Alfa Slab One', Georgia, serif" font-size="24" fill="#2a1710">o cento</text>
  </g>
</svg>
