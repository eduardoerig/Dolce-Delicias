// Carrinho: guardado no navegador (localStorage).
// O pedido é fechado abrindo o WhatsApp da matriz com a mensagem pronta.

function lerCarrinho() {
  const texto = localStorage.getItem('dolce_cart');
  if (!texto) {
    return [];
  }
  try {
    return JSON.parse(texto);
  } catch (erro) {
    return [];
  }
}

function salvarCarrinho(itens) {
  localStorage.setItem('dolce_cart', JSON.stringify(itens));
  mostrarCarrinho();
}

// A quantidade nunca fica abaixo do mínimo e anda de "passo" em "passo".
function ajustarQuantidade(quantidade, minimo, passo) {
  if (!(quantidade > minimo)) {
    return minimo;
  }
  const passos = Math.round((quantidade - minimo) / passo);
  return minimo + passos * passo;
}

function adicionarAoCarrinho(novo) {
  const itens = lerCarrinho();
  let achou = false;

  for (const item of itens) {
    if (item.id === novo.id) {
      item.qtd = ajustarQuantidade(item.qtd + novo.qtd, item.min, item.passo);
      achou = true;
    }
  }

  if (!achou) {
    novo.qtd = ajustarQuantidade(novo.qtd, novo.min, novo.passo);
    itens.push(novo);
  }

  salvarCarrinho(itens);
  mostrarAviso(novo.nome, novo.qtd + ' un no pedido', fotoDoItem(novo));
}

function mudarQuantidade(id, quantidade) {
  const itens = lerCarrinho();
  for (const item of itens) {
    if (item.id === id) {
      item.qtd = ajustarQuantidade(quantidade, item.min, item.passo);
    }
  }
  salvarCarrinho(itens);
}

function removerDoCarrinho(id) {
  const itens = lerCarrinho();
  const restantes = [];
  for (const item of itens) {
    if (item.id !== id) {
      restantes.push(item);
    }
  }
  salvarCarrinho(restantes);
  mostrarAviso('Item removido do pedido.');
}

function totalDoCarrinho(itens) {
  let total = 0;
  for (const item of itens) {
    total = total + item.preco * item.qtd;
  }
  return total;
}

// ---------- Desenho do carrinho (gaveta lateral e página carrinho.html) ----------

function fotoDoItem(item) {
  if (item.imagem) {
    return `<img src="${RAIZ}${limpar(item.imagem)}" alt="">`;
  }
  return `<span class="item-pedido-sem-foto">${iconeProduto(item.categoria, item.nome)}</span>`;
}

function linhaDoCarrinho(item) {
  const link = pagina('produto.html?slug=' + encodeURIComponent(item.slug));
  const nome = limpar(item.nome);

  let menosDesligado = '';
  if (item.qtd <= item.min) {
    menosDesligado = 'disabled';
  }

  return `
    <li class="item-pedido" data-item="${limpar(item.id)}">
      <a href="${link}" tabindex="-1" aria-hidden="true" class="item-pedido-foto">${fotoDoItem(item)}</a>
      <div class="item-pedido-corpo">
        <div class="item-pedido-topo">
          <p class="item-pedido-nome">
            <a href="${link}">${nome}</a>
            <span>${limpar(item.por)}</span>
          </p>
          <span class="item-pedido-subtotal">${dinheiro(item.preco * item.qtd)}</span>
        </div>
        <div class="item-pedido-acoes">
          <div class="seletor-qtd seletor-qtd-sm">
            <button type="button" data-item-menos aria-label="Diminuir ${nome}" ${menosDesligado}>${ICONE_MENOS}</button>
            <input type="number" data-item-qtd inputmode="numeric" value="${item.qtd}" min="${item.min}" step="${item.passo}" aria-label="Quantidade de ${nome}">
            <button type="button" data-item-mais aria-label="Aumentar ${nome}">${ICONE_MAIS}</button>
          </div>
          <button type="button" data-item-remove class="item-pedido-remover" aria-label="Remover ${nome}">${ICONE_LIXEIRA}<span>Remover</span></button>
        </div>
      </div>
    </li>`;
}

function mostrarCarrinho() {
  const itens = lerCarrinho();
  const vazio = itens.length === 0;

  let html = '';
  for (const item of itens) {
    html = html + linhaDoCarrinho(item);
  }

  let contagem = itens.length + ' itens';
  if (itens.length === 1) {
    contagem = '1 item';
  }

  for (const lista of document.querySelectorAll('[data-cart-items]')) {
    lista.innerHTML = html;
    esconder(lista, vazio);
  }
  for (const bloco of document.querySelectorAll('[data-cart-empty]')) {
    esconder(bloco, !vazio);
  }
  for (const bloco of document.querySelectorAll('[data-cart-footer]')) {
    esconder(bloco, vazio);
  }
  for (const total of document.querySelectorAll('[data-cart-total]')) {
    total.textContent = dinheiro(totalDoCarrinho(itens));
  }
  for (const bolinha of document.querySelectorAll('[data-cart-badge]')) {
    bolinha.textContent = itens.length;
    esconder(bolinha, vazio);
  }
  for (const texto of document.querySelectorAll('[data-cart-count]')) {
    texto.textContent = contagem;
  }
  for (const texto of document.querySelectorAll('[data-cart-sr]')) {
    texto.textContent = 'Carrinho com ' + contagem;
  }
}

