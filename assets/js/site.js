// O que é igual em todas as páginas: menu, gaveta do carrinho, avisos e card de produto.
// Os textos do topo e do rodapé estão escritos direto em cada .html (procure por ALTERAR).
// Cada página chama iniciarPagina() passando a função que desenha o conteúdo dela.

async function iniciarPagina(desenharPagina) {
  try {
    await carregarDados();
  } catch (erro) {
    document.getElementById('conteudo').insertAdjacentHTML('afterbegin',
      '<p class="p-6 text-center font-semibold" role="alert">Não conseguimos carregar o catálogo. Atualize a página.</p>');
    return;
  }

  preencherTopo();
  if (desenharPagina) {
    desenharPagina();
  }
  mostrarCarrinho();
}

// ---------- Topo ----------

function preencherTopo() {
  // Marca no menu a página em que a pessoa está.
  const paginaAtual = location.pathname.split('/').pop();
  const links = document.querySelectorAll('[data-menu-link]');
  for (let i = 0; i < links.length; i++) {
    if (links[i].getAttribute('href') === paginaAtual) {
      links[i].setAttribute('aria-current', 'page');
      links[i].classList.add('font-bold');
    }
  }

  // O link "Promoções" já vem visível no HTML (o caso comum) e só some sem promoção,
  // para o menu não pular para o lado a cada troca de página.
  if (site.promocoes.length === 0) {
    const itens = document.querySelectorAll('[data-so-com-promocoes]');
    for (let i = 0; i < itens.length; i++) {
      itens[i].hidden = true;
    }
  }

  // PDFs das lojas no menu "Catálogo": nome e arquivo vêm do cadastro no Sanity.
  // A matriz fica de fora, porque o dela é o "Baixar PDF completo".
  let pdfs = '';
  for (let i = 0; i < site.unidades.length; i++) {
    const unidade = site.unidades[i];
    const arquivo = caminhoArquivo(unidade.catalogoPdf);
    if (unidade.matriz || !arquivo) {
      continue;
    }
    pdfs += `<li><a href="${limpar(arquivo)}" download class="flex items-center gap-2 rounded-xl px-3 py-2 font-medium">${limpar(unidade.nome)}${ICONE_BAIXAR}</a></li>`;
  }
  const lugares = document.querySelectorAll('[data-pdfs-unidades]');
  for (let i = 0; i < lugares.length; i++) {
    lugares[i].insertAdjacentHTML('beforebegin', pdfs);
  }
}

const ICONE_BAIXAR = '<svg class="ml-auto h-4 w-4 shrink-0 opacity-45" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v12"/><path d="m7 11 5 5 5-5"/><path d="M5 21h14"/></svg>';

// ---------- Card de produto (usado na home e na página do produto) ----------

function cardDoProduto(produto) {
  const faixa = produto.faixa;
  const link = pagina('produto.html?slug=' + encodeURIComponent(produto.slug));
  const promocoes = promocoesDoProduto(produto);

  let foto = `<span class="cartao-sem-foto">${iconeProduto(produto.categoria, produto.nome)}</span>`;
  if (produto.imagem) {
    foto = `<img src="${limpar(caminhoArquivo(produto.imagem))}" alt="" loading="lazy" width="480" height="360">`;
  }

  // Com desconto valendo hoje, o selo é o dessa promoção, para bater com o preço riscado.
  const promocaoComDesconto = melhorPromocao(produto.slug);
  let promocaoDoSelo = promocoes[0];
  let preco = `<strong>${dinheiro(faixa.valor)}</strong>`;
  if (promocaoComDesconto) {
    promocaoDoSelo = promocaoComDesconto;
    preco = `<s>${dinheiro(faixa.valor)}</s> <strong>${dinheiro(valorComDesconto(faixa.valor, promocaoComDesconto.desconto))}</strong>`;
  }

  let selo = '';
  if (promocaoDoSelo) {
    selo = `<p class="cartao-selos"><span class="selo selo-promo">${limpar(promocaoDoSelo.selo || 'Promoção')}</span></p>`;
  }

  let botao = '<span class="cartao-indisponivel">Indisponível hoje</span>';
  if (produto.disponivel) {
    botao = `<button type="button" class="botao-mais" aria-label="Adicionar ${limpar(produto.nome)} ao pedido" ${dadosDoBotao(produto)}>
        <svg class="icone-mais" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
        <svg class="icone-feito" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 5 5 9-10"/></svg>
      </button>`;
  }

  let destaque = '0';
  if (produto.destaque) {
    destaque = '1';
  }

  let temPromocao = '0';
  if (promocoes.length > 0) {
    temPromocao = '1';
  }

  return `
    <article class="cartao" data-produto
      data-categoria="${limpar(produto.categoria)}"
      data-promo="${temPromocao}"
      data-tags="${limpar(produto.tags.join(','))}"
      data-busca="${limpar(semAcento(produto.nome + ' ' + produto.categoria + ' ' + produto.descricao + ' ' + produto.tags.join(' ')))}"
      data-nome="${limpar(semAcento(produto.nome))}"
      data-unitario="${faixa.unitario}"
      data-destaque="${destaque}">
      <a href="${link}" class="cartao-foto" tabindex="-1" aria-hidden="true">${foto}</a>
      ${selo}
      <div class="cartao-corpo">
        <h3 class="cartao-nome"><a href="${link}">${limpar(produto.nome)}</a></h3>
        <p class="cartao-descricao">${limpar(produto.descricao)}</p>
        <div class="cartao-rodape">
          <p class="cartao-preco">${preco} <span>/ ${limpar(faixa.porCurto)}</span></p>
          ${botao}
        </div>
      </div>
    </article>`;
}

