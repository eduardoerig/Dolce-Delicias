/**
 * =============================================================================
 * assets/js/ui.js — comportamento da interface
 * =============================================================================
 * JavaScript puro, sem framework. Cuida de:
 *   1. nome da matriz nos rótulos do carrinho
 *   2. dropdowns <details> (fechar ao clicar fora / Esc)
 *   3. drawer do carrinho, com foco e teclado
 *   4. catálogo: busca, filtros, ordem, páginas e vistos recentemente
 *   5. revelação dos cards ao rolar (IntersectionObserver)
 *   6. avisos curtos (toasts)
 *   7. quantidade na página do produto
 *   8. confirmação do pedido (carrinho.php)
 *
 * O estado do carrinho em si mora em assets/js/cart.js.
 */

import { matriz } from './cart.js';

/* ===========================================================================
 * 1. NOME DA MATRIZ
 * O pedido pelo site vai só para a matriz (ver matriz() em cart.js), então
 * aqui não há escolha: é só escrever o nome dela onde o carrinho mostra
 * para quem o pedido vai. O nome vive em data/units.php, não no HTML.
 * ======================================================================== */

(function escreverMatriz() {
  const loja = matriz();
  if (!loja) return;

  document.querySelectorAll('[data-unit-label]').forEach((el) => {
    el.textContent = loja.nome;
  });
})();

/* ===========================================================================
 * 2. DROPDOWNS <details>
 * O <details> já abre, fecha e navega pelo teclado sozinho. Só falta fechar
 * quando o clique sai dele ou quando a pessoa aperta Esc.
 * ======================================================================== */

function fecharDropdowns(exceto) {
  document.querySelectorAll('details.dropdown[open]').forEach((d) => {
    if (d !== exceto) d.open = false;
  });
}

document.addEventListener('click', (evento) => {
  const dentro = evento.target instanceof Element ? evento.target.closest('details.dropdown') : null;
  fecharDropdowns(dentro);
});

document.addEventListener('keydown', (evento) => {
  if (evento.key !== 'Escape') return;
  const aberto = document.querySelector('details.dropdown[open]');
  if (aberto) {
    aberto.open = false;
    aberto.querySelector('summary')?.focus();
  }
});

/* ===========================================================================
 * 3. DRAWER DO CARRINHO
 * `inert` tira o painel fechado do foco e do leitor de tela.
 * ======================================================================== */

const alavancaDrawer = document.getElementById('carrinho-toggle');
const painelCarrinho = document.getElementById('painel-carrinho');
let quemAbriuODrawer = null;

function aplicarEstadoDrawer(aberto) {
  if (!painelCarrinho) return;

  painelCarrinho.inert = !aberto;
  document.querySelectorAll('[data-cart-open]').forEach((botao) => {
    botao.setAttribute('aria-expanded', String(aberto));
  });

  if (aberto) {
    // Tirar `inert` só libera o foco no próximo ciclo de tarefas — nem forçar
    // reflow adianta. setTimeout roda depois disso e, ao contrário de
    // requestAnimationFrame, também dispara com a aba em segundo plano.
    setTimeout(() => painelCarrinho.querySelector('[data-cart-close]')?.focus(), 0);
  } else if (quemAbriuODrawer) {
    quemAbriuODrawer.focus();
    quemAbriuODrawer = null;
  }
}

function abrirDrawer(origem) {
  if (!alavancaDrawer) return;
  quemAbriuODrawer = origem || null;
  alavancaDrawer.checked = true;
  aplicarEstadoDrawer(true);
}

function fecharDrawer() {
  if (!alavancaDrawer) return;
  alavancaDrawer.checked = false;
  aplicarEstadoDrawer(false);
}

