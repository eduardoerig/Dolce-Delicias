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
  for (const link of document.querySelectorAll('[data-menu-link]')) {
    if (link.getAttribute('href') === paginaAtual) {
      link.setAttribute('aria-current', 'page');
      link.classList.add('font-bold');
    }
  }

  // O link "Promoções" só aparece se houver promoção.
  if (site.promocoes.length > 0) {
    for (const item of document.querySelectorAll('[data-so-com-promocoes]')) {
      item.hidden = false;
    }
  }
}

// ---------- Card de produto (usado na home e na página do produto) ----------

function cardDoProduto(produto) {
  const faixa = produto.faixa;
  const link = pagina('produto.html?slug=' + encodeURIComponent(produto.slug));
  const promocoes = promocoesDoProduto(produto);

  let foto = `<span class="cartao-sem-foto">${iconeProduto(produto.categoria, produto.nome)}</span>`;
  if (produto.imagem) {
    foto = `<img src="${RAIZ}${limpar(produto.imagem)}" alt="" loading="lazy" width="480" height="360">`;
  }

  let selo = '';
  if (promocoes.length > 0) {
    selo = `<p class="cartao-selos"><span class="selo selo-promo">${limpar(promocoes[0].selo || 'Promoção')}</span></p>`;
  }

  let botao = '<span class="cartao-indisponivel">Indisponível hoje</span>';
  if (produto.disponivel) {
    botao = `<button type="button" class="botao-mais" aria-label="Adicionar ${limpar(produto.nome)} ao pedido" ${dadosDoBotao(produto)}>
        <svg class="icone-mais" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
        <svg class="icone-feito" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 5 5 9-10"/></svg>
      </button>`;
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
      data-destaque="${produto.destaque ? '1' : '0'}">
      <a href="${link}" class="cartao-foto" tabindex="-1" aria-hidden="true">${foto}</a>
      ${selo}
      <div class="cartao-corpo">
        <h3 class="cartao-nome"><a href="${link}">${limpar(produto.nome)}</a></h3>
        <p class="cartao-descricao">${limpar(produto.descricao)}</p>
        <div class="cartao-rodape">
          <p class="cartao-preco"><strong>${dinheiro(faixa.valor)}</strong> <span>/ ${limpar(faixa.porCurto)}</span></p>
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
  for (const menu of document.querySelectorAll('details.dropdown[open]')) {
    if (!menu.contains(evento.target)) {
      menu.open = false;
    }
  }
});

document.addEventListener('keydown', function (evento) {
  if (evento.key === 'Escape') {
    for (const menu of document.querySelectorAll('details.dropdown[open]')) {
      menu.open = false;
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
  } else if (evento.target.closest('[data-cart-close]') || evento.target.closest('.drawer-overlay')) {
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

function mostrarAviso(titulo, detalhe, foto) {
  const area = document.querySelector('[data-toast-area]');

  let html = '';
  if (foto) {
    html += `<span class="aviso-foto">${foto}</span>`;
  }
  html += `<div class="aviso-texto"><p class="aviso-titulo"><span>${limpar(titulo)}</span></p>`;
  if (detalhe) {
    html += `<p class="aviso-detalhe">${limpar(detalhe)}</p>`;
  }
  html += '</div>';

  const aviso = document.createElement('div');
  aviso.className = 'aviso aviso-neutro';
  if (foto) {
    aviso.className = 'aviso aviso-marca';
  }
  aviso.innerHTML = html;
  area.append(aviso);

  // Leitor de tela também ouve o aviso.
  document.querySelector('[data-aviso-leitor]').textContent = titulo;

  setTimeout(function () {
    aviso.remove();
  }, 4000);
}

