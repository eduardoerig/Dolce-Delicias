// Carrinho: guardado no navegador (localStorage).
// O pedido é fechado abrindo o WhatsApp da matriz com a mensagem pronta.

function lerCarrinho() {
  const texto = localStorage.getItem("dolce_cart");
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
  localStorage.setItem("dolce_cart", JSON.stringify(itens));
  mostrarCarrinho();
}

// A quantidade nunca fica abaixo do mínimo e anda de "passo" em "passo".
function ajustarQuantidade(quantidade, minimo, passo) {
  if (quantidade <= minimo) {
    return minimo;
  }
  const passos = Math.round((quantidade - minimo) / passo);
  return minimo + passos * passo;
}

function adicionarAoCarrinho(novo) {
  const itens = lerCarrinho();
  let achou = false;

  for (let i = 0; i < itens.length; i++) {
    if (itens[i].id === novo.id) {
      itens[i].qtd = ajustarQuantidade(
        itens[i].qtd + novo.qtd,
        itens[i].min,
        itens[i].passo,
      );
      achou = true;
    }
  }

  if (achou === false) {
    novo.qtd = ajustarQuantidade(novo.qtd, novo.min, novo.passo);
    itens.push(novo);
  }

  salvarCarrinho(itens);
  mostrarAviso(novo.nome, novo.qtd + " un no pedido", fotoDoItem(novo));
}

function mudarQuantidade(id, quantidade) {
  const itens = lerCarrinho();
  for (let i = 0; i < itens.length; i++) {
    if (itens[i].id === id) {
      itens[i].qtd = ajustarQuantidade(
        quantidade,
        itens[i].min,
        itens[i].passo,
      );
    }
  }
  salvarCarrinho(itens);
}

function removerDoCarrinho(id) {
  const itens = lerCarrinho();
  const restantes = [];
  for (let i = 0; i < itens.length; i++) {
    if (itens[i].id !== id) {
      restantes.push(itens[i]);
    }
  }
  salvarCarrinho(restantes);
  mostrarAviso("Item removido do pedido.");
}

// O desconto vale sobre o subtotal da linha (preço x quantidade) e é calculado na hora, nunca guardado.
function valoresDoItem(item) {
  const cheio = item.preco * item.qtd;
  const promocao = melhorPromocao(item.slug);
  if (promocao === null) {
    return { cheio: cheio, final: cheio, promocao: null };
  }
  return {
    cheio: cheio,
    final: valorComDesconto(cheio, promocao.desconto),
    promocao: promocao,
  };
}

function resumoDoCarrinho(itens) {
  let subtotal = 0;
  let total = 0;
  for (let i = 0; i < itens.length; i++) {
    const valores = valoresDoItem(itens[i]);
    subtotal = subtotal + valores.cheio;
    total = total + valores.final;
  }
  return {
    subtotal: Math.round(subtotal * 100) / 100,
    desconto: Math.round((subtotal - total) * 100) / 100,
    total: Math.round(total * 100) / 100,
  };
}

// ---------- Desenho do carrinho (gaveta lateral e página carrinho.html) ----------

function fotoDoItem(item) {
  if (item.imagem) {
    return `<img src="${limpar(caminhoArquivo(item.imagem))}" alt="">`;
  }
  return `<span class="item-pedido-sem-foto">${iconeProduto(item.categoria, item.nome)}</span>`;
}