if (alavancaDrawer && painelCarrinho) {
  alavancaDrawer.addEventListener('change', () => aplicarEstadoDrawer(alavancaDrawer.checked));

  document.addEventListener('click', (evento) => {
    const alvo = evento.target;
    if (!(alvo instanceof Element)) return;

    const abridor = alvo.closest('[data-cart-open]');
    if (abridor) {
      evento.preventDefault();
      abrirDrawer(abridor);
      return;
    }
    if (alvo.closest('[data-cart-close]') || alvo.closest('.drawer-overlay')) {
      fecharDrawer();
    }
  });

  document.addEventListener('keydown', (evento) => {
    if (evento.key === 'Escape' && alavancaDrawer.checked) fecharDrawer();
  });

  // O painel nasce fechado.
  aplicarEstadoDrawer(false);
}

/* ===========================================================================
 * 4. CATÁLOGO: BUSCA, FILTROS, ORDEM E PÁGINAS
 * Tudo no próprio DOM: o PHP já imprimiu todos os cards, e os data-* de cada
 * <article> (partials/product-card.php) dizem categoria, linha, etiquetas,
 * preço por peça e destaque. Sem JavaScript, a grade inteira aparece.
 * ======================================================================== */

const movimentoSuave = () =>
  window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth';

const grade = document.querySelector('[data-grade-produtos]');

