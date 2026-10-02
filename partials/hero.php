<?php

declare(strict_types=1);

/**
 * partials/hero.php — o herói da home: "o pedido fecha no WhatsApp".
 *
 * Um braço entra pela esquerda segurando o celular; na tela, uma conversa de
 * WhatsApp com a Dolce: o pedido que o site monta, a loja respondendo e a foto
 * do cento. É o caminho do site contado numa imagem: escolhe aqui, fecha lá.
 *
 * A tela da foto foi furada (assets/img/recortes/celular-*.webp): a conversa é
 * HTML por baixo da imagem, então o notch e os cantos vêm da própria foto e o
 * texto fica nítido em qualquer tela. Ela é decorativa (aria-hidden): quem lê
 * a página recebe o título e a frase, que dizem o mesmo.
 *
 * Do computador em diante (64rem) o celular fica à esquerda, cortado pela
 * borda, e o texto à direita, alinhado à esquerda na altura da tela. No celular
 * e no tablet em pé, o texto vem em cima e o celular embaixo, cortado pelo pé
 * do herói.
 *
 * A posição final é CSS puro: sem JavaScript, ou para quem pediu menos
 * movimento, a conversa já aparece inteira. O movimento é do GSAP
 * (assets/js/heroi-zap.js): o braço entra, as mensagens chegam uma a uma.
 *
 * Espera:
 *   $heroLinhas      array        linhas do h1, cada uma ['texto' => string]
 *   $heroTexto       string       frase abaixo do título
 *   $heroCta         array|null   ['href','texto'] botão principal
 *   $heroLink        array|null   ['href','texto'] link discreto ao lado do botão
 *   $heroGarantias   string[]     frases curtas sob o botão
 */

require_once __DIR__ . '/bootstrap.php';

$heroLinhas    = $heroLinhas    ?? [];
$heroTexto     = $heroTexto     ?? '';
$heroCta       = $heroCta       ?? null;
$heroLink      = $heroLink      ?? null;
$heroGarantias = $heroGarantias ?? [];

$celular       = dd_imagem('/assets/img/recortes/celular-1100.webp');
$celularMini   = dd_imagem('/assets/img/recortes/celular-640.webp');
$fotoCento     = dd_imagem('/assets/img/recortes/cento-400.webp');
$logo          = dd_imagem('/assets/img/logo.svg');
$gsap          = dd_imagem('/assets/js/vendor/gsap.min.js');
$scrollTrigger = dd_imagem('/assets/js/vendor/ScrollTrigger.min.js');

$iconeGarantia = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg>';
// Os dois vistos do WhatsApp (cinza: entregue; azul: lido).
$vistos = '<svg class="zap-vistos" viewBox="0 0 18 11" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="m1 6 3.2 3.2L11 2.4"/><path d="m7.2 9.2.2.2L14.4 2.4"/></svg>';
?>
<?php if ($celular && $gsap && $scrollTrigger): ?>
  <?php
  // Esconde o celular, as mensagens e o texto antes do primeiro quadro, para o
  // GSAP começar do zero sem piscar. Se o script não rodar em 3s, tudo aparece.
  ?>
  <script>
    if (matchMedia('(prefers-reduced-motion: no-preference)').matches) {
      document.documentElement.classList.add('zap-anima');
      setTimeout(function () { document.documentElement.classList.remove('zap-anima'); }, 3000);
    }
  </script>
  <script src="<?= e($gsap) ?>" defer></script>
  <script src="<?= e($scrollTrigger) ?>" defer></script>
  <script type="module" src="/assets/js/heroi-zap.js"></script>
<?php endif; ?>

