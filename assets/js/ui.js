/**
 * =============================================================================
 * assets/js/ui.js — comportamento da interface
 * =============================================================================
 * JavaScript puro, sem framework. Cuida de:
 *   1. nome da matriz nos rótulos do carrinho
 *   2. dropdowns <details> (fechar ao clicar fora / Esc)
 *   3. drawer do carrinho, com foco e teclado
 *   4. busca e filtros do catálogo
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
 * 4. BUSCA E FILTROS DO CATÁLOGO
 * Filtragem no próprio DOM: o PHP já imprimiu todos os cards.
 * ======================================================================== */

const grade = document.querySelector('[data-grade-produtos]');

if (grade) {
  const cards = Array.from(grade.querySelectorAll('[data-produto]'));
  const campoBusca = document.querySelector('[data-busca-input]');
  const semResultado = document.querySelector('[data-sem-resultado]');
  // Plural: a contagem aparece na barra completa E na barra fina.
  const contagens = document.querySelectorAll('[data-contagem]');
  const definirContagem = (texto) => contagens.forEach((el) => { el.textContent = texto; });

  let termo = '';

  /** minúsculas e sem acento, igual ao índice gerado pelo PHP */
  const marcasDeAcento = new RegExp('[\\u0300-\\u036f]', 'g');
  const normalizar = (texto) => texto.toLowerCase().normalize('NFD').replace(marcasDeAcento, '');

  function filtrar() {
    let visiveis = 0;

    cards.forEach((card) => {
      const combina = termo === '' || (card.dataset.busca || '').includes(termo);

      card.classList.toggle('oculto', !combina);
      if (combina) visiveis += 1;
    });

    const filtrando = termo !== '';

    if (semResultado) semResultado.classList.toggle('oculto', visiveis > 0);
    grade.classList.toggle('oculto', visiveis === 0);

    const plural = cards.length === 1 ? 'item' : 'itens';

    if (visiveis === 0) {
      definirContagem('Nenhum item encontrado');
    } else if (filtrando) {
      // A concordância segue o total, não o filtrado: "1 de 18 itens".
      definirContagem(`${visiveis} de ${cards.length} ${plural}`);
    } else {
      definirContagem(`${cards.length} ${plural}`);
    }
  }

  campoBusca?.addEventListener('input', () => {
    termo = normalizar(campoBusca.value.trim());
    filtrar();
  });

  document.addEventListener('click', (evento) => {
    const alvo = evento.target;
    if (!(alvo instanceof Element)) return;

    if (alvo.closest('[data-limpar-filtros]')) {
      termo = '';
      if (campoBusca) campoBusca.value = '';
      filtrar();
      campoBusca?.focus();
    }

    // Lupa da barra fina: leva de volta ao campo de busca de verdade.
    if (alvo.closest('[data-focar-busca]') && campoBusca) {
      campoBusca.focus();
      campoBusca.scrollIntoView({ block: 'center' });
    }
  });

  /* -------------------------------------------------------------------------
   * BARRA FINA
   * Entra quando a barra completa passa do topo e sai quando o catálogo
   * acaba, para não pairar sobre "Como encomendar".
   *
   * Um listener de scroll com requestAnimationFrame em vez de
   * IntersectionObserver: são duas condições combinadas (passou da barra E o
   * catálogo ainda está na tela), e ler dois rects por quadro é mais simples
   * e mais exato do que sincronizar dois observadores.
   * ---------------------------------------------------------------------- */
  const barraFina = document.querySelector('[data-barra-fina]');
  const barraCheia = document.querySelector('[data-barra-filtros]');
  const secaoCatalogo = document.getElementById('catalogo');

  if (barraFina && barraCheia && secaoCatalogo) {
    const ALTURA_HEADER = 72; // h-[4.5rem] do header fixo
    let agendado = false;

    function avaliarBarraFina() {
      agendado = false;
      const passouDaBarra = barraCheia.getBoundingClientRect().bottom < ALTURA_HEADER;
      // Uma folga para a barra sumir antes de encostar no fim da seção.
      const catalogoNaTela = secaoCatalogo.getBoundingClientRect().bottom > ALTURA_HEADER + 96;
      barraFina.classList.toggle('oculto', !(passouDaBarra && catalogoNaTela));
    }

    function agendarAvaliacao() {
      if (agendado) return;
      agendado = true;
      requestAnimationFrame(avaliarBarraFina);
    }

    window.addEventListener('scroll', agendarAvaliacao, { passive: true });
    window.addEventListener('resize', agendarAvaliacao, { passive: true });

    // Aba em segundo plano não roda requestAnimationFrame, então a barra pode
    // ficar com o estado velho enquanto ninguém olha. Ao voltar, reavalia na
    // hora, sem esperar um scroll.
    document.addEventListener('visibilitychange', () => {
      if (document.visibilityState === 'visible') avaliarBarraFina();
    });

    avaliarBarraFina();
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
