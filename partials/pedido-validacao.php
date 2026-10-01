<?php

declare(strict_types=1);

/**
 * partials/pedido-validacao.php — etapas 2 e 3 do pedido, em carrinho.php.
 *
 * É aqui que o site pergunta o que a padaria precisa saber antes de a
 * conversa no WhatsApp começar:
 *
 *   RF-29  horário de funcionamento da matriz
 *   RF-30  tempo mínimo de preparo
 *   RF-17  retirar na matriz ou receber em casa
 *   —      forma de pagamento (só declarada; NADA é pago pelo site)
 *
 * RF-18 está CORTADO do escopo: não há valor, faixa nem condição de frete em
 * lugar nenhum. Perguntar "entrega?" é RF-17; cobrar por ela não é deste site.
 *
 * Mora só nesta página, e não no painel lateral: são várias decisões, e o
 * painel viraria um formulário rolando dentro de outro.
 *
 * assets/js/cart.js lê estes campos pelos data-pedido-* no checkout(). Se a
 * pessoa fechar o pedido de outra página, os campos não existem e o checkout()
 * cai nos padrões — retirada na matriz e pagamento a combinar.
 * assets/js/ui.js copia a escolha para o resumo (data-pedido-resumo-*).
 */

require_once __DIR__ . '/bootstrap.php';

$matriz   = dd_matriz();
$horario  = trim((string) ($matriz['horario'] ?? ''));
$preparo  = trim((string) ($matriz['preparo'] ?? ''));
$endereco = trim((string) ($matriz['endereco'] ?? ''));

/**
 * Como receber (RF-17). A primeira nasce marcada.
 *
 * 'valor' é o que vai escrito na mensagem do WhatsApp; 'ajuda' é a linha de
 * baixo do cartão, que responde "o que acontece se eu escolher isto?".
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
        'ajuda'  => 'Você informa o endereço e a entrega é combinada na conversa.',
    ],
];

/** Formas de pagamento oferecidas. A primeira nasce marcada. */
$formasDePagamento = [
    [
        'valor'  => 'Pix',
        'rotulo' => 'Pix',
        'ajuda'  => 'A chave vem na conversa.',
    ],
    [
        'valor'  => 'Cartão débito/crédito',
        'rotulo' => 'Cartão',
        'ajuda'  => 'Débito ou crédito, na retirada ou na entrega.',
    ],
    [
        'valor'  => 'A combinar no WhatsApp',
        'rotulo' => 'Prefiro combinar',
        'ajuda'  => 'Você decide junto com a matriz.',
    ],
];
?>
<!-- 2. Como receber -->
<section class="cartao-etapa" aria-labelledby="etapa-receber">
  <h2 id="etapa-receber" class="etapa-titulo"><span class="etapa-numero" aria-hidden="true">2</span>Como você quer receber</h2>

  <?php // RF-29 e RF-30 juntos: são as duas respostas para "quando eu recebo?". ?>
  <?php if ($horario !== '' || $preparo !== ''): ?>
    <dl class="mt-4 grid gap-2 text-sm sm:grid-cols-2">
      <?php if ($horario !== ''): ?>
        <div class="flex gap-2.5">
          <svg class="mt-0.5 h-5 w-5 shrink-0 text-crust" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
          <div>
            <dt class="font-semibold">Horário da matriz</dt>
            <dd class="leading-relaxed text-crust"><?= e($horario) ?></dd>
          </div>
        </div>
      <?php endif; ?>

      <?php if ($preparo !== ''): ?>
        <div class="flex gap-2.5">
          <svg class="mt-0.5 h-5 w-5 shrink-0 text-crust" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 22h14"/><path d="M5 2h14"/><path d="M17 22v-4.2a2 2 0 0 0-.6-1.4L12 12l-4.4 4.4a2 2 0 0 0-.6 1.4V22"/><path d="M7 2v4.2a2 2 0 0 0 .6 1.4L12 12l4.4-4.4a2 2 0 0 0 .6-1.4V2"/></svg>
          <div>
            <dt class="font-semibold">Prazo de preparo</dt>
            <dd class="leading-relaxed text-crust"><?= e($preparo) ?></dd>
          </div>
        </div>
      <?php endif; ?>
    </dl>
  <?php endif; ?>

  <fieldset class="mt-5">
    <legend class="sr-only">Como você quer receber</legend>

    <div class="grid gap-2.5 sm:grid-cols-2">
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
    <label class="oculto mt-4 block" data-pedido-endereco-campo>
      <span class="text-sm font-semibold">Endereço da entrega</span>
      <textarea data-pedido-endereco rows="2" class="campo-texto mt-1.5"
                placeholder="Rua, número, bairro e um ponto de referência"></textarea>
      <span class="mt-1.5 block text-xs leading-relaxed text-crust">
        O site não calcula nem cobra a entrega; ela é combinada na conversa.
      </span>
    </label>
  </fieldset>
</section>

<!-- 3. Forma de pagamento: declaração, não cobrança -->
<section class="cartao-etapa" aria-labelledby="etapa-pagamento">
  <h2 id="etapa-pagamento" class="etapa-titulo"><span class="etapa-numero" aria-hidden="true">3</span>Forma de pagamento</h2>

  <?php // O aviso vem ANTES das opções de propósito: é a primeira coisa que a pessoa lê aqui. ?>
  <p class="mt-2 flex gap-2 text-sm leading-relaxed text-crust">
    <svg class="mt-0.5 h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M12 8v5M12 16.5h.01"/><circle cx="12" cy="12" r="9"/></svg>
    <span>Nada é pago pelo site. Você só avisa como pretende pagar; o acerto é direto com a matriz.</span>
  </p>

  <fieldset class="mt-4">
    <legend class="sr-only">Forma de pagamento</legend>
    <div class="grid gap-2.5 sm:grid-cols-3">
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
</section>
