// Página inicial: promoções e catálogo com busca, filtros e páginas.

// ALTERAR: quantos produtos aparecem por página e quais etiquetas viram filtro.
const PRODUTOS_POR_PAGINA = 12;
const RESTRICOES = ['vegano', 'sem lactose', 'sem glúten', 'sem carne'];

let paginaAtual = 1;

iniciarPagina(function () {
  mostrarPromocoes();
  mostrarFiltros();
  mostrarProdutos();
  ligarFiltros();

  // Veio da página do produto clicando na categoria.
  const categoria = new URLSearchParams(location.search).get('categoria');
  if (categoria) {
    for (const caixa of document.querySelectorAll('[data-filter-category]')) {
      caixa.checked = caixa.value === categoria;
    }
  }
  filtrar();

  // Os blocos foram desenhados agora; leva até a parte pedida no endereço.
  if (location.hash === '#busca') {
    document.getElementById('busca').focus();
  } else if (location.hash) {
    const parte = document.getElementById(location.hash.slice(1));
    if (parte) {
      parte.scrollIntoView();
    }
  }
});

// ---------- Promoções ----------

function mostrarPromocoes() {
  const lugar = document.querySelector('[data-promocoes]');
  if (site.promocoes.length === 0) {
    lugar.remove();
    return;
  }

  let ofertas = '';
  for (const promocao of site.promocoes) {
    let quando = 'Programe-se';
    let classeQuando = 'oferta-quando';
    if (promocao.valeHoje) {
      quando = 'Vale hoje';
      classeQuando = 'oferta-quando oferta-quando-hoje';
    }

    let fotos = '';
    for (const produto of promocao.produtosDaPromocao.slice(0, 4)) {
      let foto = iconeProduto(produto.categoria, produto.nome);
      if (produto.imagem) {
        foto = `<img src="${RAIZ}${limpar(produto.imagem)}" alt="" loading="lazy">`;
      }
      fotos += `
        <li>
          <a href="${pagina('produto.html?slug=' + encodeURIComponent(produto.slug))}" class="oferta-produto">
            <span class="oferta-produto-foto">${foto}</span>
            <span class="oferta-produto-nome">${limpar(produto.nome)}</span>
          </a>
        </li>`;
    }

    ofertas += `
      <li class="oferta">
        <div class="oferta-selo">
          <p class="oferta-desconto">${limpar(promocao.selo || 'Oferta')}</p>
          <p class="${classeQuando}">${quando}</p>
        </div>
        <div class="oferta-corpo">
          <h3 class="oferta-titulo">${limpar(promocao.titulo)}</h3>
          <p class="oferta-texto">${limpar(promocao.texto)}</p>
          <ul class="oferta-regras">
            <li>${ICONE_CALENDARIO}${limpar(promocao.agenda)}</li>
            <li>${ICONE_LOCAL}Vale em: ${limpar(promocao.lojas)}</li>
          </ul>
        </div>
        <div class="oferta-produtos"><ul>${fotos}</ul></div>
      </li>`;
  }

  lugar.outerHTML = `
    <section id="promocoes" class="ofertas" aria-labelledby="titulo-promocoes">
      <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14">
        <!-- ALTERAR: título e aviso da seção de promoções -->
        <h2 id="titulo-promocoes" class="text-3xl sm:text-4xl">Promoções</h2>
        <ul class="ofertas-lista">${ofertas}</ul>
        <p class="mt-4 text-sm leading-relaxed text-crust">O site mostra o preço cheio; o desconto é aplicado pela matriz no fechamento pelo WhatsApp.</p>
      </div>
    </section>`;
}

// ---------- Filtros ----------

function mostrarFiltros() {
  // Conta quantos produtos tem em cada categoria.
  const categorias = {};
  for (const produto of site.produtos) {
    if (categorias[produto.categoria]) {
      categorias[produto.categoria] = categorias[produto.categoria] + 1;
    } else {
      categorias[produto.categoria] = 1;
    }
  }

  let html = '<fieldset class="filtro-grupo"><legend>Categoria</legend>';
  html += opcaoDeFiltro('data-filter-category-todas checked', '', 'Todas', site.produtos.length);
  for (const nome in categorias) {
    html += opcaoDeFiltro('data-filter-category', nome, nome, categorias[nome]);
  }
  html += '</fieldset>';

  // Filtro de preço: vai de R$ 1 até o maior preço por peça.
  let maiorPreco = 0;
  for (const produto of site.produtos) {
    if (produto.faixa.unitario > maiorPreco) {
      maiorPreco = produto.faixa.unitario;
    }
  }
  maiorPreco = Math.ceil(maiorPreco);
  html += `
    <div class="filtro-grupo">
      <label for="filtro-preco" class="filtro-titulo">Preço por peça</label>
      <input id="filtro-preco" type="range" class="faixa-preco" data-filter-preco min="1" max="${maiorPreco}" step="1" value="${maiorPreco}">
      <p class="filtro-dica"><span>R$ 1</span><output data-filter-preco-saida>Qualquer preço</output></p>
    </div>`;

  if (site.promocoes.length > 0) {
    html += '<fieldset class="filtro-grupo"><legend>Promoções</legend>';
    html += opcaoDeFiltro('data-filter-promo', '', 'Só itens em promoção', '');
    html += '</fieldset>';
  }

  let restricoes = '';
  for (const restricao of RESTRICOES) {
    if (algumProdutoTem(restricao)) {
      restricoes += opcaoDeFiltro('data-filter-tag', restricao, restricao, '');
    }
  }
  if (restricoes) {
    html += '<fieldset class="filtro-grupo"><legend>Restrições alimentares</legend>' + restricoes + '</fieldset>';
  }

  document.querySelector('[data-filtros-corpo]').innerHTML = html;
}

