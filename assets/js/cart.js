/**
 * =============================================================================
 * assets/js/cart.js — carrinho da Dolce Delícias
 * =============================================================================
 * JavaScript puro, sem framework. O estado inteiro vive em
 * localStorage['dolce_cart'].
 *
 * Não existe back-end nem pagamento: o pedido é fechado abrindo o WhatsApp da
 * MATRIZ com a mensagem já escrita — pedido pelo site é só com ela.
 *
 * Formato de um item guardado:
 *   {
 *     id:    'pao-de-queijo',        // chave única na lista
 *     slug:  'pao-de-queijo',
 *     nome:  'Pão de Queijo',
 *     preco: 1.2,                    // preço de UMA unidade
 *     por:   'R$ 120,00 / 100 un',   // base do preço, só para exibir
 *     qtd:   150,                    // em unidades
 *     min:   50,
 *     passo: 50,
 *     imagem: '/assets/img/...',     // vazio enquanto a foto nao existe
 *     categoria: 'Assados'           // escolhe o icone do placeholder
 *   }
 */

import { mensagemPedido } from './order.js';

const CHAVE_CARRINHO = 'dolce_cart';

const brl = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });

/* ---------------------------------------------------------------------------
 * Estado
 * ------------------------------------------------------------------------ */

/** @returns {Array<object>} */
export function lerCarrinho() {
  try {
    const cru = localStorage.getItem(CHAVE_CARRINHO);
    const dados = cru ? JSON.parse(cru) : [];
    return Array.isArray(dados) ? dados.filter(itemValido) : [];
  } catch {
    return []; // localStorage bloqueado ou JSON corrompido: começa vazio
  }
}

function itemValido(item) {
  return item && typeof item.id === 'string' && Number.isFinite(Number(item.preco)) && Number(item.preco)>=0 && Number.isSafeInteger(Number(item.qtd)) && Number(item.qtd)>0;
}

function salvarCarrinho(itens) {
  try {
    localStorage.setItem(CHAVE_CARRINHO, JSON.stringify(itens));
  } catch {
    /* modo privado / cota cheia: a sessão continua, só não persiste */
  }
  sincronizar();
  document.dispatchEvent(new CustomEvent('dolce:carrinho', { detail: { itens } }));
}

/** Arredonda a quantidade para o mínimo e o passo do produto. */
function normalizarQtd(qtd, min, passo) {
  const m = Math.max(1, Number(min) || 1);
  const p = Math.max(1, Number(passo) || 1);
  const n = Number(qtd);
  if (!Number.isFinite(n) || n <= m) return m;
  return m + Math.round((n - m) / p) * p;
}

/* ---------------------------------------------------------------------------
 * Operações
 * ------------------------------------------------------------------------ */

export function addToCart(novo) {
  const itens = lerCarrinho();
  const existente = itens.find((i) => i.id === novo.id);

  if (existente) {
    existente.qtd = normalizarQtd(existente.qtd + novo.qtd, novo.min, novo.passo);
  } else {
    itens.push({ ...novo, qtd: normalizarQtd(novo.qtd, novo.min, novo.passo) });
  }

  salvarCarrinho(itens);
  document.dispatchEvent(new CustomEvent('dolce:adicionado', { detail: { item: novo } }));
}

export function updateQty(id, qtd) {
  const itens = lerCarrinho();
  const item = itens.find((i) => i.id === id);
  if (!item) return;
  item.qtd = normalizarQtd(qtd, item.min, item.passo);
  salvarCarrinho(itens);
}

export function removeItem(id) {
  const itens = lerCarrinho().filter((i) => i.id !== id);
  salvarCarrinho(itens);
}

export function limparCarrinho() {
  salvarCarrinho([]);
}

export function totalCarrinho(itens = lerCarrinho()) {
  return itens.reduce((soma, i) => soma + Number(i.preco) * Number(i.qtd), 0);
}

/* ---------------------------------------------------------------------------
 * Unidades
 * ------------------------------------------------------------------------ */

/** Lê o <script type="application/json" id="units-data"> publicado pelo PHP. */
export function lerUnidades() {
  const bloco = document.getElementById('units-data');
  if (!bloco) return [];
  try {
    const dados = JSON.parse(bloco.textContent || '[]');
    return Array.isArray(dados) ? dados : [];
  } catch {
    return [];
  }
}

