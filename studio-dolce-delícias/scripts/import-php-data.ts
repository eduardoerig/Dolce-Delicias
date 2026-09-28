/**
 * Importa para o Sanity o conteúdo que hoje está em ../data/*.php (products, units, promocoes).
 * Rodar UMA vez, a partir desta pasta:
 *
 *   npx sanity exec scripts/import-php-data.ts --with-user-token
 *
 * Aborta se já existir qualquer produto, unidade, categoria ou promoção no dataset.
 */
import {getCliClient} from 'sanity/cli'

const client = getCliClient({apiVersion: '2026-09-28'})

const TODAS = ['matriz', 'unidade-1', 'unidade-2', 'unidade-3', 'unidade-4', 'unidade-5']

const SOBRE =
  'PREENCHER: um parágrafo sobre esta loja — o que ela faz de melhor, se tem mesa e café, estacionamento, quais escolas e empresas da região ela atende.'

const unidades = [
  {slug: 'matriz', nome: 'Matriz — PREENCHER bairro', endereco: 'PREENCHER: Rua Exemplo, 000 — Centro, Cidade/UF', horario: 'Seg a sex, 6h às 20h · Sáb e dom, 6h às 14h', matriz: true},
  {slug: 'unidade-1', nome: 'Unidade 1 — PREENCHER bairro', endereco: 'PREENCHER: Av. Exemplo, 000 — Bairro, Cidade/UF', horario: 'Seg a sáb, 6h às 20h', matriz: false},
  {slug: 'unidade-2', nome: 'Unidade 2 — PREENCHER bairro', endereco: 'PREENCHER: Rua Exemplo, 000 — Bairro, Cidade/UF', horario: 'Seg a sáb, 6h às 20h', matriz: false},
  {slug: 'unidade-3', nome: 'Unidade 3 — PREENCHER bairro', endereco: 'PREENCHER: Rua Exemplo, 000 — Bairro, Cidade/UF', horario: 'Seg a sáb, 6h às 20h', matriz: false},
  {slug: 'unidade-4', nome: 'Unidade 4 — PREENCHER bairro', endereco: 'PREENCHER: Av. Exemplo, 000 — Bairro, Cidade/UF', horario: 'Seg a sáb, 6h às 20h', matriz: false},
  {slug: 'unidade-5', nome: 'Unidade 5 — PREENCHER bairro', endereco: 'PREENCHER: Rua Exemplo, 000 — Bairro, Cidade/UF', horario: 'Seg a sáb, 6h às 20h', matriz: false},
]

type Faixa = {valor: number; por: string; minPedido?: number; passo?: number; rotulo?: string}
type Produto = {
  slug: string
  nome: string
  descricao: string
  categoria: string
  linha: 'atacado' | 'varejo'
  destaque: boolean
  precos: Faixa[]
  sabores?: string[]
  tags: string[]
}

