// Busca produtos, unidades e promoções (pasta dados/) e guarda em "site".
// ALTERAR: para usar o Sanity, troque o endereço em buscarJson() pela consulta do Sanity.

// TODO (E): criar o projeto e o Studio do Sanity (npm create sanity) e publicar com sanity deploy.
// TODO (E, R): modelar no Sanity os tipos produto, unidade e promoção com os mesmos campos de dados/*.json.
// TODO (E): importar os JSON atuais para o Sanity (sanity dataset import).

let site = null;

// As páginas ficam em "pages/"; o index fica na raiz.
// RAIZ é o caminho de volta até a pasta do site.
let RAIZ = '';
if (location.pathname.includes('/pages/')) {
  RAIZ = '../';
}

function pagina(nome) {
  return RAIZ + 'pages/' + nome;
}

async function carregarDados() {
  const produtos = await buscarJson('dados/produtos.json');
  const unidades = await buscarJson('dados/unidades.json');
  const promocoes = await buscarJson('dados/promocoes.json');

  site = {
    produtos: produtos,
    unidades: unidades,
    promocoes: [],
    matriz: null,
  };

  for (const unidade of unidades) {
    if (unidade.matriz) {
      site.matriz = unidade;
    }
  }

  for (const produto of produtos) {
    prepararProduto(produto);
  }

  for (const promocao of promocoes) {
    if (promocaoAparece(promocao)) {
      site.promocoes.push(promocao);
    }
  }
}

// TODO (E): trocar este fetch pela consulta GROQ à API do Sanity e liberar o CORS (npx sanity cors add).
async function buscarJson(endereco) {
  const resposta = await fetch(RAIZ + endereco);
  return resposta.json();
}

// ---------- Produtos ----------

// TODO (R, G): levantar preços reais, pedido mínimo e prazo de cada item (hoje são valores de exemplo em dados/produtos.json).
// TODO (R, G): marcar em dados/produtos.json quais lojas vendem cada produto (campo unidades; vazio = todas).
function prepararProduto(produto) {
  // Sem lista de unidades = vendido em todas.
  if (produto.unidades.length === 0) {
    for (const unidade of site.unidades) {
      produto.unidades.push(unidade.slug);
    }
  }

  // Só dá para comprar pelo site se a matriz vende o produto.
  const matrizVende = site.matriz && produto.unidades.includes(site.matriz.slug);
  produto.disponivel = produto.disponivel && matrizVende;

  produto.faixa = calcularPreco(produto.preco);
}

// O preço vem por embalagem ("R$ 120 por 100 unidades").
// Aqui calculamos o preço de uma peça, o mínimo e de quanto em quanto aumenta.
function calcularPreco(preco) {
  const numeroNaEmbalagem = parseInt(preco.por);
  let base = 1;
  if (numeroNaEmbalagem > 0) {
    base = numeroNaEmbalagem;
  }

  let minimo = base;
  if (preco.minPedido > 0) {
    minimo = preco.minPedido;
  }

  let passo = base;
  if (preco.passo > 0) {
    passo = preco.passo;
  } else if (minimo < base) {
    passo = minimo;
  }

  const porCurto = preco.por.replace('unidades', 'un').replace('unidade', 'un');

  return {
    valor: preco.valor,
    base: base,
    unitario: preco.valor / base,
    min: minimo,
    passo: passo,
    porCurto: porCurto,
    exibicao: dinheiro(preco.valor) + ' / ' + porCurto,
  };
}

function buscarProduto(slug) {
  for (const produto of site.produtos) {
    if (produto.slug === slug) {
      return produto;
    }
  }
  return null;
}

// ---------- Promoções ----------

// TODO (LD, PW): escrever as promoções reais (semana, dezembro, julho/janeiro) em dados/promocoes.json,
// com produtos e unidades ligados e "ativa": true. Hoje as 3 estão inativas.

function hojeEmTexto() {
  const hoje = new Date();
  const mes = String(hoje.getMonth() + 1).padStart(2, '0');
  const dia = String(hoje.getDate()).padStart(2, '0');
  return hoje.getFullYear() + '-' + mes + '-' + dia;
}

