<?php

declare(strict_types=1);

/**
 * partials/ilustra-fachada.php — a frente de uma loja da Dolce, desenhada.
 *
 * Mesmo traço da bandeja do herói: toldo listrado, placa oval vermelha da
 * marca, vitrine com uma bandeja de salgados e a porta. Aparece no lugar da
 * foto da fachada enquanto ela não é cadastrada, e na faixa de unidades.php e
 * sobre.php.
 *
 * Decorativa (aria-hidden): quem chama dá o texto alternativo, se precisar.
 *
 * Espera (opcional):
 *   $fachadaClasse  string  classes do <svg>
 */

$fachadaClasse = $fachadaClasse ?? '';

// Toldo: listras alternadas de vermelho e branco, com a barra recortada embaixo.
$listras = '';
for ($i = 0, $x = 44; $x < 276; $i++, $x += 29) {
    $cor = $i % 2 === 0 ? '#e1051e' : '#ffffff';
    $listras .= '<path d="M' . $x . ' 70h29v22a14.5 14.5 0 0 1-29 0Z" fill="' . $cor . '"/>';
}
?>
<svg class="<?= e($fachadaClasse) ?>" viewBox="0 0 320 200" aria-hidden="true" focusable="false">
  <!-- chão -->
  <ellipse cx="160" cy="190" rx="150" ry="9" fill="#2a1710" opacity=".12"/>

  <!-- prédio -->
  <rect x="58" y="34" width="204" height="156" rx="4" fill="#fbf4ee" stroke="#c5ac98" stroke-width="2"/>
  <rect x="50" y="26" width="220" height="14" rx="3" fill="#7a2e12"/>

  <!-- placa oval da marca, com os raios -->
  <g transform="translate(160 50)">
    <ellipse rx="50" ry="17" fill="#e1051e" stroke="#ffffff" stroke-width="3"/>
    <path d="M0 0-46-6v12ZM0 0 46-6v12Z" fill="#ffffff" opacity=".18"/>
    <text y="-1" text-anchor="middle" font-family="'Baloo 2', 'Trebuchet MS', sans-serif" font-weight="800" font-style="italic" font-size="14" fill="#ffffff">Dolce</text>
    <text y="11" text-anchor="middle" font-family="'Baloo 2', 'Trebuchet MS', sans-serif" font-weight="800" font-size="10" fill="#fce24c">delícias</text>
  </g>

  <!-- toldo -->
  <rect x="42" y="64" width="236" height="8" rx="4" fill="#9e0315"/>
  <?= $listras ?>
  <path d="M44 70h232" stroke="#9e0315" stroke-width="1.5"/>

  <!-- vitrine com a bandeja -->
  <rect x="74" y="108" width="88" height="62" rx="4" fill="#f7e8db" stroke="#7a2e12" stroke-width="2.5"/>
  <path d="M80 114l20 0M80 120l12 0" stroke="#ffffff" stroke-width="3" stroke-linecap="round" opacity=".8"/>
  <rect x="74" y="150" width="88" height="6" fill="#c5ac98"/>
  <ellipse cx="118" cy="148" rx="30" ry="7" fill="#fbf4ee" stroke="#c5ac98" stroke-width="1.5"/>
  <g stroke="#a9561a" stroke-width="1">
    <circle cx="104" cy="143" r="5" fill="#e7a446"/><circle cx="114" cy="142" r="5" fill="#f2d48a"/>
    <path d="M124 135c3 3 5 6 5 8a5 5 0 0 1-10 0c0-2 2-5 5-8Z" fill="#e39a3b"/>
    <circle cx="134" cy="143" r="5" fill="#e7a446"/>
  </g>

  <!-- porta -->
  <rect x="182" y="104" width="54" height="86" rx="4" fill="#7a2e12"/>
  <rect x="190" y="112" width="38" height="40" rx="3" fill="#f7e8db" opacity=".9"/>
  <circle cx="226" cy="150" r="3" fill="#fce24c"/>
  <rect x="176" y="186" width="66" height="4" rx="2" fill="#c5ac98"/>

  <!-- vasinho -->
  <path d="M248 172h16l-3 18h-10Z" fill="#c2412c"/>
  <circle cx="252" cy="166" r="6" fill="#5f9e4a"/><circle cx="260" cy="164" r="6" fill="#6aa84f"/><circle cx="256" cy="158" r="5" fill="#5f9e4a"/>
</svg>