function linhaDoCarrinho(item) {
  const link = pagina("produto.html?slug=" + encodeURIComponent(item.slug));
  const nome = limpar(item.nome);

  const valores = valoresDoItem(item);
  let subtotal = dinheiro(valores.cheio);
  let promo = "";
  if (valores.promocao !== null) {
    subtotal = `<s class="item-pedido-cheio">${dinheiro(valores.cheio)}</s> ${dinheiro(valores.final)}`;
    promo = `
          <p class="item-pedido-promo">${limpar(valores.promocao.titulo)} −${valores.promocao.desconto}% · válido na ${limpar(site.matriz.nome)}</p>`;
  }

  let menosDesligado = "";
  if (item.qtd <= item.min) {
    menosDesligado = "disabled";
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
          <span class="item-pedido-subtotal">${subtotal}</span>
        </div>${promo}
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

  let html = "";
  for (let i = 0; i < itens.length; i++) {
    html = html + linhaDoCarrinho(itens[i]);
  }

  let contagem = itens.length + " itens";
  if (itens.length === 1) {
    contagem = "1 item";
  }

  const listas = document.querySelectorAll("[data-cart-items]");
  for (let i = 0; i < listas.length; i++) {
    listas[i].innerHTML = html;
    esconder(listas[i], vazio);
  }
  const vazios = document.querySelectorAll("[data-cart-empty]");
  for (let i = 0; i < vazios.length; i++) {
    esconder(vazios[i], !vazio);
  }
  const rodapes = document.querySelectorAll("[data-cart-footer]");
  for (let i = 0; i < rodapes.length; i++) {
    esconder(rodapes[i], vazio);
  }
  const resumo = resumoDoCarrinho(itens);
  const totais = document.querySelectorAll("[data-cart-total]");
  for (let i = 0; i < totais.length; i++) {
    totais[i].textContent = dinheiro(resumo.total);
  }
  const subtotais = document.querySelectorAll("[data-cart-subtotal]");
  for (let i = 0; i < subtotais.length; i++) {
    subtotais[i].textContent = dinheiro(resumo.subtotal);
  }
  const descontos = document.querySelectorAll("[data-cart-desconto]");
  for (let i = 0; i < descontos.length; i++) {
    descontos[i].textContent = "− " + dinheiro(resumo.desconto);
  }
  const linhasDesconto = document.querySelectorAll("[data-cart-linha-desconto]");
  for (let i = 0; i < linhasDesconto.length; i++) {
    esconder(linhasDesconto[i], resumo.desconto === 0);
  }
  const bolinhas = document.querySelectorAll("[data-cart-badge]");
  for (let i = 0; i < bolinhas.length; i++) {
    bolinhas[i].textContent = itens.length;
    esconder(bolinhas[i], vazio);
  }
  const contagens = document.querySelectorAll("[data-cart-count]");
  for (let i = 0; i < contagens.length; i++) {
    contagens[i].textContent = contagem;
  }
  const leitores = document.querySelectorAll("[data-cart-sr]");
  for (let i = 0; i < leitores.length; i++) {
    leitores[i].textContent = "Carrinho com " + contagem;
  }
}

// Esconde o elemento quando "esconde" for verdadeiro; senão, mostra.
function esconder(elemento, esconde) {
  if (esconde) {
    elemento.classList.add("oculto");
  } else {
    elemento.classList.remove("oculto");
  }
}

// ---------- Fechar o pedido no WhatsApp ----------

// ALTERAR: o texto da mensagem que chega no WhatsApp da matriz.
// TODO (LD, LH, PW): testar o pedido completo até o WhatsApp em celulares Android e iOS reais.
function mensagemDoPedido(itens, endereco, pagamento, observacao) {
  const linhas = [];
  linhas.push("Olá! Quero fazer uma encomenda pelo site da Dolce Delícias.");
  linhas.push("");
  linhas.push("Unidade: " + site.matriz.nome);
  linhas.push("Entrega: " + (endereco || "endereço a combinar na conversa"));
  linhas.push("");

  for (let i = 0; i < itens.length; i++) {
    const valores = valoresDoItem(itens[i]);
    let preco = dinheiro(valores.cheio);
    if (valores.promocao !== null) {
      preco =
        "de " +
        dinheiro(valores.cheio) +
        " por " +
        dinheiro(valores.final) +
        " (" +
        valores.promocao.titulo +
        " −" +
        valores.promocao.desconto +
        "%)";
    }
    linhas.push(
      "• " +
        itens[i].qtd +
        "x " +
        itens[i].nome +
        " (" +
        itens[i].por +
        ") — " +
        preco,
    );
  }

  const resumo = resumoDoCarrinho(itens);
  linhas.push("");
  if (resumo.desconto > 0) {
    linhas.push("Descontos: − " + dinheiro(resumo.desconto));
  }
  linhas.push("Total estimado: " + dinheiro(resumo.total));
  linhas.push("Forma de pagamento: " + pagamento);
  if (resumo.desconto > 0) {
    linhas.push(
      "Desconto de promoção calculado pelo site; a matriz confirma no fechamento.",
    );
  } else {
    linhas.push(
      "Preços cheios. Promoções confirmadas pela matriz no fechamento.",
    );
  }

  if (site.matriz.preparo) {
    linhas.push("Preparo mínimo: " + site.matriz.preparo);
  }
  if (observacao) {
    linhas.push("");
    linhas.push("Observação: " + observacao);
  }

  return linhas.join("\n");
}

function fecharPedido() {
  const itens = lerCarrinho();
  if (itens.length === 0) {
    // ALTERAR: avisos que aparecem no canto da tela.
    mostrarAviso("Adicione pelo menos um item antes de fechar o pedido.");
    return;
  }

  const numero = numeroWhatsapp(site.matriz);
  if (numero === "") {
    mostrarAviso("A matriz ainda não tem WhatsApp cadastrado.");
    return;
  }

  const campoEndereco = document.querySelector("[data-pedido-endereco]");
  let endereco = "";
  if (campoEndereco) {
    endereco = campoEndereco.value.trim();
    if (endereco === "") {
      mostrarAviso("Informe o endereço da entrega.");
      campoEndereco.focus();
      return;
    }
  }

  let pagamento = "A combinar no WhatsApp";
  const pagamentoMarcado = document.querySelector(
    "[data-pedido-pagamento]:checked",
  );
  if (pagamentoMarcado) {
    pagamento = pagamentoMarcado.value;
  }

  let observacao = "";
  const campoObservacao = document.querySelector("[data-cart-obs]");
  if (campoObservacao) {
    observacao = campoObservacao.value.trim();
  }

  const mensagem = mensagemDoPedido(itens, endereco, pagamento, observacao);
  window.open(
    "https://wa.me/" + numero + "?text=" + encodeURIComponent(mensagem),
    "_blank",
  );
}

// ---------- Cliques ----------

document.addEventListener("click", function (evento) {
  const clicado = evento.target;

  const botaoAdicionar = clicado.closest("[data-add]");
  if (botaoAdicionar) {
    const dados = botaoAdicionar.dataset;
    let quantidade = Number(dados.min);

    // Na página do produto, usa a quantidade escolhida.
    const campoQuantidade = document.querySelector("[data-qty-input]");
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

  const linha = clicado.closest("[data-item]");
  if (linha) {
    const id = linha.dataset.item;
    const campo = linha.querySelector("[data-item-qtd]");
    const atual = Number(campo.value);
    const passo = Number(campo.step);

    if (clicado.closest("[data-item-remove]")) {
      removerDoCarrinho(id);
    } else if (clicado.closest("[data-item-mais]")) {
      mudarQuantidade(id, atual + passo);
    } else if (clicado.closest("[data-item-menos]")) {
      mudarQuantidade(id, atual - passo);
    }
    return;
  }

  if (clicado.closest("[data-cart-checkout]")) {
    fecharPedido();
  }

  if (clicado.closest("[data-cart-clear]")) {
    salvarCarrinho([]);
    mostrarAviso("Pedido esvaziado.");
  }
});

// Digitou a quantidade direto no campo.
document.addEventListener("change", function (evento) {
  if (evento.target.matches("[data-item-qtd]")) {
    const linha = evento.target.closest("[data-item]");
    mudarQuantidade(linha.dataset.item, Number(evento.target.value));
  }
});

const ICONE_MENOS =
  '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14"/></svg>';
const ICONE_MAIS =
  '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>';
const ICONE_LIXEIRA =
  '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16"/><path d="M10 11v6M14 11v6"/><path d="M6 7l1 13h10l1-13"/><path d="M9 7V4h6v3"/></svg>';
