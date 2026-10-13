// Página de um produto: pages/produto.html?slug=pao-de-queijo

iniciarPagina(function () {
  const slug = new URLSearchParams(location.search).get('slug');
  const produto = buscarProduto(slug);
  const lugar = document.querySelector('[data-pagina-produto]');

  if (!produto) {
    document.title = 'Produto não encontrado — Dolce Delícias';
    lugar.innerHTML = paginaNaoEncontrada();
    return;
  }

  document.title = produto.nome + ' — Dolce Delícias';
  lugar.innerHTML = paginaDoProduto(produto);
  ligarQuantidade(produto);
});

// ALTERAR: textos da página quando o produto não existe.
function paginaNaoEncontrada() {
  return `
    <div class="mx-auto flex min-h-[60vh] max-w-xl flex-col items-center justify-center gap-4 px-4 py-20 text-center">
      <h1 class="text-3xl">Esse item saiu do cardápio</h1>
      <p class="max-w-md leading-relaxed text-crust">Pode ter mudado de nome ou saído de linha.</p>
      <div class="mt-2 flex flex-wrap justify-center gap-3">
        <a href="../index.html#catalogo" class="botao-primario">Ver o catálogo</a>
        <a href="../index.html" class="botao-secundario">Voltar para o início</a>
      </div>
    </div>`;
}

function paginaDoProduto(produto) {
  const faixa = produto.faixa;
  const promocoes = promocoesDoProduto(produto);

  let foto = `<span class="produto-sem-foto" role="img" aria-label="Foto de ${limpar(produto.nome)} ainda não cadastrada">${iconeProduto(produto.categoria, produto.nome)}</span>`;
  if (produto.imagem) {
    foto = `<img src="../${limpar(produto.imagem)}" alt="${limpar(produto.nome)}" width="960" height="720">`;
  }

  let selo = '';
  if (promocoes.length > 0) {
    selo = `<span class="produto-foto-selo">${limpar(promocoes[0].selo || 'Promoção')}</span>`;
  }

  return `
    <div class="border-b border-linha bg-polvilho">
      <nav aria-label="Você está aqui" class="mx-auto flex max-w-7xl flex-wrap items-center gap-2 px-4 py-3 text-sm font-semibold text-crust sm:px-6 lg:px-8">
        <a href="../index.html" class="rounded hover:text-brand-escuro">Início</a>
        <span aria-hidden="true">/</span>
        <a href="../index.html?categoria=${encodeURIComponent(produto.categoria)}#catalogo" class="rounded hover:text-brand-escuro">${limpar(produto.categoria)}</a>
        <span aria-hidden="true">/</span>
        <span class="text-base-content" aria-current="page">${limpar(produto.nome)}</span>
      </nav>
    </div>

    <div class="produto-grade mx-auto max-w-7xl lg:px-8">
      <figure class="produto-foto">${foto}${selo}</figure>

      <div class="produto-info">
        <h1 class="produto-titulo">${limpar(produto.nome)}</h1>
        <p class="produto-descricao">${limpar(produto.descricao)}</p>
        ${etiquetas(produto)}
        ${blocoDePromocoes(promocoes)}
        <div class="produto-compra">
          <p class="produto-preco">
            <span class="text-lg font-bold">R$</span>
            <span class="produto-preco-valor">${faixa.valor.toFixed(2).replace('.', ',')}</span>
            <span class="text-base font-semibold text-crust">/ ${limpar(faixa.porCurto)}</span>
          </p>
          <dl class="produto-ficha">
            <div><dt>Pedido mínimo</dt><dd>${faixa.min} un</dd></div>
            <div><dt>Por peça</dt><dd>${dinheiro(faixa.unitario)}</dd></div>
          </dl>
          ${blocoDeCompra(produto)}
        </div>
        ${blocoDeSabores(produto)}
        ${blocoDeLojas(produto)}
      </div>
    </div>

    ${blocoDeRelacionados(produto)}`;
}

function etiquetas(produto) {
  const escondidas = ['atacado', 'varejo', 'balcao', 'balcão', 'encomenda'];
  let html = '';
  for (const tag of produto.tags) {
    if (!escondidas.includes(tag)) {
      html += `<li>${limpar(tag)}</li>`;
    }
  }
  if (html === '') {
    return '';
  }
  return `<ul class="produto-etiquetas first-letter:uppercase" aria-label="Etiquetas">${html}</ul>`;
}

function blocoDePromocoes(promocoes) {
  if (promocoes.length === 0) {
    return '';
  }

  let html = '';
  for (const promocao of promocoes) {
    let quando = 'Programe-se';
    let classeQuando = 'oferta-quando';
    if (promocao.valeHoje) {
      quando = 'Vale hoje';
      classeQuando = 'oferta-quando oferta-quando-hoje';
    }
    html += `
      <li class="produto-oferta">
        <p class="oferta-desconto">${limpar(promocao.selo || 'Oferta')}</p>
        <div class="min-w-0">
          <p class="flex flex-wrap items-center gap-x-3 gap-y-1">
            <strong class="text-lg leading-tight">${limpar(promocao.titulo)}</strong>
            <span class="${classeQuando}">${quando}</span>
          </p>
          <p class="mt-1 text-sm leading-relaxed">${limpar(promocao.agenda)}. Vale em: ${limpar(promocao.lojas)}.</p>
        </div>
      </li>`;
  }

  return `
    <section class="produto-ofertas">
      <ul class="grid gap-2">${html}</ul>
      <p class="mt-2 text-xs leading-relaxed text-crust">O site mostra o preço cheio; o desconto é aplicado pela matriz no fechamento pelo WhatsApp.</p>
    </section>`;
}