if (grade) {
  const POR_PAGINA = 12;
  const cards = Array.from(grade.querySelectorAll('[data-produto]'));
  // A ordem do PHP vira o desempate de "Destaques" (o PHP já manda na ordem do cadastro).
  cards.forEach((card, i) => { card.dataset.ordem = String(i); });

  const campoBusca = document.querySelector('[data-busca-input]');
  const semResultado = document.querySelector('[data-sem-resultado]');
  const contagens = document.querySelectorAll('[data-contagem]');
  const paginacao = document.querySelector('[data-paginacao]');
  const ordenar = document.querySelector('[data-ordenar]');
  const todas = document.querySelector('[data-filter-category-todas]');
  const categorias = Array.from(document.querySelectorAll('[data-filter-category]'));
  const preco = document.querySelector('[data-filter-preco]');
  const precoSaida = document.querySelector('[data-filter-preco-saida]');
  const brl = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL', maximumFractionDigits: 0 });

  let termo = '';
  let pagina = 1;

  /** minúsculas e sem acento, igual ao índice gerado pelo PHP */
  const marcasDeAcento = new RegExp('[\\u0300-\\u036f]', 'g');
  const normalizar = (texto) => texto.toLowerCase().normalize('NFD').replace(marcasDeAcento, '');
  const plural = (n) => (n === 1 ? 'item' : 'itens');

  function lerFiltros() {
    return {
      categorias: categorias.filter((c) => c.checked).map((c) => c.value),
      linha: document.querySelector('[data-filter-line]:checked')?.value || '',
      tags: Array.from(document.querySelectorAll('[data-filter-tag]:checked')).map((el) => el.value),
      // No teto, o filtro de preço está desligado.
      precoMax: preco && Number(preco.value) < Number(preco.max) ? Number(preco.value) : null,
    };
  }

  function combina(card, f) {
    if (termo && !(card.dataset.busca || '').includes(termo)) return false;
    // Várias categorias marcadas somam (Salgados OU Doces).
    if (f.categorias.length && !f.categorias.includes(card.dataset.categoria)) return false;
    if (f.linha && card.dataset.linha !== 'AMBOS' && card.dataset.linha !== f.linha) return false;
    // Restrições se acumulam: vegano E sem lactose.
    const etiquetas = (card.dataset.tags || '').split(',');
    if (!f.tags.every((t) => etiquetas.includes(t))) return false;
    if (f.precoMax !== null && Number(card.dataset.unitario) > f.precoMax) return false;
    return true;
  }

  const criterios = {
    destaque: (a, b) =>
      Number(b.dataset.destaque) - Number(a.dataset.destaque) || Number(a.dataset.ordem) - Number(b.dataset.ordem),
    'preco-asc': (a, b) => Number(a.dataset.unitario) - Number(b.dataset.unitario),
    'preco-desc': (a, b) => Number(b.dataset.unitario) - Number(a.dataset.unitario),
    nome: (a, b) => (a.dataset.nome || '').localeCompare(b.dataset.nome || '', 'pt-BR'),
  };

  function atualizarPreco() {
    if (!preco) return;
    const min = Number(preco.min);
    const max = Number(preco.max);
    const valor = Number(preco.value);
    preco.style.setProperty('--pct', `${((valor - min) / (max - min || 1)) * 100}%`);
    const texto = valor >= max ? 'Qualquer preço' : `Até ${brl.format(valor)}`;
    if (precoSaida) precoSaida.textContent = texto;
    preco.setAttribute('aria-valuetext', texto);
  }

  /** ‹ 1 … 4 5 6 … 10 › — primeira, última e vizinhas da atual. */
  function desenharPaginas(total) {
    if (!paginacao) return;
    const paginas = Math.ceil(total / POR_PAGINA);
    paginacao.replaceChildren();
    paginacao.hidden = paginas <= 1;
    if (paginas <= 1) return;

    const botao = (rotulo, alvo, nome, { atual = false, desligado = false } = {}) => {
      const b = document.createElement('button');
      b.type = 'button';
      b.textContent = rotulo;
      b.dataset.irPara = String(alvo);
      b.setAttribute('aria-label', nome);
      if (atual) b.setAttribute('aria-current', 'page');
      b.disabled = desligado;
      return b;
    };

    paginacao.append(botao('‹', pagina - 1, 'Página anterior', { desligado: pagina === 1 }));
    let ultimo = 0;
    for (let n = 1; n <= paginas; n += 1) {
      if (n !== 1 && n !== paginas && Math.abs(n - pagina) > 1) continue;
      if (n - ultimo > 1) {
        const reticencias = document.createElement('span');
        reticencias.className = 'grid min-w-6 place-items-center text-crust';
        reticencias.setAttribute('aria-hidden', 'true');
        reticencias.textContent = '…';
        paginacao.append(reticencias);
      }
      paginacao.append(botao(String(n), n, `Página ${n}`, { atual: n === pagina }));
      ultimo = n;
    }
    paginacao.append(botao('›', pagina + 1, 'Próxima página', { desligado: pagina === paginas }));
  }

  function filtrar({ manterPagina = false } = {}) {
    if (!manterPagina) pagina = 1;
    const f = lerFiltros();
    const ordem = criterios[ordenar?.value] || criterios.destaque;
    const achados = cards.filter((card) => combina(card, f)).sort(ordem);
    const paginas = Math.max(1, Math.ceil(achados.length / POR_PAGINA));
    pagina = Math.min(Math.max(1, pagina), paginas);

    const inicio = (pagina - 1) * POR_PAGINA;
    const naPagina = new Set(achados.slice(inicio, inicio + POR_PAGINA));

    // A grade segue a ordem escolhida; os que não combinam vão para o fim, escondidos.
    const resto = cards.filter((card) => !achados.includes(card));
    [...achados, ...resto].forEach((card) => grade.append(card));
    cards.forEach((card) => card.classList.toggle('oculto', !naPagina.has(card)));

    grade.classList.toggle('oculto', achados.length === 0);
    semResultado?.classList.toggle('oculto', achados.length > 0);

    const filtrando = termo !== '' || f.categorias.length > 0 || f.linha !== '' || f.tags.length > 0 || f.precoMax !== null;
    let texto;
    if (achados.length === 0) texto = 'Nenhum item encontrado';
    // A concordância segue o total, não o filtrado: "1 de 18 itens".
    else if (filtrando) texto = `${achados.length} de ${cards.length} ${plural(cards.length)}`;
    else texto = `${cards.length} ${plural(cards.length)}`;
    if (paginas > 1) texto += `, página ${pagina} de ${paginas}`;
    contagens.forEach((el) => { el.textContent = texto; });

    // "Todas" fica marcada exatamente quando nenhuma categoria específica está.
    if (todas) todas.checked = f.categorias.length === 0;

    const ligados = f.categorias.length + (f.linha ? 1 : 0) + f.tags.length + (f.precoMax !== null ? 1 : 0);
    document.querySelectorAll('[data-filtros-contagem]').forEach((el) => {
      el.textContent = String(ligados);
      el.classList.toggle('oculto', ligados === 0);
    });
    document.querySelectorAll('[data-ver-itens]').forEach((b) => {
      b.textContent = achados.length ? `Ver ${achados.length} ${plural(achados.length)}` : 'Nenhum item: ajuste os filtros';
    });

    desenharPaginas(achados.length);
  }

  categorias.forEach((c) => c.addEventListener('change', () => filtrar()));
  todas?.addEventListener('change', () => {
    if (todas.checked) categorias.forEach((c) => { c.checked = false; });
    filtrar();
  });
  document.querySelectorAll('[data-filter-line], [data-filter-tag]').forEach((el) => el.addEventListener('change', () => filtrar()));
  preco?.addEventListener('input', () => {
    atualizarPreco();
    filtrar();
  });
  ordenar?.addEventListener('change', () => filtrar());

  campoBusca?.addEventListener('input', () => {
    termo = normalizar(campoBusca.value.trim());
    filtrar();
  });

  // "Buscar" filtra (já filtrou ao digitar), fecha o teclado e leva aos resultados.
  document.querySelector('[data-busca-form]')?.addEventListener('submit', (evento) => {
    evento.preventDefault();
    termo = normalizar((campoBusca?.value || '').trim());
    filtrar();
    campoBusca?.blur();
    document.getElementById('contagem-catalogo')?.scrollIntoView({ behavior: movimentoSuave(), block: 'start' });
  });

  // Trocar de página leva o foco para a grade nova (e o leitor de tela lê a contagem).
  grade.tabIndex = -1;
  paginacao?.addEventListener('click', (evento) => {
    const alvo = evento.target instanceof Element ? evento.target.closest('[data-ir-para]') : null;
    if (!alvo || alvo.disabled) return;
    pagina = Number(alvo.dataset.irPara);
    filtrar({ manterPagina: true });
    document.getElementById('contagem-catalogo')?.scrollIntoView({ behavior: movimentoSuave(), block: 'start' });
    grade.focus({ preventScroll: true });
  });

  document.addEventListener('click', (evento) => {
    const alvo = evento.target;
    if (!(alvo instanceof Element) || !alvo.closest('[data-limpar-filtros]')) return;
    termo = '';
    if (campoBusca) campoBusca.value = '';
    categorias.forEach((c) => { c.checked = false; });
    document.querySelectorAll('[data-filter-tag]').forEach((el) => { el.checked = false; });
    const tudo = document.querySelector('[data-filter-line][value=""]');
    if (tudo) tudo.checked = true;
    if (preco) preco.value = preco.max;
    atualizarPreco();
    filtrar();
  });

  /* -------------------------------------------------------------------------
   * PAINEL DE FILTROS NO CELULAR
   * Abaixo de 1024px o <aside> vira um painel por cima da grade. Enquanto
   * aberto ele se comporta como diálogo: foco preso dentro, Esc fecha, e o
   * foco volta para o botão "Filtros".
   * ---------------------------------------------------------------------- */
  const painel = document.querySelector('[data-filtros-painel]');
  const abridor = document.querySelector('[data-abrir-filtros]');
  const fundo = document.querySelector('.filtros-fundo');

  if (painel && abridor) {
    const aberto = () => painel.classList.contains('aberto');

    function abrirFiltros() {
      painel.classList.add('aberto');
      painel.setAttribute('role', 'dialog');
      painel.setAttribute('aria-modal', 'true');
      if (fundo) fundo.hidden = false;
      abridor.setAttribute('aria-expanded', 'true');
      document.documentElement.style.overflow = 'hidden';
      setTimeout(() => painel.querySelector('.filtros-fechar')?.focus(), 60);
    }

    function fecharFiltros({ devolverFoco = true } = {}) {
      if (!aberto()) return;
      painel.classList.remove('aberto');
      painel.removeAttribute('role');
      painel.removeAttribute('aria-modal');
      if (fundo) fundo.hidden = true;
      abridor.setAttribute('aria-expanded', 'false');
      document.documentElement.style.overflow = '';
      if (devolverFoco) abridor.focus();
    }

    abridor.addEventListener('click', abrirFiltros);
    document.querySelectorAll('[data-fechar-filtros]').forEach((el) => el.addEventListener('click', () => fecharFiltros()));

    document.addEventListener('keydown', (evento) => {
      if (!aberto()) return;
      if (evento.key === 'Escape') {
        fecharFiltros();
        return;
      }
      if (evento.key !== 'Tab') return;
      const focaveis = Array.from(painel.querySelectorAll('button, input, select, a[href]'))
        .filter((el) => !el.disabled && el.getClientRects().length > 0);
      const primeiro = focaveis[0];
      const ultimo = focaveis[focaveis.length - 1];
      if (evento.shiftKey && document.activeElement === primeiro) {
        evento.preventDefault();
        ultimo.focus();
      } else if (!evento.shiftKey && document.activeElement === ultimo) {
        evento.preventDefault();
        primeiro.focus();
      }
    });

    // Girou o tablet ou abriu a janela: no computador o painel é coluna, não diálogo.
    window.matchMedia('(min-width: 64rem)').addEventListener('change', (consulta) => {
      if (consulta.matches) fecharFiltros({ devolverFoco: false });
    });
  }

  // Vindo do caminho da página do produto (/?categoria=Assados#catalogo): já abre filtrado.
  const categoriaPedida = new URLSearchParams(window.location.search).get('categoria');
  if (categoriaPedida) categorias.forEach((c) => { c.checked = c.value === categoriaPedida; });

  atualizarPreco();
  filtrar();
}

