/**
 * =============================================================================
 * assets/js/ui.js — comportamento da interface
 * =============================================================================
 * JavaScript puro, sem framework. Cuida de:
 *   1. tema claro/escuro
 *   2. seletor de unidade (persistido em localStorage['dolce_unit'])
 *   3. dropdowns <details> (fechar ao clicar fora / Esc)
 *   4. drawer do carrinho, com foco e teclado
 *   5. busca e filtros do catálogo
 *   6. revelação dos cards ao rolar (IntersectionObserver)
 *   7. avisos curtos (toasts)
 *
 * O estado do carrinho em si mora em assets/js/cart.js.
 */

import { lerUnidades, unidadeAtual } from './cart.js';

const CHAVE_TEMA = 'dolce_theme';
const CHAVE_UNIDADE = 'dolce_unit';

const raiz = document.documentElement;

/* ===========================================================================
 * 1. TEMA
 * ======================================================================== */

function aplicarTema(tema) {
  raiz.dataset.theme = tema;
  const escuro = tema === 'dolce-dark';

  document.querySelectorAll('[data-theme-toggle]').forEach((botao) => {
    botao.setAttribute('aria-pressed', String(escuro));
    botao.setAttribute('aria-label', escuro ? 'Voltar para o modo claro' : 'Ativar modo escuro');
  });
}

document.querySelectorAll('[data-theme-toggle]').forEach((botao) => {
  botao.addEventListener('click', () => {
    const proximo = raiz.dataset.theme === 'dolce-dark' ? 'dolce' : 'dolce-dark';
    try {
      localStorage.setItem(CHAVE_TEMA, proximo);
    } catch {
      /* sem localStorage: vale só nesta página */
    }
    aplicarTema(proximo);
  });
});

aplicarTema(raiz.dataset.theme || 'dolce');

/* ===========================================================================
 * 2. SELETOR DE UNIDADE
 * A escolha define para qual WhatsApp o pedido vai e qual PDF é oferecido.
 * ======================================================================== */

function escolherUnidade(slug, { avisar = false } = {}) {
  const unidade = lerUnidades().find((u) => u.slug === slug);
  if (!unidade) return;

  try {
    localStorage.setItem(CHAVE_UNIDADE, unidade.slug);
  } catch {
    /* sem localStorage: vale só nesta página */
  }

  refletirUnidade();
  if (avisar) mostrarAviso(`Pedido pela ${unidade.nome}.`);
}

function refletirUnidade() {
  const unidade = unidadeAtual();
  if (!unidade) return;

  document.querySelectorAll('[data-unit-label]').forEach((el) => {
    el.textContent = unidade.nome;
  });

  document.querySelectorAll('[data-unit-option]').forEach((opcao) => {
    const escolhida = opcao.dataset.unitOption === unidade.slug;
    if (opcao.hasAttribute('role')) opcao.setAttribute('aria-selected', String(escolhida));
    const check = opcao.querySelector('[data-unit-check]');
    if (check) check.classList.toggle('oculto', !escolhida);
  });

  // Mantém o PDF do catálogo apontando para a unidade escolhida.
  document.querySelectorAll('[data-unit-pdf]').forEach((link) => {
    link.href = unidade.catalogoPdf || '#';
  });
}

document.addEventListener('click', (evento) => {
  const opcao = evento.target instanceof Element ? evento.target.closest('[data-unit-option]') : null;
  if (!opcao) return;
  escolherUnidade(opcao.dataset.unitOption, { avisar: true });
  fecharDropdowns();
});

// Primeira visita: grava a matriz para que a unidade mostrada e a usada no
// checkout sejam sempre a mesma coisa.
(function inicializarUnidade() {
  let salva = null;
  try {
    salva = localStorage.getItem(CHAVE_UNIDADE);
  } catch {
    /* ignora */
  }
  const unidade = unidadeAtual();
  if (unidade && salva !== unidade.slug) escolherUnidade(unidade.slug);
  refletirUnidade();
})();

/* ===========================================================================
 * 3. DROPDOWNS <details>
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
 * 4. DRAWER DO CARRINHO
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
 * 5. BUSCA E FILTROS DO CATÁLOGO
 * Filtragem no próprio DOM: o PHP já imprimiu todos os cards.
 * ======================================================================== */

const grade = document.querySelector('[data-grade-produtos]');

if (grade) {
  const cards = Array.from(grade.querySelectorAll('[data-produto]'));
  const campoBusca = document.querySelector('[data-busca-input]');
  const semResultado = document.querySelector('[data-sem-resultado]');
  const contagem = document.querySelector('[data-contagem]');

  let termo = '';
  let categoria = '';
  let linha = '';

  /** minúsculas e sem acento, igual ao índice gerado pelo PHP */
  const marcasDeAcento = new RegExp('[\\u0300-\\u036f]', 'g');
  const normalizar = (texto) => texto.toLowerCase().normalize('NFD').replace(marcasDeAcento, '');

  function filtrar() {
    let visiveis = 0;

    cards.forEach((card) => {
      const combina =
        (termo === '' || (card.dataset.busca || '').includes(termo)) &&
        (categoria === '' || card.dataset.categoria === categoria) &&
        (linha === '' || card.dataset.linha === linha);

      card.classList.toggle('oculto', !combina);
      if (combina) visiveis += 1;
    });

    const filtrando = termo !== '' || categoria !== '' || linha !== '';

    if (semResultado) semResultado.classList.toggle('oculto', visiveis > 0);
    grade.classList.toggle('oculto', visiveis === 0);

    if (contagem) {
      if (visiveis === 0) {
        contagem.textContent = 'Nenhum item encontrado';
      } else if (filtrando) {
        // A concordância segue o total, não o filtrado: "1 de 18 itens".
        contagem.textContent = `${visiveis} de ${cards.length} ${cards.length === 1 ? 'item' : 'itens'}`;
      } else {
        contagem.textContent = `${cards.length} itens no catálogo`;
      }
    }
  }

  function marcarGrupo(seletor, valorAtivo) {
    document.querySelectorAll(seletor).forEach((botao) => {
      const chave = seletor.includes('categoria') ? botao.dataset.filtroCategoria : botao.dataset.filtroLinha;
      botao.setAttribute('aria-pressed', String(chave === valorAtivo));
    });
  }

  campoBusca?.addEventListener('input', () => {
    termo = normalizar(campoBusca.value.trim());
    filtrar();
  });

  document.addEventListener('click', (evento) => {
    const alvo = evento.target;
    if (!(alvo instanceof Element)) return;

    const porCategoria = alvo.closest('[data-filtro-categoria]');
    if (porCategoria) {
      categoria = porCategoria.dataset.filtroCategoria;
      marcarGrupo('[data-filtro-categoria]', categoria);
      filtrar();
      return;
    }

    const porLinha = alvo.closest('[data-filtro-linha]');
    if (porLinha) {
      linha = porLinha.dataset.filtroLinha;
      marcarGrupo('[data-filtro-linha]', linha);
      filtrar();
      return;
    }

    if (alvo.closest('[data-limpar-filtros]')) {
      termo = '';
      categoria = '';
      linha = '';
      if (campoBusca) campoBusca.value = '';
      marcarGrupo('[data-filtro-categoria]', '');
      marcarGrupo('[data-filtro-linha]', '');
      filtrar();
      campoBusca?.focus();
    }
  });
}

/* ===========================================================================
 * 6. REVELAÇÃO AO ROLAR
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
 * 7. AVISOS CURTOS
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
 * 8. QUANTIDADE NA PÁGINA DO PRODUTO
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