/**
 * A unidade que recebe o pedido — sempre a matriz.
 *
 * REGRA DE NEGÓCIO: pedido feito pelo site vai só para a matriz. As outras
 * lojas continuam com catálogo próprio em PDF e WhatsApp de contato direto
 * em unidades.php, mas não recebem carrinho.
 *
 * Se um dia cada loja receber pedido, é aqui que a escolha volta a entrar.
 */
export function matriz() {
  const unidades = lerUnidades();
  if (!unidades.length) return null;

  return unidades.find((u) => u.matriz) || null;
}

/* ---------------------------------------------------------------------------
 * Renderização
 * ------------------------------------------------------------------------ */

function esc(texto) {
  return String(texto ?? '').replace(/[&<>"']/g, (c) => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
  }[c]));
}

/**
 * Ícones de placeholder por categoria, lidos do <template id="icones-produto">
 * que o PHP imprime no rodapé. São os mesmos SVGs do card do catálogo — o JS
 * não guarda uma segunda cópia deles.
 */
let iconesPorCategoria = null;
let iconesPorProduto = null;

/** O desenho do próprio produto; se não houver, o da categoria. (ui.js usa no aviso.) */
export function iconeDoItem(item) {
  if (iconesPorCategoria === null) {
    iconesPorCategoria = new Map();
    iconesPorProduto = new Map();
    const molde = document.getElementById('icones-produto');
    if (molde) {
      molde.content.querySelectorAll('[data-icone-categoria]').forEach((no) => {
        iconesPorCategoria.set(no.dataset.iconeCategoria, no.innerHTML);
      });
      molde.content.querySelectorAll('[data-icone-produto]').forEach((no) => {
        iconesPorProduto.set(no.dataset.iconeProduto, no.innerHTML);
      });
    }
  }
  return iconesPorProduto.get(item.slug || item.id || '')
    || iconesPorCategoria.get(item.categoria || '')
    || iconesPorCategoria.get('') || '';
}

/**
 * Miniatura do item. A foto some do alt de propósito: o nome do produto está
 * logo ao lado, e repetir só faria o leitor de tela falar duas vezes.
 */
function miniatura(item) {
  if (item.imagem) {
    return `<img src="${esc(item.imagem)}" alt="">`;
  }
  return `<span class="item-pedido-sem-foto">${iconeDoItem(item)}</span>`;
}

const ICONE_MENOS = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14"/></svg>';
const ICONE_MAIS = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>';
const ICONE_LIXEIRA = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16"/><path d="M10 11v6M14 11v6"/><path d="M6 7l1 13h10l1-13"/><path d="M9 7V4h6v3"/></svg>';

/**
 * Uma linha do carrinho: foto, nome e embalagem, subtotal; embaixo o seletor
 * de quantidade (o mesmo da página do produto) e "Remover".
 *
 * Os controles têm 44px: é aqui que se corrige o pedido no celular.
 */
function linhaItem(item) {
  const nome = esc(item.nome);
  const subtotal = brl.format(Number(item.preco) * Number(item.qtd));
  const link = `/produtos/${encodeURIComponent(item.slug || item.id)}`;

  return `
    <li class="item-pedido" data-item="${esc(item.id)}">
      <a href="${link}" tabindex="-1" aria-hidden="true" class="item-pedido-foto">
        ${miniatura(item)}
      </a>

      <div class="item-pedido-corpo">
        <div class="item-pedido-topo">
          <p class="item-pedido-nome">
            <a href="${link}">${nome}</a>
            <span>${esc(item.por)}</span>
          </p>
          <span class="item-pedido-subtotal">${subtotal}</span>
        </div>

        <div class="item-pedido-acoes">
          <div class="seletor-qtd seletor-qtd-sm">
            <button type="button" data-item-menos aria-label="Diminuir a quantidade de ${nome}"${Number(item.qtd) <= (Number(item.min) || 1) ? ' disabled' : ''}>${ICONE_MENOS}</button>
            <input type="number" data-item-qtd inputmode="numeric"
                   value="${Number(item.qtd)}" min="${Number(item.min) || 1}" step="${Number(item.passo) || 1}"
                   aria-label="Quantidade de ${nome}, em unidades">
            <button type="button" data-item-mais aria-label="Aumentar a quantidade de ${nome}">${ICONE_MAIS}</button>
          </div>
          <button type="button" data-item-remove class="item-pedido-remover" aria-label="Remover ${nome} do pedido">
            ${ICONE_LIXEIRA}<span>Remover</span>
          </button>
        </div>
      </div>
    </li>`;
}