/* Lupa do cabeçalho: na home leva à busca do catálogo; nas outras páginas o
   link vai para /#busca e a busca recebe o foco quando a home abre. */
document.addEventListener('click', (evento) => {
  const link = evento.target instanceof Element ? evento.target.closest('[data-focar-busca]') : null;
  const campo = document.querySelector('[data-busca-input]');
  if (!link || !campo) return;
  evento.preventDefault();
  campo.scrollIntoView({ behavior: movimentoSuave(), block: 'center' });
  campo.focus({ preventScroll: true });
});

if (window.location.hash === '#busca') {
  document.querySelector('[data-busca-input]')?.focus();
}

/* Botão "+": responde ao clique trocando o sinal por um visto por um instante.
   Quem coloca no carrinho é cart.js; aqui é só o retorno visual. */
const temporizadoresMais = new WeakMap();
document.addEventListener('click', (evento) => {
  const mais = evento.target instanceof Element ? evento.target.closest('.botao-mais') : null;
  if (!mais) return;
  mais.classList.add('feito');
  clearTimeout(temporizadoresMais.get(mais));
  temporizadoresMais.set(mais, setTimeout(() => mais.classList.remove('feito'), 1400));
});

/* ---------------------------------------------------------------------------
 * VISTOS RECENTEMENTE
 * A página do produto anota o slug no navegador; a home mostra os últimos
 * quatro, copiando os cards prontos do <template>. Sem histórico (ou com o
 * armazenamento bloqueado), a seção simplesmente não aparece.
 * ------------------------------------------------------------------------ */