// Os dados que o carrinho lê quando o botão de adicionar é clicado.
function dadosDoBotao(produto) {
  return `data-add
    data-slug="${limpar(produto.slug)}"
    data-nome="${limpar(produto.nome)}"
    data-preco="${produto.faixa.unitario}"
    data-por="${limpar(produto.faixa.exibicao)}"
    data-min="${produto.faixa.min}"
    data-passo="${produto.faixa.passo}"
    data-imagem="${limpar(produto.imagem || '')}"
    data-categoria="${limpar(produto.categoria)}"`;
}

// ---------- Menus que abrem e fecham ----------

// Clicou fora de um menu aberto: fecha.
document.addEventListener('click', function (evento) {
  const menus = document.querySelectorAll('details.dropdown[open]');
  for (let i = 0; i < menus.length; i++) {
    if (!menus[i].contains(evento.target)) {
      menus[i].open = false;
    }
  }
});

document.addEventListener('keydown', function (evento) {
  if (evento.key === 'Escape') {
    const menus = document.querySelectorAll('details.dropdown[open]');
    for (let i = 0; i < menus.length; i++) {
      menus[i].open = false;
    }
    fecharGaveta();
  }
});

// ---------- Gaveta do carrinho ----------

function abrirGaveta() {
  document.getElementById('carrinho-toggle').checked = true;
  document.getElementById('painel-carrinho').inert = false;
  document.querySelector('[data-cart-close]').focus();
}

function fecharGaveta() {
  document.getElementById('carrinho-toggle').checked = false;
  document.getElementById('painel-carrinho').inert = true;
}

document.addEventListener('click', function (evento) {
  if (evento.target.closest('[data-cart-open]')) {
    abrirGaveta();
  } else if (evento.target.closest('.drawer-overlay')) {
    // O overlay é um <label for="carrinho-toggle">: sem isto, o navegador marca o checkbox
    // de novo depois de fecharGaveta() e a gaveta reabre travada (inert).
    evento.preventDefault();
    fecharGaveta();
  } else if (evento.target.closest('[data-cart-close]')) {
    fecharGaveta();
  }
});

fecharGaveta();

// ---------- Botão "+" mostra um visto por um instante ----------

document.addEventListener('click', function (evento) {
  const botao = evento.target.closest('.botao-mais');
  if (botao) {
    botao.classList.add('feito');
    setTimeout(function () {
      botao.classList.remove('feito');
    }, 1400);
  }
});

// ---------- Avisos curtos ----------

const DURACAO_AVISO = 4000;
const ICONE_SUCESSO = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 5 5 9-10"/></svg>';
const ICONE_ERRO = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>';
const ICONE_FECHAR = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>';

// tipo: 'sucesso' ou 'erro'.
function mostrarAviso(tipo, titulo, detalhe) {
  const area = document.querySelector('[data-toast-area]');

  let icone = ICONE_SUCESSO;
  if (tipo === 'erro') {
    icone = ICONE_ERRO;
  }

  let html = `<span class="aviso-icone">${icone}</span>`;
  html += `<div class="aviso-texto"><p class="aviso-titulo">${limpar(titulo)}</p>`;
  if (detalhe) {
    html += `<p class="aviso-detalhe">${limpar(detalhe)}</p>`;
  }
  html += '</div>';
  html += `<button type="button" class="aviso-fechar" aria-label="Fechar aviso">${ICONE_FECHAR}</button>`;
  html += '<span class="aviso-tempo" aria-hidden="true"></span>';

  const aviso = document.createElement('div');
  aviso.className = 'aviso aviso-' + tipo;
  aviso.style.setProperty('--aviso-tempo', DURACAO_AVISO + 'ms');
  aviso.innerHTML = html;
  area.append(aviso);

  // Leitor de tela também ouve o aviso.
  let leitura = titulo;
  if (tipo === 'erro') {
    leitura = 'Erro: ' + titulo;
  }
  document.querySelector('[data-aviso-leitor]').textContent = leitura;

  // O ✕ e o fim do tempo podem chegar juntos: fecha uma vez só.
  let fechado = false;
  function fechar() {
    if (fechado) {
      return;
    }
    fechado = true;
    aviso.classList.add('saindo');
    setTimeout(function () {
      aviso.remove();
    }, 200);
  }
  aviso.querySelector('.aviso-fechar').addEventListener('click', fechar);
  setTimeout(fechar, DURACAO_AVISO);
}

