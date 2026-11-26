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
  const {produtos, unidades, promocoes} = await buscarCatalogo();

  site = {
    produtos: produtos,
    unidades: unidades,
    promocoes: [],
    matriz: null,
  };

  for (let i = 0; i < unidades.length; i++) {
    if (unidades[i].matriz) {
      site.matriz = unidades[i];
    }
  }

  for (let i = 0; i < produtos.length; i++) {
    prepararProduto(produtos[i]);
  }

  for (let i = 0; i < promocoes.length; i++) {
    if (promocaoAparece(promocoes[i])) {
      site.promocoes.push(promocoes[i]);
    }
  }
}

// A leitura pública não precisa de token. Nunca coloque tokens privados neste arquivo.
//
// A consulta passa pelo próprio domínio (/sanity/...), e a Vercel repassa para a API
// do Sanity (ver "rewrites" em vercel.json). Assim o navegador não faz requisição
// entre domínios e o site não depende da lista de origens liberadas no painel do
// Sanity — inclusive nos endereços de prévia da Vercel, que mudam a cada deploy.
const SANITY = {projectId:'wdisx8zs', dataset:'production', apiVersion:'2025-02-19'};
function consultaSanity(parametros) {
  return RAIZ + 'sanity/v' + SANITY.apiVersion + '/data/query/' + SANITY.dataset + '?' + parametros;
}
// Reserva: fala direto com a API (só funciona se este domínio estiver liberado em
// Sanity > API > CORS origins). Serve para o site não depender de uma coisa só.
function consultaSanityDireta(parametros) {
  return 'https://' + SANITY.projectId + '.api.sanity.io/v' + SANITY.apiVersion +
    '/data/query/' + SANITY.dataset + '?' + parametros;
}
async function pedirSanity(parametros) {
  const urls = [consultaSanity(parametros), consultaSanityDireta(parametros)];
  for (let i = 0; i < urls.length; i++) {
    try {
      const resposta = await fetch(urls[i], {signal: AbortSignal.timeout(12000)});
      if (resposta.ok) return resposta;
    } catch (erro) {
      // tenta o próximo endereço
    }
  }
  throw new Error('Falha ao consultar Sanity');
}
async function buscarCatalogo() {
  const imagem = '"imagem": coalesce(foto.asset->url, imagemLocal)';
  const consulta = `{
    "produtos": *[_type == "produto"] | order(ordem asc, _createdAt asc){
      ..., "slug": slug.current, "categoria": categoria->nome, ${imagem},
      "tags": coalesce(tags, []), "sabores": coalesce(sabores, []),
      "unidades": coalesce(unidades[]->slug.current, [])
    },
    "unidades": *[_type == "unidade"] | order(ordem asc, _createdAt asc){
      ..., "slug": slug.current, ${imagem}, "canais": coalesce(canais, []),
      "catalogoPdf": coalesce(catalogo.asset->url, catalogoPdf)
    },
    "promocoes": *[_type == "promocao"] | order(ordem asc, _createdAt asc){
      ..., "slug": slug.current, "produtos": coalesce(produtos[]->slug.current, []),
      "unidades": coalesce(unidades[]->slug.current, []),
      "quando": {"tipo": coalesce(quando.tipo, "sempre"), "dias": coalesce(quando.dias, []),
        "meses": coalesce(quando.meses, []), "de": quando.de, "ate": quando.ate}
    }
  }`;
  const resposta = await pedirSanity('perspective=published&query=' + encodeURIComponent(consulta));
  if (!resposta.ok) throw new Error('Falha ao consultar Sanity: ' + resposta.status);
  const dados = await resposta.json();
  if (dados.error || !dados.result) throw new Error('Resposta inválida do Sanity');
  // Projeto novo: usa o catálogo original até a primeira importação.
  // Após importar, um catálogo vazio no Sanity continua vazio (não ressuscita produtos excluídos).
  if (!dados.result.unidades.length && !dados.result.produtos.length) {
    const marca = await pedirSanity('query=' + encodeURIComponent('*[_id == "catalogo-importado"][0]._id'));
    if (!marca.ok) throw new Error('Falha ao verificar importação');
    if (!(await marca.json()).result) {
      console.info('Sanity ainda vazio: exibindo o catálogo local. Execute a importação.');
      const nomes = ['produtos', 'unidades', 'promocoes'];
      const pedidos = [];
      for (let i = 0; i < nomes.length; i++) {
        pedidos.push(buscarJson(nomes[i]));
      }
      const [produtos, unidades, promocoes] = await Promise.all(pedidos);
      return {produtos,unidades,promocoes};
    }
  }
  return dados.result;
}
async function buscarJson(nome) {
  const resposta = await fetch(RAIZ + 'dados/' + nome + '.json');
  if (!resposta.ok) throw new Error('Falha ao carregar ' + nome);
  return resposta.json();
}
function caminhoArquivo(valor) {
  if (!valor) return '';
  if (/^https:\/\//i.test(valor)) return valor;
  if (/^[a-z][a-z0-9+.-]*:/i.test(valor) || valor.startsWith('//')) return '';
  return RAIZ + valor;
}

// Link de um PDF. O enviado pelo Sanity fica em outro domínio, onde o navegador
// ignora o "download" e abre o arquivo; o ?dl= pede ao Sanity para baixar.
function linkDoPdf(valor) {
  const arquivo = caminhoArquivo(valor);
  if (arquivo.startsWith('https://cdn.sanity.io/')) {
    return arquivo + '?dl=';
  }
  return arquivo;
}

// O catálogo completo é o PDF enviado na matriz; sem envio, o arquivo da pasta.
function pdfCompleto() {
  if (site.matriz && /^https:\/\//i.test(site.matriz.catalogoPdf || '')) {
    return linkDoPdf(site.matriz.catalogoPdf);
  }
  return RAIZ + 'catalogos/completo.pdf';
}

// ---------- Produtos ----------

// TODO (R, G): levantar preços reais, pedido mínimo e prazo de cada item (hoje são valores de exemplo em dados/produtos.json).
// TODO (R, G): marcar em dados/produtos.json quais lojas vendem cada produto (campo unidades; vazio = todas).
function prepararProduto(produto) {
  // Sem lista de unidades = vendido em todas.
  if (produto.unidades.length === 0) {
    for (let i = 0; i < site.unidades.length; i++) {
      produto.unidades.push(site.unidades[i].slug);
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
  for (let i = 0; i < site.produtos.length; i++) {
    if (site.produtos[i].slug === slug) {
      return site.produtos[i];
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

  // Sem lista de unidades = vale em todas.
  if (promocao.unidades.length === 0) {
    for (let i = 0; i < site.unidades.length; i++) {
      promocao.unidades.push(site.unidades[i].slug);
    }
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
  for (let i = 0; i < promocao.produtos.length; i++) {
    const produto = buscarProduto(promocao.produtos[i]);
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
  for (let i = 0; i < lojas.length; i++) {
    if (produto.unidades.includes(lojas[i])) {
      return true;
    }
  }
  return false;
}

function nomesDasLojas(slugs) {
  const nomes = [];
  for (let i = 0; i < site.unidades.length; i++) {
    if (slugs.includes(site.unidades[i].slug)) {
      nomes.push(site.unidades[i].nome);
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
    for (let i = 0; i < quando.dias.length; i++) {
      nomes.push(dias[quando.dias[i]]);
    }
    return nomes.join(' e ');
  }

  if (quando.tipo === 'mensal') {
    const nomes = [];
    for (let i = 0; i < quando.meses.length; i++) {
      nomes.push(meses[quando.meses[i]]);
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
  for (let i = 0; i < site.promocoes.length; i++) {
    if (site.promocoes[i].produtosDaPromocao.includes(produto)) {
      lista.push(site.promocoes[i]);
    }
  }
  return lista;
}

// Desconto só vale se a promoção vale hoje na matriz (onde o pedido é feito).
// Entre várias, ganha a de maior desconto.
function melhorPromocao(slug) {
  if (!site || !site.matriz) {
    return null;
  }
  let melhor = null;
  for (let i = 0; i < site.promocoes.length; i++) {
    const promocao = site.promocoes[i];
    if (!promocao.valeHoje || !(promocao.desconto > 0) || !promocao.unidades.includes(site.matriz.slug)) {
      continue;
    }
    for (let j = 0; j < promocao.produtosDaPromocao.length; j++) {
      if (promocao.produtosDaPromocao[j].slug === slug && (!melhor || promocao.desconto > melhor.desconto)) {
        melhor = promocao;
      }
    }
  }
  return melhor;
}

function valorComDesconto(valor, desconto) {
  return Math.round(valor * (100 - desconto)) / 100;
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