const CHAVE_VISTOS = 'dolce:vistos';

function lerVistos() {
  try {
    const lista = JSON.parse(localStorage.getItem(CHAVE_VISTOS) || '[]');
    return Array.isArray(lista) ? lista.filter((s) => typeof s === 'string') : [];
  } catch {
    return [];
  }
}

const produtoAberto = document.querySelector('[data-produto-visto]');
if (produtoAberto) {
  const slug = produtoAberto.dataset.produtoVisto;
  try {
    localStorage.setItem(CHAVE_VISTOS, JSON.stringify([slug, ...lerVistos().filter((s) => s !== slug)].slice(0, 8)));
  } catch {
    /* navegador sem armazenamento: só não haverá "vistos recentemente" */
  }
}

const secaoVistos = document.querySelector('[data-vistos]');
const modelosVistos = document.querySelector('[data-vistos-modelos]');
if (secaoVistos && modelosVistos) {
  const porSlug = new Map(
    Array.from(modelosVistos.content.querySelectorAll('[data-produto]')).map((card) => [card.dataset.slug, card])
  );
  const escolhidos = lerVistos().map((slug) => porSlug.get(slug)).filter(Boolean).slice(0, 4);
  if (escolhidos.length) {
    secaoVistos.querySelector('[data-vistos-grade]')?.replaceChildren(...escolhidos.map((card) => document.importNode(card, true)));
    secaoVistos.classList.remove('oculto');
  }
}