function opcaoDeFiltro(atributo, valor, texto, quantidade) {
  return `
    <label class="filtro-opcao">
      <input type="checkbox" ${atributo} value="${limpar(valor)}">
      <span class="first-letter:uppercase">${limpar(texto)}</span>
      <small>${quantidade}</small>
    </label>`;
}

function algumProdutoTem(tag) {
  for (const produto of site.produtos) {
    if (produto.tags.includes(tag)) {
      return true;
    }
  }
  return false;
}

// ---------- Produtos ----------

function mostrarProdutos() {
  let html = '';
  for (const produto of site.produtos) {
    html += cardDoProduto(produto);
  }
  document.querySelector('[data-grade-produtos]').innerHTML = html;
  document.querySelector('[data-total-texto]').textContent = site.produtos.length + ' itens feitos';
}

// Diz se um card passa em todos os filtros marcados.
function cardPassaNosFiltros(card) {
  const busca = semAcento(document.getElementById('busca').value.trim());
  if (busca && !card.dataset.busca.includes(busca)) {
    return false;
  }

  const categoriasMarcadas = [];
  for (const caixa of document.querySelectorAll('[data-filter-category]:checked')) {
    categoriasMarcadas.push(caixa.value);
  }
  if (categoriasMarcadas.length > 0 && !categoriasMarcadas.includes(card.dataset.categoria)) {
    return false;
  }

  const soPromocao = document.querySelector('[data-filter-promo]:checked');
  if (soPromocao && card.dataset.promo !== '1') {
    return false;
  }

  const tagsDoCard = card.dataset.tags.split(',');
  for (const caixa of document.querySelectorAll('[data-filter-tag]:checked')) {
    if (!tagsDoCard.includes(caixa.value)) {
      return false;
    }
  }

  const preco = document.querySelector('[data-filter-preco]');
  if (preco.value !== preco.max && Number(card.dataset.unitario) > Number(preco.value)) {
    return false;
  }

  return true;
}

function ordenarCards(cards) {
  const ordem = document.querySelector('[data-ordenar]').value;

  cards.sort(function (a, b) {
    if (ordem === 'preco-asc') {
      return a.dataset.unitario - b.dataset.unitario;
    }
    if (ordem === 'preco-desc') {
      return b.dataset.unitario - a.dataset.unitario;
    }
    if (ordem === 'nome') {
      return a.dataset.nome.localeCompare(b.dataset.nome);
    }
    // "Recomendados": os destaques primeiro, o resto na ordem do cadastro.
    if (a.dataset.destaque !== b.dataset.destaque) {
      return b.dataset.destaque - a.dataset.destaque;
    }
    return a.dataset.ordem - b.dataset.ordem;
  });
}

function filtrar() {
  const grade = document.querySelector('[data-grade-produtos]');
  const todosOsCards = Array.from(grade.querySelectorAll('[data-produto]'));

  const encontrados = [];
  for (const card of todosOsCards) {
    if (cardPassaNosFiltros(card)) {
      encontrados.push(card);
    }
  }
  ordenarCards(encontrados);

  const totalDePaginas = Math.max(1, Math.ceil(encontrados.length / PRODUTOS_POR_PAGINA));
  if (paginaAtual > totalDePaginas) {
    paginaAtual = totalDePaginas;
  }
  const primeiro = (paginaAtual - 1) * PRODUTOS_POR_PAGINA;
  const ultimo = primeiro + PRODUTOS_POR_PAGINA;

  // Esconde todos e mostra só os da página atual, na ordem escolhida.
  for (const card of todosOsCards) {
    card.classList.add('oculto');
  }
  for (let i = 0; i < encontrados.length; i++) {
    grade.append(encontrados[i]);
    if (i >= primeiro && i < ultimo) {
      encontrados[i].classList.remove('oculto');
    }
  }

  document.querySelector('[data-sem-resultado]').classList.toggle('oculto', encontrados.length > 0);

  let texto = encontrados.length + ' itens';
  if (totalDePaginas > 1) {
    texto += ', página ' + paginaAtual + ' de ' + totalDePaginas;
  }
  document.querySelector('[data-contagem]').textContent = texto;
  document.querySelector('[data-ver-itens]').textContent = 'Ver ' + encontrados.length + ' itens';

  // "Todas" fica marcada quando nenhuma categoria está marcada.
  const algumaCategoria = document.querySelector('[data-filter-category]:checked');
  document.querySelector('[data-filter-category-todas]').checked = !algumaCategoria;

  mostrarPrecoEscolhido();
  mostrarPaginas(totalDePaginas);
}