function promocaoValeHoje(promocao) {
  const quando = promocao.quando;
  const hoje = new Date();

  if (quando.tipo === 'semanal') {
    return quando.dias.includes(hoje.getDay());
  }
  if (quando.tipo === 'mensal') {
    return quando.meses.includes(hoje.getMonth() + 1);
  }
  return true;
}

function promocaoAparece(promocao) {
  if (!promocao.ativa) {
    return false;
  }

  const hoje = hojeEmTexto();
  if (promocao.quando.de && hoje < promocao.quando.de) {
    return false;
  }
  if (promocao.quando.ate && hoje > promocao.quando.ate) {
    return false;
  }

  // Precisa ter pelo menos um produto vendido numa das lojas da promoção.
  promocao.produtosDaPromocao = [];
  for (const slug of promocao.produtos) {
    const produto = buscarProduto(slug);
    if (produto && vendidoEmAlgumaLoja(produto, promocao.unidades)) {
      promocao.produtosDaPromocao.push(produto);
    }
  }
  if (promocao.produtosDaPromocao.length === 0) {
    return false;
  }

  promocao.valeHoje = promocaoValeHoje(promocao);
  promocao.agenda = agendaEmTexto(promocao.quando);
  promocao.lojas = nomesDasLojas(promocao.unidades);

  // A semanal aparece a semana toda. As outras, só quando valem.
  if (promocao.quando.tipo === 'semanal') {
    return true;
  }
  return promocao.valeHoje;
}

function vendidoEmAlgumaLoja(produto, lojas) {
  for (const loja of lojas) {
    if (produto.unidades.includes(loja)) {
      return true;
    }
  }
  return false;
}

function nomesDasLojas(slugs) {
  const nomes = [];
  for (const unidade of site.unidades) {
    if (slugs.includes(unidade.slug)) {
      nomes.push(unidade.nome);
    }
  }
  return nomes.join(', ');
}

function agendaEmTexto(quando) {
  const dias = ['Todo domingo', 'Toda segunda-feira', 'Toda terça-feira', 'Toda quarta-feira',
    'Toda quinta-feira', 'Toda sexta-feira', 'Todo sábado'];
  const meses = ['', 'janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho',
    'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];

  if (quando.tipo === 'semanal') {
    const nomes = [];
    for (const dia of quando.dias) {
      nomes.push(dias[dia]);
    }
    return nomes.join(' e ');
  }

  if (quando.tipo === 'mensal') {
    const nomes = [];
    for (const mes of quando.meses) {
      nomes.push(meses[mes]);
    }
    return 'Em ' + nomes.join(' e ');
  }

  if (quando.tipo === 'periodo') {
    return 'De ' + dataCurta(quando.de) + ' até ' + dataCurta(quando.ate);
  }

  return 'Vale em qualquer pedido';
}

// "2026-12-24" vira "24/12"
function dataCurta(data) {
  if (!data) {
    return '?';
  }
  return data.slice(8, 10) + '/' + data.slice(5, 7);
}

function promocoesDoProduto(produto) {
  const lista = [];
  for (const promocao of site.promocoes) {
    if (promocao.produtosDaPromocao.includes(produto)) {
      lista.push(promocao);
    }
  }
  return lista;
}

// ---------- Ajudantes ----------

function dinheiro(valor) {
  return 'R$ ' + valor.toFixed(2).replace('.', ',');
}

// Número de WhatsApp só com dígitos, ou "" se não for um número de verdade.
function numeroWhatsapp(unidade) {
  if (!unidade || !unidade.whatsapp) {
    return '';
  }
  const numero = unidade.whatsapp.replace(/\D/g, '');
  if (numero.length < 10 || numero === '55000000000') {
    return '';
  }
  return numero;
}

function linkWhatsapp(unidade, mensagem) {
  const numero = numeroWhatsapp(unidade);
  if (numero === '') {
    return '';
  }
  return 'https://wa.me/' + numero + '?text=' + encodeURIComponent(mensagem);
}

// Troca os caracteres especiais para o texto não virar HTML.
function limpar(texto) {
  return String(texto)
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;');
}

// "Pão de Queijo" vira "pao de queijo"
function semAcento(texto) {
  return texto.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();
}
