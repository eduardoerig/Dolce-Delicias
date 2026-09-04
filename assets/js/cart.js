/**
 * =============================================================================
 * assets/js/cart.js — carrinho da Dolce Delícias
 * =============================================================================
 * JavaScript puro, sem framework. O estado inteiro vive em
 * localStorage['dolce_cart'] e a unidade escolhida em localStorage['dolce_unit'].
 *
 * Não existe back-end nem pagamento: o pedido é fechado abrindo o WhatsApp da
 * unidade com a mensagem já escrita.
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

const CHAVE_CARRINHO = 'dolce_cart';
const CHAVE_UNIDADE = 'dolce_unit';

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
  return item && typeof item.id === 'string' && Number.isFinite(Number(item.preco));
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

/** Unidade escolhida; cai na matriz (ou na primeira) quando não houver escolha. */
export function unidadeAtual() {
  const unidades = lerUnidades();
  if (!unidades.length) return null;

  let slug = null;
  try {
    slug = localStorage.getItem(CHAVE_UNIDADE);
  } catch {
    /* sem localStorage: usa o padrão */
  }

  return (
    unidades.find((u) => u.slug === slug) ||
    unidades.find((u) => u.matriz) ||
    unidades[0]
  );
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

function iconeDaCategoria(categoria) {
  if (iconesPorCategoria === null) {
    iconesPorCategoria = new Map();
    const molde = document.getElementById('icones-produto');
    if (molde) {
      molde.content.querySelectorAll('[data-icone-categoria]').forEach((no) => {
        iconesPorCategoria.set(no.dataset.iconeCategoria, no.innerHTML);
      });
    }
  }
  return iconesPorCategoria.get(categoria) || iconesPorCategoria.get('') || '';
}

/**
 * Miniatura do item. A foto some do alt de propósito: o nome do produto está
 * logo ao lado, e repetir só faria o leitor de tela falar duas vezes.
 */
function miniatura(item) {
  if (item.imagem) {
    return `<img src="${esc(item.imagem)}" alt="" class="h-full w-full object-cover">`;
  }
  return `<div class="massa flex h-full w-full items-center justify-center text-crust">${iconeDaCategoria(item.categoria || '')}</div>`;
}

function linhaItem(item) {
  const nome = esc(item.nome);
  const subtotal = brl.format(Number(item.preco) * Number(item.qtd));
  const link = `produto.php?slug=${encodeURIComponent(item.slug || item.id)}`;

  return `
    <li class="flex gap-3 p-4" data-item="${esc(item.id)}">
      <a href="${link}" tabindex="-1" aria-hidden="true"
         class="miniatura h-14 w-14 shrink-0 overflow-hidden rounded-xl border border-base-300 sm:h-16 sm:w-16">
        ${miniatura(item)}
      </a>

      <div class="flex min-w-0 flex-1 flex-col gap-2.5">
        <div class="flex items-start gap-2">
          <div class="min-w-0 flex-1">
            <p class="font-bold leading-tight">
              <a class="rounded hover:text-brand" href="${link}">${nome}</a>
            </p>
            <p class="mt-0.5 text-xs text-crust">${esc(item.por)}</p>
          </div>
          <button type="button" data-item-remove
                  class="btn btn-ghost btn-xs h-8 w-8 shrink-0 rounded-lg p-0 text-crust hover:text-brand"
                  aria-label="Remover ${nome} do pedido">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
          </button>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-2">
          <div class="flex items-center rounded-xl border border-campo bg-base-200">
            <button type="button" data-item-menos
                    class="btn btn-ghost h-9 w-9 rounded-xl p-0 text-lg font-bold"
                    aria-label="Diminuir a quantidade de ${nome}">−</button>
            <input type="number" data-item-qtd inputmode="numeric"
                   class="h-9 w-14 border-0 bg-transparent text-center text-sm font-bold focus:outline-none"
                   value="${Number(item.qtd)}" min="${Number(item.min) || 1}" step="${Number(item.passo) || 1}"
                   aria-label="Quantidade de ${nome}, em unidades">
            <button type="button" data-item-mais
                    class="btn btn-ghost h-9 w-9 rounded-xl p-0 text-lg font-bold"
                    aria-label="Aumentar a quantidade de ${nome}">+</button>
          </div>
          <span class="fonte-display text-lg">${subtotal}</span>
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

export function checkout() {
  const itens = lerCarrinho();

  if (!itens.length) {
    avisar('Adicione pelo menos um item antes de fechar o pedido.');
    return;
  }

  const unidade = unidadeAtual();
  if (!unidade) {
    avisar('Nenhuma unidade cadastrada. Confira data/units.php.');
    return;
  }

  const numero = String(unidade.whatsapp || '').replace(/\D+/g, '');
  if (!numero) {
    avisar('Esta unidade ainda não tem WhatsApp cadastrado.');
    return;
  }

  // Observação: pega o primeiro campo preenchido (drawer ou página).
  let observacao = '';
  document.querySelectorAll('[data-cart-obs]').forEach((campo) => {
    if (!observacao && campo.value.trim()) observacao = campo.value.trim();
  });

  const linhas = itens.map(
    (i) => `• ${i.qtd}x ${i.nome} (${i.por}) — ${brl.format(Number(i.preco) * Number(i.qtd))}`
  );

  const partes = [
    'Olá! Quero fazer uma encomenda pelo site da Dolce Delícias.',
    '',
    `Unidade: ${unidade.nome}`,
    '',
    ...linhas,
    '',
    `Total estimado: ${brl.format(totalCarrinho(itens))}`,
  ];

  if (observacao) {
    partes.push('', `Observação: ${observacao}`);
  }

  const url = `https://wa.me/${numero}?text=${encodeURIComponent(partes.join('\n'))}`;
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
      removeItem(id);
      document.dispatchEvent(new CustomEvent('dolce:removido'));
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
    limparCarrinho();
    document.dispatchEvent(new CustomEvent('dolce:removido'));
  }
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