// ALTERAR: textos da caixa de compra.
function blocoDeCompra(produto) {
  const faixa = produto.faixa;

  if (!produto.disponivel) {
    return '<p class="mt-6 bg-polvilho p-4 text-sm font-semibold text-crust">Este item está fora do cardápio hoje. Fale com a unidade para saber quando volta.</p>';
  }

  return `
    <div class="mt-6">
      <label for="qtd" class="block text-sm font-semibold">Quantidade (peças)</label>
      <div class="mt-1.5 flex items-center justify-between gap-4">
        <div class="seletor-qtd">
          <button type="button" data-qty-menos aria-label="Diminuir quantidade">${ICONE_MENOS}</button>
          <input id="qtd" type="number" data-qty-input value="${faixa.min}" min="${faixa.min}" step="${faixa.passo}" inputmode="numeric">
          <button type="button" data-qty-mais aria-label="Aumentar quantidade">${ICONE_MAIS}</button>
        </div>
        <p class="text-right text-sm text-crust">
          Subtotal<br>
          <strong class="text-xl text-base-content sm:text-2xl" data-subtotal></strong>
        </p>
      </div>
      <p class="mt-1.5 text-xs text-crust">Mínimo de ${faixa.min} un, de ${faixa.passo} em ${faixa.passo}.</p>
    </div>
    <button type="button" class="botao-primario mt-5 min-h-13 w-full text-base" data-usa-qty ${dadosDoBotao(produto)}>
      <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
      Adicionar ao pedido
    </button>
    <p class="mt-3 text-center text-xs text-crust">Sem pagamento no site. O pedido é fechado pelo WhatsApp da matriz.</p>`;
}

function blocoDeSabores(produto) {
  if (produto.sabores.length === 0) {
    return '';
  }
  let html = '';
  for (const sabor of produto.sabores) {
    html += `<li>${limpar(sabor)}</li>`;
  }
  return `
    <section class="produto-bloco">
      <!-- ALTERAR: título e frase dos sabores -->
      <h2 class="produto-subtitulo">Sabores</h2>
      <p class="mt-1 text-sm leading-relaxed text-crust">Você escolhe a combinação na conversa com a unidade. Dá para misturar.</p>
      <ul class="produto-sabores">${html}</ul>
    </section>`;
}

function blocoDeLojas(produto) {
  let html = '';
  for (const unidade of site.unidades) {
    if (!produto.unidades.includes(unidade.slug)) {
      continue;
    }
    let observacao = '';
    if (unidade.matriz) {
      observacao = '<small>recebe os pedidos do site</small>';
    }
    html += `
      <li>
        <a href="unidades.html#${encodeURIComponent(unidade.slug)}">
          ${ICONE_LOCAL}
          <span>${limpar(unidade.nome)}${observacao}</span>
        </a>
      </li>`;
  }

  return `
    <section class="produto-bloco">
      <h2 class="produto-subtitulo">Onde encontrar</h2>
      <ul class="produto-lojas">${html}</ul>
    </section>`;
}

function blocoDeRelacionados(produto) {
  let cards = '';
  let quantos = 0;
  for (const outro of site.produtos) {
    if (outro !== produto && outro.categoria === produto.categoria && outro.disponivel && quantos < 4) {
      cards += cardDoProduto(outro);
      quantos++;
    }
  }
  if (quantos === 0) {
    return '';
  }

  return `
    <section class="border-t border-linha bg-polvilho">
      <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-12">
        <!-- ALTERAR: título dos produtos relacionados -->
        <h2 class="text-3xl sm:text-4xl">Vai bem junto</h2>
        <p class="mt-2 text-crust">Outros itens de ${limpar(produto.categoria)}.</p>
        <div class="mt-6 grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-4">${cards}</div>
      </div>
    </section>`;
}

// Botões + e − e o subtotal.
function ligarQuantidade(produto) {
  const campo = document.querySelector('[data-qty-input]');
  if (!campo) {
    return;
  }
  const faixa = produto.faixa;

  function atualizar() {
    const quantidade = ajustarQuantidade(Number(campo.value), faixa.min, faixa.passo);
    campo.value = quantidade;
    document.querySelector('[data-subtotal]').textContent = dinheiro(quantidade * faixa.unitario);
    document.querySelector('[data-qty-menos]').disabled = quantidade <= faixa.min;
  }

  document.querySelector('[data-qty-mais]').addEventListener('click', function () {
    campo.value = Number(campo.value) + faixa.passo;
    atualizar();
  });
  document.querySelector('[data-qty-menos]').addEventListener('click', function () {
    campo.value = Number(campo.value) - faixa.passo;
    atualizar();
  });
  campo.addEventListener('change', atualizar);

  atualizar();
}

const ICONE_LOCAL = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/></svg>';