/** Desenha a lista de itens no drawer E na página do carrinho (mesmos data-*). */
export function renderDrawer() {
  const itens = lerCarrinho();
  const vazio = itens.length === 0;
  const html = itens.map(linhaItem).join('');

  // `.oculto` usa display:none !important — não briga com flex/block do card.
  document.querySelectorAll('[data-cart-items]').forEach((lista) => {
    lista.innerHTML = html;
    lista.classList.toggle('oculto', vazio);
  });

  document.querySelectorAll('[data-cart-empty]').forEach((bloco) => {
    bloco.classList.toggle('oculto', !vazio);
  });

  document.querySelectorAll('[data-cart-footer]').forEach((rodape) => {
    rodape.classList.toggle('oculto', vazio);
  });

  const total = brl.format(totalCarrinho(itens));
  document.querySelectorAll('[data-cart-total]').forEach((el) => {
    el.textContent = total;
  });
}

export function updateBadge() {
  const itens = lerCarrinho();
  const quantos = itens.length;
  const rotulo = quantos === 1 ? '1 item' : `${quantos} itens`;

  document.querySelectorAll('[data-cart-badge]').forEach((badge) => {
    badge.textContent = String(quantos);
    badge.classList.toggle('oculto', quantos === 0);
  });

  document.querySelectorAll('[data-cart-count]').forEach((el) => {
    el.textContent = rotulo;
  });

  document.querySelectorAll('[data-cart-sr]').forEach((el) => {
    el.textContent = quantos === 0 ? 'Carrinho vazio' : `Carrinho com ${rotulo}`;
  });
}

function sincronizar() {
  renderDrawer();
  updateBadge();
}

/* ---------------------------------------------------------------------------
 * Fechamento no WhatsApp
 * ------------------------------------------------------------------------ */

/**
 * Lê a confirmação do pedido — endereço da entrega e pagamento. Não há
 * retirada: todo pedido é entregue.
 *
 * Os campos vivem só em carrinho.php (partials/pedido-validacao.php). Quando o
 * checkout é chamado de outra página, eles não existem e valem os padrões: a
 * opção mais conservadora de cada pergunta, nunca uma promessa que a padaria
 * não fez.
 *
 * @returns {{endereco: string, pagamento: string, temCampoEndereco: boolean}}
 */
function lerConfirmacao() {
  const marcado = (seletor, padrao) =>
    document.querySelector(`${seletor}:checked`)?.value?.trim() || padrao;
  const campoEndereco = document.querySelector('[data-pedido-endereco]');

  return {
    endereco: campoEndereco?.value.trim() || '',
    pagamento: marcado('[data-pedido-pagamento]', 'A combinar no WhatsApp'),
    temCampoEndereco: Boolean(campoEndereco),
  };
}

export function checkout() {
  const itens = lerCarrinho();

  if (!itens.length) {
    avisar('Adicione pelo menos um item antes de fechar o pedido.');
    return;
  }

  const unidade = matriz();
  if (!unidade) {
    avisar('A matriz está indisponível no momento. Tente novamente mais tarde.');
    return;
  }

  const numero = String(unidade.whatsapp || '').replace(/\D+/g, '');
  // Mesma regra de dd_whatsapp() em partials/bootstrap.php: número de exemplo
  // (ex.: 55000000000) conta como não cadastrado.
  if (!/^\d{10,15}$/.test(numero) || /^\d{0,3}0{8,}$/.test(numero)) {
    avisar('A matriz ainda não tem WhatsApp cadastrado.');
    return;
  }

  // Observação: pega o primeiro campo preenchido (drawer ou página).
  let observacao = '';
  document.querySelectorAll('[data-cart-obs]').forEach((campo) => {
    if (!observacao && campo.value.trim()) observacao = campo.value.trim();
  });

  const confirmacao = lerConfirmacao();

  // Na página do pedido o endereço é obrigatório; fechando pela gaveta, ele é
  // combinado na conversa.
  if (confirmacao.temCampoEndereco && !confirmacao.endereco) {
    avisar('Informe o endereço da entrega.');
    document.querySelector('[data-pedido-endereco]')?.focus();
    return;
  }
  const url = `https://wa.me/${numero}?text=${encodeURIComponent(mensagemPedido(itens, unidade, confirmacao, observacao))}`;
  window.open(url, '_blank', 'noopener');
}