/* ===========================================================================
 * 5. REVELAÇÃO AO ROLAR
 * Uma passada só: o card aparece e o observador o solta.
 * ======================================================================== */

const aRevelar = document.querySelectorAll('.revelar');

if (aRevelar.length) {
  const semMovimento = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const revelarTudo = () => aRevelar.forEach((el) => el.classList.add('visivel'));

  // Aba em segundo plano não roda o observador (nem requestAnimationFrame).
  // Aí não há animação: o conteúdo simplesmente já nasce visível. O modo de
  // falha desta animação nunca pode ser "o catálogo some".
  if (semMovimento || document.visibilityState === 'hidden' || !('IntersectionObserver' in window)) {
    revelarTudo();
  } else {
    const observador = new IntersectionObserver(
      (entradas) => {
        entradas.forEach((entrada) => {
          if (!entrada.isIntersecting) return;
          entrada.target.classList.add('visivel');
          observador.unobserve(entrada.target);
        });
      },
      // Margem generosa embaixo: o card já entra revelado quando a pessoa rola
      // rápido. A animação é um detalhe, não pode virar buraco branco na tela.
      { rootMargin: '0px 0px 280px 0px', threshold: 0 }
    );
    aRevelar.forEach((el) => observador.observe(el));
  }
}

/* ===========================================================================
 * 6. AVISOS CURTOS
 * ======================================================================== */

const areaAvisos = document.querySelector('[data-toast-area]');

function mostrarAviso(mensagem, { acao } = {}) {
  if (!areaAvisos) return;

  const aviso = document.createElement('div');
  aviso.className =
    'alert flex w-auto max-w-sm items-center gap-3 rounded-2xl border-none bg-neutral py-3 text-sm font-semibold text-neutral-content shadow-bandeja-alta';

  const texto = document.createElement('span');
  texto.textContent = mensagem;
  aviso.append(texto);

  if (acao) {
    const botao = document.createElement('button');
    botao.type = 'button';
    botao.className = 'rounded-lg bg-accent px-3 py-1.5 text-xs font-bold text-accent-content';
    botao.textContent = acao.texto;
    botao.addEventListener('click', () => {
      acao.aoClicar();
      aviso.remove();
    });
    aviso.append(botao);
  }

  areaAvisos.append(aviso);
  setTimeout(() => aviso.remove(), 4200);
}

/* Feedback ao adicionar: o ícone do carrinho pula e um aviso aparece. */
document.addEventListener('dolce:adicionado', (evento) => {
  document.querySelectorAll('[data-cart-icon]').forEach((icone) => {
    icone.classList.remove('pulando');
    void icone.offsetWidth; // reinicia a animação
    icone.classList.add('pulando');
  });

  const nome = evento.detail?.item?.nome || 'Item';
  mostrarAviso(`${nome} entrou no pedido.`, {
    acao: { texto: 'Ver pedido', aoClicar: () => abrirDrawer(null) },
  });
});

document.addEventListener('dolce:removido', () => mostrarAviso('Pedido atualizado.'));
document.addEventListener('dolce:aviso', (evento) => mostrarAviso(evento.detail.mensagem));

/* ===========================================================================
 * 7. QUANTIDADE NA PÁGINA DO PRODUTO
 * ======================================================================== */

const campoQtd = document.querySelector('[data-qty-input]');