// Esconde o elemento quando "esconde" for verdadeiro; senão, mostra.
function esconder(elemento, esconde) {
  if (esconde) {
    elemento.classList.add('oculto');
  } else {
    elemento.classList.remove('oculto');
  }
}

// ---------- Fechar o pedido no WhatsApp ----------

// ALTERAR: o texto da mensagem que chega no WhatsApp da matriz.
// TODO (LD, LH, PW): testar o pedido completo até o WhatsApp em celulares Android e iOS reais.
function mensagemDoPedido(itens, endereco, pagamento, observacao) {
  const linhas = [];
  linhas.push('Olá! Quero fazer uma encomenda pelo site da Dolce Delícias.');
  linhas.push('');
  linhas.push('Unidade: ' + site.matriz.nome);
  linhas.push('Entrega: ' + (endereco || 'endereço a combinar na conversa'));
  linhas.push('');

  for (const item of itens) {
    linhas.push('• ' + item.qtd + 'x ' + item.nome + ' (' + item.por + ') — ' + dinheiro(item.preco * item.qtd));
  }

  linhas.push('');
  linhas.push('Total estimado: ' + dinheiro(totalDoCarrinho(itens)));
  linhas.push('Forma de pagamento: ' + pagamento);
  linhas.push('Preços cheios. Promoções confirmadas pela matriz no fechamento.');

  if (site.matriz.preparo) {
    linhas.push('Preparo mínimo: ' + site.matriz.preparo);
  }
  if (observacao) {
    linhas.push('');
    linhas.push('Observação: ' + observacao);
  }

  return linhas.join('\n');
}

function fecharPedido() {
  const itens = lerCarrinho();
  if (itens.length === 0) {
    // ALTERAR: avisos que aparecem no canto da tela.
    mostrarAviso('Adicione pelo menos um item antes de fechar o pedido.');
    return;
  }

  const numero = numeroWhatsapp(site.matriz);
  if (numero === '') {
    mostrarAviso('A matriz ainda não tem WhatsApp cadastrado.');
    return;
  }

  const campoEndereco = document.querySelector('[data-pedido-endereco]');
  let endereco = '';
  if (campoEndereco) {
    endereco = campoEndereco.value.trim();
    if (endereco === '') {
      mostrarAviso('Informe o endereço da entrega.');
      campoEndereco.focus();
      return;
    }
  }

  let pagamento = 'A combinar no WhatsApp';
  const pagamentoMarcado = document.querySelector('[data-pedido-pagamento]:checked');
  if (pagamentoMarcado) {
    pagamento = pagamentoMarcado.value;
  }

  let observacao = '';
  const campoObservacao = document.querySelector('[data-cart-obs]');
  if (campoObservacao) {
    observacao = campoObservacao.value.trim();
  }

  const mensagem = mensagemDoPedido(itens, endereco, pagamento, observacao);
  window.open('https://wa.me/' + numero + '?text=' + encodeURIComponent(mensagem), '_blank');
}

// ---------- Cliques ----------

document.addEventListener('click', function (evento) {
  const clicado = evento.target;

  const botaoAdicionar = clicado.closest('[data-add]');
  if (botaoAdicionar) {
    const dados = botaoAdicionar.dataset;
    let quantidade = Number(dados.min);

    // Na página do produto, usa a quantidade escolhida.
    const campoQuantidade = document.querySelector('[data-qty-input]');
    if (dados.usaQty !== undefined && campoQuantidade) {
      quantidade = Number(campoQuantidade.value);
    }

    adicionarAoCarrinho({
      id: dados.slug,
      slug: dados.slug,
      nome: dados.nome,
      preco: Number(dados.preco),
      por: dados.por,
      qtd: quantidade,
      min: Number(dados.min),
      passo: Number(dados.passo),
      imagem: dados.imagem,
      categoria: dados.categoria,
    });
    return;
  }

  const linha = clicado.closest('[data-item]');
  if (linha) {
    const id = linha.dataset.item;
    const campo = linha.querySelector('[data-item-qtd]');
    const atual = Number(campo.value);
    const passo = Number(campo.step);

    if (clicado.closest('[data-item-remove]')) {
      removerDoCarrinho(id);
    } else if (clicado.closest('[data-item-mais]')) {
      mudarQuantidade(id, atual + passo);
    } else if (clicado.closest('[data-item-menos]')) {
      mudarQuantidade(id, atual - passo);
    }
    return;
  }

  if (clicado.closest('[data-cart-checkout]')) {
    fecharPedido();
  }

  if (clicado.closest('[data-cart-clear]')) {
    salvarCarrinho([]);
    mostrarAviso('Pedido esvaziado.');
  }
});

// Digitou a quantidade direto no campo.
document.addEventListener('change', function (evento) {
  if (evento.target.matches('[data-item-qtd]')) {
    const linha = evento.target.closest('[data-item]');
    mudarQuantidade(linha.dataset.item, Number(evento.target.value));
  }
});

const ICONE_MENOS = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14"/></svg>';
const ICONE_MAIS = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>';
const ICONE_LIXEIRA = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16"/><path d="M10 11v6M14 11v6"/><path d="M6 7l1 13h10l1-13"/><path d="M9 7V4h6v3"/></svg>';