<section class="heroi-zap" aria-labelledby="heroi-titulo" data-heroi-zap>
  <div class="heroi-zap-conteudo">
    <div class="heroi-zap-texto">
      <?php // leading abaixo de 1 é o que dá cara de cartaz: as linhas se tocam. ?>
      <h1 id="heroi-titulo" class="heroi-zap-titulo">
        <?php foreach ($heroLinhas as $linha): ?>
          <span class="block" data-zap-revela><?= e($linha['texto']) ?></span>
        <?php endforeach; ?>
      </h1>

      <p class="heroi-zap-frase" data-zap-revela><?= e($heroTexto) ?></p>

      <div class="mt-6 flex flex-wrap items-center gap-x-6 gap-y-3" data-zap-revela>
        <?php if ($heroCta !== null): ?>
          <a href="<?= e($heroCta['href']) ?>" class="botao-primario heroi-cta w-full sm:w-auto"><?= e($heroCta['texto']) ?></a>
        <?php endif; ?>
        <?php if ($heroLink !== null): ?>
          <a href="<?= e($heroLink['href']) ?>" class="hidden min-h-11 items-center font-semibold underline decoration-white/60 decoration-2 underline-offset-4 hover:decoration-white sm:inline-flex"><?= e($heroLink['texto']) ?></a>
        <?php endif; ?>
      </div>

      <?php if ($heroGarantias !== []): ?>
        <ul class="heroi-garantias" data-zap-revela>
          <?php foreach ($heroGarantias as $garantia): ?>
            <li><?= $iconeGarantia ?><?= e($garantia) ?></li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </div>

  <?php if ($celular): ?>
    <div class="heroi-zap-palco" aria-hidden="true">
      <div class="heroi-zap-celular" data-zap-celular>
        <div class="zap-tela">
          <div class="zap-app">
          <div class="zap-topo">
            <div class="zap-status">
              <span>10:24</span>
              <svg viewBox="0 0 34 12" fill="currentColor"><rect x="0" y="7" width="3" height="5" rx="1"/><rect x="5" y="5" width="3" height="7" rx="1"/><rect x="10" y="2.5" width="3" height="9.5" rx="1"/><rect x="15" y="0" width="3" height="12" rx="1"/><rect x="22" y="1.5" width="10" height="9" rx="2.5" fill="none" stroke="currentColor"/><rect x="23.5" y="3" width="7" height="6" rx="1.2"/></svg>
            </div>
            <div class="zap-contato">
              <svg class="zap-voltar" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m15 5-7 7 7 7"/></svg>
              <span class="zap-avatar"><?php if ($logo): ?><img src="<?= e($logo) ?>" alt="" width="40" height="40"><?php endif; ?></span>
              <span class="zap-nome"><strong>Dolce Delícias</strong><small>online</small></span>
            </div>
          </div>

          <div class="zap-conversa">
            <span class="zap-dia">Hoje</span>
            <p class="zap-msg zap-eu zap-lido" data-zap-msg>
              Olá! Quero fazer uma encomenda pelo site da Dolce Delícias.
              <span class="zap-itens">• 1 cento de Coxinha<br>• 1 cento de Mini Esfirras</span>
              <span class="zap-hora">10:24 <?= $vistos ?></span>
            </p>
            <p class="zap-msg zap-loja zap-digitando" data-zap-digitando><i></i><i></i><i></i></p>
            <p class="zap-msg zap-loja" data-zap-msg>
              Oi! Recebemos seu pedido e já separamos tudo para a sua festa.
              <span class="zap-hora">10:25</span>
            </p>
            <?php if ($fotoCento): ?>
              <p class="zap-msg zap-loja zap-foto" data-zap-msg>
                <img src="<?= e($fotoCento) ?>" alt="" width="400" height="400" decoding="async">
                Seu cento vai sair assim.
                <span class="zap-hora">10:25</span>
              </p>
            <?php endif; ?>
          </div>

          <div class="zap-barra">
            <span class="zap-campo">Mensagem</span>
            <span class="zap-mic"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 15a3.5 3.5 0 0 0 3.5-3.5v-6a3.5 3.5 0 1 0-7 0v6A3.5 3.5 0 0 0 12 15Zm6-3.5a1 1 0 1 0-2 0 4 4 0 0 1-8 0 1 1 0 1 0-2 0 6 6 0 0 0 5 5.9V20H9a1 1 0 1 0 0 2h6a1 1 0 1 0 0-2h-2v-2.6a6 6 0 0 0 5-5.9Z"/></svg></span>
          </div>
          </div>
        </div>
        <img class="heroi-zap-foto" src="<?= e($celular) ?>" srcset="<?= e((string) $celularMini) ?> 640w, <?= e($celular) ?> 1094w" sizes="(min-width: 64rem) min(50vw, 44rem), 118vw" alt="" width="1094" height="1519" fetchpriority="high" decoding="async">
      </div>
    </div>
  <?php endif; ?>

  <?php // No celular a pessoa arrasta; no computador ela rola. O link leva ao catálogo. ?>
  <a href="#catalogo" class="heroi-rolar" data-zap-revela>
    <span class="sm:hidden">Arraste para baixo</span>
    <span class="hidden sm:inline">Role para baixo</span>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 4v15"/><path d="m6 13 6 6 6-6"/></svg>
  </a>
</section>