/** Aviso curto; o desenho fica por conta de ui.js. */
function avisar(mensagem) {
  document.dispatchEvent(new CustomEvent('dolce:aviso', { detail: { mensagem } }));
}

/* ---------------------------------------------------------------------------
 * Delegação de eventos — um listener no documento cobre tudo, inclusive o que
 * é desenhado depois (as linhas do carrinho).
 * ------------------------------------------------------------------------ */

document.addEventListener('click', (evento) => {
  const alvo = evento.target;
  if (!(alvo instanceof Element)) return;

  /* Adicionar ao carrinho -------------------------------------------------- */
  const botaoAdd = alvo.closest('[data-add]');
  if (botaoAdd) {
    const d = botaoAdd.dataset;
    const min = Number(d.min) || 1;
    const passo = Number(d.passo) || 1;

    // Na página do produto o botão usa o seletor de quantidade; no card, o mínimo.
    let qtd = min;
    if (d.usaQty !== undefined) {
      const campo = document.querySelector('[data-qty-input]');
      if (campo) qtd = Number(campo.value) || min;
    }

    addToCart({
      id: d.id,
      slug: d.slug || d.id,
      nome: d.nome,
      preco: Number(d.preco) || 0,
      por: d.por || '',
      qtd,
      min,
      passo,
      imagem: d.imagem || '',
      categoria: d.categoria || '',
    });
    return;
  }

  /* Linha do carrinho ------------------------------------------------------ */
  const linha = alvo.closest('[data-item]');
  if (linha) {
    const id = linha.dataset.item;
    const campo = linha.querySelector('[data-item-qtd]');
    const passo = campo ? Number(campo.step) || 1 : 1;
    const atual = campo ? Number(campo.value) || 0 : 0;

    if (alvo.closest('[data-item-remove]')) {
      const antes = lerCarrinho();
      const nome = antes.find((i) => i.id === id)?.nome || 'Item';
      removeItem(id);
      removido(antes, `${nome} saiu do pedido.`);
      return;
    }
    if (alvo.closest('[data-item-mais]')) {
      updateQty(id, atual + passo);
      return;
    }
    if (alvo.closest('[data-item-menos]')) {
      updateQty(id, atual - passo);
      return;
    }
  }

  /* Rodapé do carrinho ----------------------------------------------------- */
  if (alvo.closest('[data-cart-checkout]')) {
    checkout();
    return;
  }
  if (alvo.closest('[data-cart-clear]')) {
    // Esvazia na hora e oferece "Desfazer" no aviso, em vez de perguntar antes:
    // o engano custa um toque para voltar, e o acerto não custa uma confirmação.
    const antes = lerCarrinho();
    limparCarrinho();
    removido(antes, 'Pedido esvaziado.');
  }
});

/** Avisa a remoção levando o carrinho de antes, para o aviso poder desfazer. */
function removido(antes, mensagem) {
  document.dispatchEvent(new CustomEvent('dolce:removido', { detail: { antes, mensagem } }));
}

/* "Desfazer" no aviso: o carrinho volta a ser o que era antes da remoção. */
document.addEventListener('dolce:restaurar', (evento) => {
  const itens = evento.detail?.itens;
  if (Array.isArray(itens)) salvarCarrinho(itens.filter(itemValido));
});

document.addEventListener('change', (evento) => {
  const campo = evento.target;
  if (!(campo instanceof Element) || !campo.matches('[data-item-qtd]')) return;
  const linha = campo.closest('[data-item]');
  if (linha) updateQty(linha.dataset.item, campo.value);
});

/* Outra aba mexeu no carrinho? Reflete aqui. */
window.addEventListener('storage', (evento) => {
  if (evento.key === CHAVE_CARRINHO) sincronizar();
});

sincronizar();
