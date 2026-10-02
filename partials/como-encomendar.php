<?php

declare(strict_types=1);

/**
 * partials/como-encomendar.php — "Como encomendar" da home, com o celular.
 *
 * Os três passos ao lado de um braço segurando o celular; na tela, o terceiro
 * passo acontecendo: a conversa de WhatsApp com a Dolce, com o pedido que o
 * site monta, a loja respondendo e a foto do cento.
 *
 * A tela da foto foi furada (assets/img/recortes/celular-*.webp): a conversa é
 * HTML por baixo da imagem, então o notch e os cantos vêm da própria foto e o
 * texto fica nítido em qualquer tela. Ela é decorativa (aria-hidden): os
 * passos dizem o mesmo em texto.
 *
 * Sem JavaScript, ou para quem pediu menos movimento, a conversa aparece
 * completa. Com movimento, o GSAP (assets/js/encomendas-zap.js) faz o braço
 * entrar e as mensagens chegarem quando a seção aparece na tela.
 *
 * Espera:
 *   $passos  array  cada um ['titulo' => string, 'texto' => string]
 */

require_once __DIR__ . '/bootstrap.php';

$passos      = $passos ?? [];
$celular     = dd_imagem('/assets/img/recortes/celular-1100.webp');
$celularMini = dd_imagem('/assets/img/recortes/celular-640.webp');
$fotoCento   = dd_imagem('/assets/img/recortes/cento-400.webp');
$logo        = dd_imagem('/assets/img/logo.svg');

// Os dois vistos do WhatsApp (cinza: entregue; azul: lido).
$vistos = '<svg class="zap-vistos" viewBox="0 0 18 11" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="m1 6 3.2 3.2L11 2.4"/><path d="m7.2 9.2.2.2L14.4 2.4"/></svg>';
?>
<?php if ($celular): ?>
  <?php
  // Esconde o celular e as mensagens antes do primeiro quadro, para o GSAP
  // começar do zero sem piscar. Se o script não rodar em 3s, tudo aparece.
  ?>
  <script>
    if (matchMedia('(prefers-reduced-motion: no-preference)').matches) {
      document.documentElement.classList.add('zap-anima');
      setTimeout(function () { document.documentElement.classList.remove('zap-anima'); }, 3000);
    }
  </script>
  <?php dd_scripts_gsap(); ?>
  <script type="module" src="/assets/js/encomendas-zap.js"></script>
<?php endif; ?>

<section id="encomendas" class="encomendas" aria-labelledby="titulo-encomendas" data-encomendas>
  <div class="encomendas-conteudo">
    <div class="encomendas-texto">
      <h2 id="titulo-encomendas" class="text-3xl sm:text-4xl">Como encomendar</h2>
      <p class="encomendas-frase">Você escolhe aqui no site e fecha com a matriz no WhatsApp.</p>

      <ol class="encomendas-passos">
        <?php foreach ($passos as $i => $passo): ?>
          <li>
            <span class="encomendas-numero"><?= e((string) ($i + 1)) ?></span>
            <div>
              <h3><?= e($passo['titulo']) ?></h3>
              <p><?= e($passo['texto']) ?></p>
            </div>
          </li>
        <?php endforeach; ?>
      </ol>

      <?php // RF-19 — atendimento a empresas tem página própria; aqui só o convite. ?>
      <p class="encomendas-empresas">
        Evento, escola ou empresa?
        <a href="/sobre#empresas">Veja o atendimento para grandes pedidos</a>
      </p>
    </div>
  </div>

  <?php if ($celular): ?>
    <div class="encomendas-palco" aria-hidden="true">
      <div class="encomendas-celular" data-zap-celular>
        <div class="zap-tela">
          <div class="zap-app">
            <div class="zap-topo">
              <div class="zap-status">
                <span>10:24</span>
                <svg viewBox="0 0 34 12" fill="currentColor"><rect x="0" y="7" width="3" height="5" rx="1"/><rect x="5" y="5" width="3" height="7" rx="1"/><rect x="10" y="2.5" width="3" height="9.5" rx="1"/><rect x="15" y="0" width="3" height="12" rx="1"/><rect x="22" y="1.5" width="10" height="9" rx="2.5" fill="none" stroke="currentColor"/><rect x="23.5" y="3" width="7" height="6" rx="1.2"/></svg>
              </div>
              <div class="zap-contato">
                <svg class="zap-voltar" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m15 5-7 7 7 7"/></svg>
                <span class="zap-avatar"><?php if ($logo): ?><img src="<?= e($logo) ?>" alt="" width="40" height="40" loading="lazy"><?php endif; ?></span>
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
                  <img src="<?= e($fotoCento) ?>" alt="" width="400" height="400" loading="lazy" decoding="async">
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
        <img class="encomendas-foto" src="<?= e($celular) ?>" srcset="<?= e((string) $celularMini) ?> 640w, <?= e($celular) ?> 1094w" sizes="(min-width: 64rem) min(46vw, 40rem), 118vw" alt="" width="1094" height="1519" loading="lazy" decoding="async">
      </div>
    </div>
  <?php endif; ?>
</section>