if (campoQtd) {
  const saidaSubtotal = document.querySelector('[data-subtotal]');
  const botaoAdd = document.querySelector('[data-add][data-usa-qty]');
  const brl = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });

  function normalizarQtd() {
    const min = Number(campoQtd.min) || 1;
    const passo = Number(campoQtd.step) || 1;
    const valor = Number(campoQtd.value);
    const ajustado =
      !Number.isFinite(valor) || valor <= min ? min : min + Math.round((valor - min) / passo) * passo;
    campoQtd.value = String(ajustado);
    return ajustado;
  }

  function atualizarSubtotal() {
    const qtd = normalizarQtd();
    const preco = Number(botaoAdd?.dataset.preco) || 0;
    if (saidaSubtotal) saidaSubtotal.textContent = brl.format(preco * qtd);
  }

  document.querySelector('[data-qty-mais]')?.addEventListener('click', () => {
    campoQtd.value = String(Number(campoQtd.value) + (Number(campoQtd.step) || 1));
    atualizarSubtotal();
  });

  document.querySelector('[data-qty-menos]')?.addEventListener('click', () => {
    campoQtd.value = String(Number(campoQtd.value) - (Number(campoQtd.step) || 1));
    atualizarSubtotal();
  });

  campoQtd.addEventListener('change', atualizarSubtotal);
  atualizarSubtotal();

  /* Variações de embalagem: trocar o rádio reajusta preço, mínimo e passo. */
  document.querySelectorAll('[data-faixa]').forEach((radio) => {
    radio.addEventListener('change', () => {
      if (!radio.checked || !botaoAdd) return;
      const d = radio.dataset;

      botaoAdd.dataset.preco = d.preco;
      botaoAdd.dataset.por = d.por;
      botaoAdd.dataset.min = d.min;
      botaoAdd.dataset.passo = d.passo;

      campoQtd.min = d.min;
      campoQtd.step = d.passo;
      campoQtd.value = d.min;

      const cheio = document.querySelector('[data-preco-cheio]');
      const por = document.querySelector('[data-preco-por]');
      if (cheio) cheio.textContent = Number(d.valor).toLocaleString('pt-BR', { minimumFractionDigits: 2 });
      if (por) por.textContent = `/ ${String(d.por).split('/').pop().trim()}`;

      atualizarSubtotal();
    });
  });
}

/* ===========================================================================
 * 8. CONFIRMAÇÃO DO PEDIDO (carrinho.php)
 *
 * Duas coisas, as duas sobre deixar a escolha visível:
 *   - o campo de endereço aparece quando a escolha é entrega;
 *   - a linha "Você escolheu" repete, por extenso, o que está marcado.
 *
 * A linha de resumo existe porque os rádios ficam no alto do bloco e o botão do
 * WhatsApp na outra coluna — no celular, duas telas depois. Ela é a última
 * coisa que se lê antes de sair daqui.
 *
 * Quem lê os valores no fechamento é checkout(), em cart.js. Aqui é só interface.
 * ======================================================================== */

const escolhasDoPedido = document.querySelectorAll('[data-pedido-entrega], [data-pedido-pagamento]');

if (escolhasDoPedido.length) {
  const campoEndereco = document.querySelector('[data-pedido-endereco-campo]');
  const saidaResumo = document.querySelector('[data-pedido-resumo]');

  /** O texto do cartão marcado — o rótulo que a pessoa leu, não o value. */
  const rotuloMarcado = (seletor) => {
    const marcado = document.querySelector(`${seletor}:checked`);
    return marcado?.closest('.opcao')?.querySelector('span span')?.textContent.trim() || '';
  };

  function aplicarEscolhasDoPedido() {
    const entrega = document.querySelector('[data-pedido-entrega]:checked');
    const ehEntrega = (entrega?.value || '').toLowerCase().startsWith('entrega');

    campoEndereco?.classList.toggle('oculto', !ehEntrega);

    if (saidaResumo) {
      const partes = [rotuloMarcado('[data-pedido-entrega]'), rotuloMarcado('[data-pedido-pagamento]')];
      saidaResumo.textContent = partes.filter(Boolean).join(' · ');
    }
  }

  escolhasDoPedido.forEach((radio) => {
    radio.addEventListener('change', aplicarEscolhasDoPedido);
  });

  // O HTML nasce com "retirar" e "Pix" marcados, mas o navegador restaura a
  // escolha anterior num F5 — então o estado inicial é lido, não presumido.
  aplicarEscolhasDoPedido();
}