function mostrarPrecoEscolhido() {
  const preco = document.querySelector('[data-filter-preco]');
  const saida = document.querySelector('[data-filter-preco-saida]');
  if (preco.value === preco.max) {
    saida.textContent = 'Qualquer preço';
  } else {
    saida.textContent = 'Até R$ ' + preco.value;
  }
  const porcentagem = ((preco.value - preco.min) / (preco.max - preco.min)) * 100;
  preco.style.setProperty('--pct', porcentagem + '%');
}

function mostrarPaginas(totalDePaginas) {
  const paginacao = document.querySelector('[data-paginacao]');
  paginacao.hidden = totalDePaginas <= 1;

  let html = '';
  for (let numero = 1; numero <= totalDePaginas; numero++) {
    let atual = '';
    if (numero === paginaAtual) {
      atual = 'aria-current="page"';
    }
    html += `<button type="button" data-ir-para="${numero}" aria-label="Página ${numero}" ${atual}>${numero}</button>`;
  }
  paginacao.innerHTML = html;
}

function ligarFiltros() {
  // Guarda a ordem original para o "Recomendados".
  const cards = document.querySelectorAll('[data-grade-produtos] [data-produto]');
  for (let i = 0; i < cards.length; i++) {
    cards[i].dataset.ordem = i;
  }

  // Qualquer filtro mudou: volta para a primeira página e filtra.
  const filtros = document.querySelector('[data-filtros-corpo]');
  filtros.addEventListener('change', function (evento) {
    if (evento.target.matches('[data-filter-category-todas]')) {
      for (const caixa of document.querySelectorAll('[data-filter-category]')) {
        caixa.checked = false;
      }
    }
    paginaAtual = 1;
    filtrar();
  });
  filtros.addEventListener('input', function () {
    paginaAtual = 1;
    filtrar();
  });

  document.getElementById('busca').addEventListener('input', function () {
    paginaAtual = 1;
    filtrar();
  });

  document.querySelector('[data-busca-form]').addEventListener('submit', function (evento) {
    evento.preventDefault();
    document.getElementById('contagem-catalogo').scrollIntoView();
  });

  document.querySelector('[data-ordenar]').addEventListener('change', filtrar);

  document.querySelector('[data-paginacao]').addEventListener('click', function (evento) {
    const botao = evento.target.closest('[data-ir-para]');
    if (botao) {
      paginaAtual = Number(botao.dataset.irPara);
      filtrar();
      document.getElementById('contagem-catalogo').scrollIntoView();
    }
  });

  for (const botao of document.querySelectorAll('[data-limpar-filtros]')) {
    botao.addEventListener('click', limparFiltros);
  }

  // No celular, os filtros abrem por cima da tela.
  const painel = document.querySelector('[data-filtros-painel]');
  const fundo = document.querySelector('.filtros-fundo');
  document.querySelector('[data-abrir-filtros]').addEventListener('click', function () {
    painel.classList.add('aberto');
    fundo.hidden = false;
  });
  for (const botao of document.querySelectorAll('[data-fechar-filtros]')) {
    botao.addEventListener('click', function () {
      painel.classList.remove('aberto');
      fundo.hidden = true;
    });
  }
}

function limparFiltros() {
  document.getElementById('busca').value = '';
  for (const caixa of document.querySelectorAll('[data-filtros-corpo] input[type=checkbox]')) {
    caixa.checked = false;
  }
  const preco = document.querySelector('[data-filter-preco]');
  preco.value = preco.max;
  paginaAtual = 1;
  filtrar();
}

const ICONE_CALENDARIO = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3.5" y="5" width="17" height="15" rx="1.5"/><path d="M3.5 10h17M8 3v4M16 3v4"/></svg>';
const ICONE_LOCAL = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-6.5-5.6-6.5-11a6.5 6.5 0 0 1 13 0c0 5.4-6.5 11-6.5 11Z"/><circle cx="12" cy="10" r="2.3"/></svg>';
