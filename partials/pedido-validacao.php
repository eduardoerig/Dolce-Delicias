<?php

declare(strict_types=1);

/**
 * partials/pedido-validacao.php — a confirmação do pedido, em carrinho.php.
 *
 * É aqui que o site pergunta as quatro coisas que a padaria precisa saber antes
 * de a conversa no WhatsApp começar:
 *
 *   RF-29  horário de funcionamento da matriz
 *   RF-30  tempo mínimo de preparo
 *   RF-17  retirar na matriz ou receber em casa
 *   —      forma de pagamento (só declarada; NADA é pago pelo site)
 *
 * RF-18 está CORTADO do escopo: não há valor, faixa nem condição de frete em
 * lugar nenhum. Perguntar "entrega?" é RF-17; cobrar por ela não é deste site.
 *
 * Mora só nesta página, e não no drawer: são quatro decisões, e o painel de
 * 24rem viraria um formulário rolando dentro de outro. O drawer é o resumo
 * rápido e manda para cá (ver partials/cart-drawer.php).
 *
 * assets/js/cart.js lê estes campos pelos data-pedido-* no checkout(). Se a
 * pessoa fechar o pedido pelo drawer de outra página, os campos não existem e o
 * checkout() cai nos padrões — retirada na matriz e pagamento a combinar.
 */

require_once __DIR__ . '/bootstrap.php';

$matriz  = dd_matriz();
$horario = trim((string) ($matriz['horario'] ?? ''));
$preparo = trim((string) ($matriz['preparo'] ?? ''));
$endereco = trim((string) ($matriz['endereco'] ?? ''));

/**
 * Como receber (RF-17). A primeira nasce marcada.
 *
 * 'valor' é o que vai escrito na mensagem do WhatsApp; 'ajuda' é a linha de
 * baixo do cartão, que responde "o que acontece se eu escolher isto?" antes de
 * a pessoa precisar escolher para descobrir.
 */
$formasDeReceber = [
    [
        'valor'  => 'Retirar na matriz',
        'rotulo' => 'Retirar na matriz',
        'ajuda'  => $endereco !== '' ? $endereco : 'Endereço da matriz ainda não cadastrado.',
    ],
    [
        'valor'  => 'Entrega',
        'rotulo' => 'Entrega',
        'ajuda'  => 'Você informa o endereço aqui embaixo e o resto é combinado na conversa.',
    ],
];