// Mesma ordem de data/products.php — ela define a ordem das categorias e dos cards.
const produtos: Produto[] = [
  {slug: 'pao-de-queijo', nome: 'Pão de Queijo', descricao: 'Massa de polvilho com queijo curado, assado na hora. Sai do forno crocante por fora e macio por dentro.', categoria: 'Assados', linha: 'atacado', destaque: true, precos: [{valor: 120, por: '100 unidades', minPedido: 50}], tags: ['coffee break', 'sem carne']},
  {slug: 'mini-pizza', nome: 'Mini Pizza', descricao: 'Disco individual com molho de tomate caseiro e muçarela. Vai ao forno e já pode ir para a bandeja.', categoria: 'Assados', linha: 'atacado', destaque: true, precos: [{valor: 220, por: '100 unidades'}], sabores: ['Muçarela', 'Calabresa', 'Frango com catupiry'], tags: ['festa']},
  {slug: 'empadinha', nome: 'Empadinha', descricao: 'Massa amanteigada que desmancha na boca, recheio generoso até a borda.', categoria: 'Assados', linha: 'atacado', destaque: false, precos: [{valor: 220, por: '100 unidades'}], sabores: ['Frango', 'Palmito', 'Camarão'], tags: ['festa']},
  {slug: 'assadinhos', nome: 'Assadinhos', descricao: 'Mix de salgados assados em massa leve — a opção mais pedida por quem quer fugir da fritura.', categoria: 'Assados', linha: 'atacado', destaque: false, precos: [{valor: 160, por: '100 unidades'}], sabores: ['Frango', 'Carne', 'Presunto e queijo'], tags: ['escola']},
  {slug: 'mini-esfirras', nome: 'Mini Esfirras', descricao: 'Abertas, do tamanho de uma mordida, com recheio temperado na hora.', categoria: 'Assados', linha: 'atacado', destaque: false, precos: [{valor: 160, por: '100 unidades'}], sabores: ['Carne', 'Frango', 'Queijo'], tags: ['festa']},
  {slug: 'salgados-fritos', nome: 'Salgados Fritos', descricao: 'O cento clássico de festa. Você escolhe os sabores na hora da encomenda e a gente monta a bandeja.', categoria: 'Salgados', linha: 'atacado', destaque: true, precos: [{valor: 85, por: '100 unidades'}], sabores: ['Coxinha de frango', 'Bolinha de queijo', 'Pastel de carne', 'Palitinho de carne', 'Enroladinho de salsicha', 'Trouxinha de presunto e queijo', 'Quibe'], tags: ['festa', 'mais vendido']},
  {slug: 'combo-festa', nome: 'Combo Festa', descricao: '45 peças montadas: 25 salgados fritos, 10 mini pizzas e 10 empadinhas. Resolve a mesa pequena.', categoria: 'Combos', linha: 'atacado', destaque: true, precos: [{valor: 60, por: '45 unidades'}], tags: ['festa', 'combo']},
  {slug: 'mini-hamburguer', nome: 'Mini Hambúrguer', descricao: 'Pão brioche pequeno, hambúrguer artesanal e queijo derretido. Entregue montado e embalado.', categoria: 'Lanches', linha: 'atacado', destaque: false, precos: [{valor: 4.2, por: 'unidade', minPedido: 25}], tags: ['festa', 'faculdade']},
  {slug: 'mini-sanduiche-pate-frango', nome: 'Mini Sanduíche com Patê de Frango', descricao: 'Pão macio sem casca e patê de frango feito na casa. Clássico de coffee break e reunião.', categoria: 'Lanches', linha: 'atacado', destaque: false, precos: [{valor: 3.2, por: 'unidade', minPedido: 25}], tags: ['coffee break']},
  {slug: 'cookies', nome: 'Cookies', descricao: 'Casquinha crocante e miolo úmido, com gotas de chocolate meio amargo.', categoria: 'Doces', linha: 'atacado', destaque: false, precos: [{valor: 120, por: '100 unidades'}], tags: ['coffee break']},
  {slug: 'cupcakes', nome: 'Cupcakes', descricao: 'Bolinho macio com cobertura em bico. A gente ajusta a cor do chantilly ao tema da festa.', categoria: 'Doces', linha: 'atacado', destaque: false, precos: [{valor: 350, por: '100 unidades', minPedido: 30}], sabores: ['Baunilha', 'Chocolate', 'Red velvet'], tags: ['festa', 'personalizado']},
  {slug: 'coxinha-balcao', nome: 'Coxinha', descricao: 'Frango desfiado temperado, massa fina e fritura na hora do pedido.', categoria: 'Salgados', linha: 'varejo', destaque: true, precos: [{valor: 8, por: 'unidade'}], tags: ['balcão']},
  {slug: 'empadinha-balcao', nome: 'Empadinha de Frango', descricao: 'A mesma empadinha do cento, vendida quentinha na vitrine.', categoria: 'Salgados', linha: 'varejo', destaque: false, precos: [{valor: 9, por: 'unidade'}], tags: ['balcão']},
  {slug: 'fatia-de-bolo', nome: 'Fatia de Bolo', descricao: 'Fatia generosa do bolo do dia. Pergunte qual saiu hoje.', categoria: 'Doces', linha: 'varejo', destaque: false, precos: [{valor: 4, por: 'unidade'}], tags: ['balcão']},
  {slug: 'cafe-expresso', nome: 'Café Expresso', descricao: 'Grão torrado na semana, extraído na xícara pequena.', categoria: 'Bebidas', linha: 'varejo', destaque: false, precos: [{valor: 6.99, por: 'unidade'}], tags: ['balcão', 'vegano', 'sem lactose']},
  {slug: 'suco-maracuja', nome: 'Suco de Maracujá', descricao: 'Polpa batida na hora, copo de 400 ml.', categoria: 'Bebidas', linha: 'varejo', destaque: false, precos: [{valor: 8, por: 'unidade'}], tags: ['balcão', 'vegano', 'sem lactose']},
  {slug: 'strogonoff-executivo', nome: 'Strogonoff Executivo', descricao: 'Prato do dia com arroz, batata palha e salada. Servido no almoço, enquanto durar.', categoria: 'Almoço', linha: 'varejo', destaque: true, precos: [{valor: 20, por: 'unidade'}], tags: ['balcão', 'almoço']},
  {slug: 'coca-cola-lata', nome: 'Coca-Cola Lata', descricao: 'Lata de 350 ml, sempre gelada.', categoria: 'Bebidas', linha: 'varejo', destaque: false, precos: [{valor: 6, por: 'unidade'}], tags: ['balcão', 'vegano', 'sem lactose']},
]