/** Formas de pagamento oferecidas. A primeira nasce marcada. */
$formasDePagamento = [
    [
        'valor'  => 'Pix',
        'rotulo' => 'Pix',
        'ajuda'  => 'A chave vem na conversa com a matriz.',
    ],
    [
        'valor'  => 'Cartão débito/crédito',
        'rotulo' => 'Cartão de débito ou crédito',
        'ajuda'  => 'Na hora da retirada ou da entrega.',
    ],
    [
        'valor'  => 'A combinar no WhatsApp',
        'rotulo' => 'Prefiro combinar',
        'ajuda'  => 'Você decide junto com a matriz.',
    ],
];
?>
<section class="mt-6 rounded-bandeja border border-base-300 bg-papel p-5 shadow-bandeja sm:p-6"
         aria-labelledby="titulo-confirmacao">

  <h2 id="titulo-confirmacao" class="text-xl">Confirme seu pedido</h2>

  <?php // RF-29 e RF-30 juntos: são as duas respostas para "quando eu recebo?". ?>
  <?php if ($horario !== '' || $preparo !== ''): ?>
    <dl class="mt-4 space-y-3 rounded-2xl bg-base-200 p-4 text-sm">
      <?php if ($horario !== ''): ?>
        <div class="flex gap-2.5">
          <svg class="mt-0.5 h-5 w-5 shrink-0 text-brand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
          <div>
            <dt class="font-semibold text-base-content">Horário de funcionamento</dt>
            <dd class="mt-0.5 leading-relaxed text-crust"><?= e($horario) ?></dd>
          </div>
        </div>
      <?php endif; ?>

      <?php if ($preparo !== ''): ?>
        <div class="flex gap-2.5">
          <svg class="mt-0.5 h-5 w-5 shrink-0 text-brand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v4"/><path d="M8 3h8"/><path d="M6 21h12"/><path d="M6 21a6 6 0 0 1 12 0"/><path d="M12 7a7 7 0 0 0-7 7"/></svg>
          <div>
            <dt class="font-semibold text-base-content">Tempo mínimo de preparo</dt>
            <dd class="mt-0.5 leading-relaxed text-crust"><?= e($preparo) ?></dd>
          </div>
        </div>
      <?php endif; ?>
    </dl>
  <?php endif; ?>

  <?php
  /**
   * RF-17 — retirada ou entrega. Sem frete: RF-18 está fora do site.
   *
   * Um cartão por opção, empilhados e em largura inteira. Já foram dois chips
   * lado a lado, do mesmo desenho dos filtros do catálogo: não dava para saber
   * o que estava selecionado, porque a única pista era a cor de fundo — a mesma
   * pista que, na grade, quer dizer "filtro ligado". Agora o rádio aparece.
   */
  ?>
  <fieldset class="mt-7">
    <legend class="text-base font-bold text-base-content">Como você quer receber</legend>

    <div class="mt-3 grid gap-2.5">
      <?php foreach ($formasDeReceber as $i => $forma): ?>
        <label class="opcao">
          <input type="radio" name="entrega" class="radio radio-primary mt-0.5 shrink-0" data-pedido-entrega
                 value="<?= e($forma['valor']) ?>" <?= $i === 0 ? 'checked' : '' ?>>
          <span class="min-w-0">
            <span class="block font-bold leading-tight"><?= e($forma['rotulo']) ?></span>
            <span class="mt-1 block text-xs leading-relaxed text-crust"><?= e($forma['ajuda']) ?></span>
          </span>
        </label>
      <?php endforeach; ?>
    </div>

    <?php // Aparece só quando a escolha é Entrega — quem liga/desliga é ui.js. ?>
    <label class="oculto mt-3 block" data-pedido-endereco-campo>
      <span class="text-sm font-semibold text-base-content">Endereço da entrega</span>
      <textarea data-pedido-endereco rows="2"
                class="textarea mt-1.5 w-full resize-none rounded-xl border-campo bg-base-200 text-sm"
                placeholder="Rua, número, bairro e um ponto de referência"></textarea>
      <span class="mt-1.5 block text-xs leading-relaxed text-crust">
        A entrega é combinada na conversa — o site não calcula nem cobra nada por ela.
      </span>
    </label>
  </fieldset>

  <?php // Forma de pagamento: declaração, não cobrança. O aviso vem ANTES das
     // opções de propósito — é a primeira coisa que a pessoa lê aqui. ?>
  <fieldset class="mt-7">
    <legend class="text-base font-bold text-base-content">Forma de pagamento</legend>

    <p class="mt-2 flex gap-2 rounded-2xl bg-accent/25 p-3 text-xs font-semibold leading-relaxed text-base-content">
      <svg class="mt-px h-4 w-4 shrink-0 text-brand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M12 8v5M12 16.5h.01"/><circle cx="12" cy="12" r="9"/></svg>
      <span>Nada é pago pelo site. Você só avisa como pretende pagar — o acerto é direto com a matriz.</span>
    </p>

    <div class="mt-3 grid gap-2.5">
      <?php foreach ($formasDePagamento as $i => $forma): ?>
        <label class="opcao">
          <input type="radio" name="pagamento" class="radio radio-primary mt-0.5 shrink-0" data-pedido-pagamento
                 value="<?= e($forma['valor']) ?>" <?= $i === 0 ? 'checked' : '' ?>>
          <span class="min-w-0">
            <span class="block font-bold leading-tight"><?= e($forma['rotulo']) ?></span>
            <span class="mt-1 block text-xs leading-relaxed text-crust"><?= e($forma['ajuda']) ?></span>
          </span>
        </label>
      <?php endforeach; ?>
    </div>
  </fieldset>

  <?php
  /**
   * O fecho: o que a pessoa acabou de escolher, escrito por extenso.
   *
   * A escolha fica lá em cima e o botão do WhatsApp lá embaixo, na outra coluna
   * — no celular são duas telas de distância. Esta linha é a última coisa antes
   * de sair do bloco, e existe para ninguém mandar o pedido sem saber o que vai
   * junto. Quem escreve é assets/js/ui.js.
   */
  ?>
  <p class="mt-7 flex flex-wrap items-baseline gap-x-2 gap-y-1 border-t border-base-300 pt-4 text-sm">
    <span class="font-semibold text-crust">Você escolheu:</span>
    <span class="font-bold text-base-content" data-pedido-resumo>Retirar na matriz · Pix</span>
  </p>
</section>