const promocoes = [
  {slug: 'quarta-do-salgado', titulo: 'Quarta do salgado', texto: 'PREENCHER: o que entra na promoção, em quais unidades e se vale para encomenda ou só no balcão.', quando: {tipo: 'semanal', dias: [3]}},
  {slug: 'ferias-escolares', titulo: 'Combo de férias', texto: 'PREENCHER: a oferta de julho e janeiro, quando as escolas param. Ex.: combo de lanche para levar para casa.', quando: {tipo: 'mensal', meses: [7, 1]}},
  {slug: 'festas-de-fim-de-ano', titulo: 'Encomendas de fim de ano', texto: 'PREENCHER: a campanha de dezembro — ceia, confraternização de empresa, bandeja de festa.', quando: {tipo: 'mensal', meses: [12]}},
]

const slug = (current: string) => ({_type: 'slug', current})
const ref = (_ref: string, _key?: string) => ({_type: 'reference', _ref, ...(_key ? {_key} : {})})
const key = () => Math.random().toString(36).slice(2, 12)

async function main() {
  const existentes = await client.fetch<number>(
    'count(*[_type in ["product", "category", "store", "promotion"]])',
  )
  if (existentes > 0) {
    throw new Error(`O dataset já tem ${existentes} documentos desses tipos — importação cancelada.`)
  }

  const idUnidade = new Map<string, string>()
  for (const [i, u] of unidades.entries()) {
    const doc = await client.create({
      _type: 'store',
      nome: u.nome,
      slug: slug(u.slug),
      matriz: u.matriz,
      ordem: i,
      endereco: u.endereco,
      whatsapp: '55000000000',
      horario: u.horario,
      preparo: '48 horas para encomendas · balcão na hora',
      sobre: SOBRE,
      mapaUrl: 'https://maps.google.com/?q=PREENCHER',
      catalogoPdf: `/catalogos/${u.slug}.pdf`,
      canais: [],
    })
    idUnidade.set(u.slug, doc._id)
  }

  const idCategoria = new Map<string, string>()
  for (const nome of [...new Set(produtos.map((p) => p.categoria))]) {
    const doc = await client.create({_type: 'category', nome, ordem: idCategoria.size})
    idCategoria.set(nome, doc._id)
  }

  for (const [i, p] of produtos.entries()) {
    await client.create({
      _type: 'product',
      nome: p.nome,
      slug: slug(p.slug),
      descricao: p.descricao,
      categoria: ref(idCategoria.get(p.categoria)!),
      linha: p.linha,
      precos: p.precos.map((f) => ({_type: 'faixaPreco', _key: key(), ...f})),
      sabores: p.sabores ?? [],
      tags: p.tags,
      unidades: TODAS.map((s) => ref(idUnidade.get(s)!, key())),
      destaque: p.destaque,
      disponivel: true,
      ordem: i,
    })
  }

  for (const p of promocoes) {
    await client.create({
      _type: 'promotion',
      titulo: p.titulo,
      slug: slug(p.slug),
      selo: 'PREENCHER',
      texto: p.texto,
      quando: p.quando,
      ativo: true,
    })
  }

  console.log(
    `Importados: ${unidades.length} unidades, ${idCategoria.size} categorias, ${produtos.length} produtos, ${promocoes.length} promoções.`,
  )
}

main().catch((err) => {
  console.error(err)
  process.exit(1)
})
