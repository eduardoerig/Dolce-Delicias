import {defineArrayMember, defineField, defineType} from 'sanity'
import {orderRankField, orderRankOrdering} from '@sanity/orderable-document-list'
import {BasketIcon} from '@sanity/icons/Basket'

/** Os valores são os mesmos que o site procura nas tags (filtros do catálogo). */
export const RESTRICOES = [
  {title: 'Vegano', value: 'vegano'},
  {title: 'Sem lactose', value: 'sem lactose'},
  {title: 'Sem glúten', value: 'sem glúten'},
  {title: 'Sem carne', value: 'sem carne'},
]

export const product = defineType({
  name: 'product',
  title: 'Produto',
  type: 'document',
  icon: BasketIcon,
  groups: [
    {name: 'basico', title: 'Básico', default: true},
    {name: 'preco', title: 'Preço'},
    {name: 'detalhes', title: 'Detalhes'},
  ],
  fields: [
    defineField({
      name: 'nome',
      title: 'Nome do produto',
      type: 'string',
      group: 'basico',
      validation: (rule) => rule.required().error('Dê um nome ao produto.'),
    }),
    defineField({
      name: 'slug',
      title: 'Endereço da página',
      type: 'slug',
      group: 'basico',
      description: 'Depois de escrever o nome, clique em "Gerar". Evite mudar depois de publicado.',
      options: {source: 'nome'},
      validation: (rule) => rule.required().error('Clique em "Gerar" para criar o endereço.'),
    }),
    defineField({
      name: 'imagem',
      title: 'Foto',
      type: 'image',
      group: 'basico',
      description: 'Arraste a foto aqui. Use o botão de recorte para escolher o centro da imagem.',
      options: {hotspot: true},
    }),
    defineField({
      name: 'descricao',
      title: 'Descrição',
      type: 'text',
      rows: 3,
      group: 'basico',
      description: '1 ou 2 frases. Aparece no card e na página do produto.',
      validation: (rule) => [
        rule.required().error('Escreva uma descrição curta.'),
        rule.max(220).warning('Descrição longa — o card vai cortar o texto.'),
      ],
    }),
    defineField({
      name: 'categoria',
      title: 'Categoria',
      type: 'reference',
      group: 'basico',
      to: [{type: 'category'}],
      options: {disableNew: true},
      validation: (rule) => rule.required().error('Escolha a categoria.'),
    }),
    defineField({
      name: 'linha',
      title: 'Onde é vendido',
      type: 'string',
      group: 'basico',
      options: {
        list: [
          {title: 'Encomenda (cento, festas, empresas)', value: 'atacado'},
          {title: 'Balcão (unidade, na loja)', value: 'varejo'},
        ],
        layout: 'radio',
      },
      initialValue: 'atacado',
      validation: (rule) => rule.required(),
    }),
    defineField({
      name: 'disponivel',
      title: 'Disponível para venda',
      type: 'boolean',
      group: 'basico',
      description: 'Desligado: o produto continua no site, mas apagado e sem botão de pedir.',
      initialValue: true,
    }),
    defineField({
      name: 'destaque',
      title: 'Destaque ("mais pedido")',
      type: 'boolean',
      group: 'basico',
      description: 'Ligado: ganha o selo "mais pedido" e aparece na vitrine da página inicial.',
      initialValue: false,
    }),

    defineField({
      name: 'precos',
      title: 'Preço',
      type: 'array',
      group: 'preco',
      description: 'Normalmente só um preço. Adicione outro apenas se houver variações (ex.: Caixa 100 e Caixa 200).',
      of: [
        defineArrayMember({
          type: 'object',
          name: 'faixaPreco',
          title: 'Preço',
          fields: [
            defineField({
              name: 'valor',
              title: 'Valor (R$)',
              type: 'number',
              description: 'Preço da embalagem inteira. Use ponto para os centavos: 6.99',
              validation: (rule) => rule.required().min(0),
            }),
            defineField({
              name: 'por',
              title: 'Vendido por',
              type: 'string',
              description: 'Escolha ou escreva, ex.: "45 unidades".',
              options: {list: ['unidade', '100 unidades', '50 unidades', 'kg']},
              validation: (rule) => rule.required(),
            }),
            defineField({
              name: 'minPedido',
              title: 'Pedido mínimo (em peças)',
              type: 'number',
              description: 'Opcional.',
              validation: (rule) => rule.integer().min(1),
            }),
            defineField({
              name: 'rotulo',
              title: 'Nome da variação',
              type: 'string',
              description: 'Só se houver mais de um preço (ex.: "Caixa 200").',
            }),
            defineField({
              name: 'passo',
              title: 'Aumentar a quantidade de quanto em quanto',
              type: 'number',
              description: 'Opcional — o site calcula sozinho se ficar vazio.',
              validation: (rule) => rule.integer().min(1),
            }),
          ],
          preview: {
            select: {valor: 'valor', por: 'por', rotulo: 'rotulo'},
            prepare: ({valor, por, rotulo}) => ({
              title: `R$ ${Number(valor ?? 0).toFixed(2).replace('.', ',')} / ${por ?? ''}`,
              subtitle: rotulo,
            }),
          },
        }),
      ],
      validation: (rule) => rule.required().min(1).error('Informe o preço.'),
    }),

    defineField({
      name: 'sabores',
      title: 'Sabores',
      type: 'array',
      group: 'detalhes',
      description: 'Digite um sabor e aperte Enter.',
      of: [defineArrayMember({type: 'string'})],
      options: {layout: 'tags'},
    }),
    defineField({
      name: 'restricoes',
      title: 'Restrições alimentares',
      type: 'array',
      group: 'detalhes',
      description: 'Marque o que se aplica. Vira filtro no catálogo.',
      of: [defineArrayMember({type: 'string'})],
      options: {list: RESTRICOES, layout: 'grid'},
    }),
    defineField({
      name: 'tags',
      title: 'Palavras para a busca',
      type: 'array',
      group: 'detalhes',
      description: 'Opcional. Ajuda a busca do site (ex.: festa, coffee break). Digite e aperte Enter.',
      of: [defineArrayMember({type: 'string'})],
      options: {layout: 'tags'},
    }),
    defineField({
      name: 'unidades',
      title: 'Lojas que vendem',
      type: 'array',
      group: 'detalhes',
      description: 'Deixe vazio se todas as lojas vendem.',
      of: [defineArrayMember({type: 'reference', to: [{type: 'store'}], options: {disableNew: true}})],
    }),
    orderRankField({type: 'product'}),
  ],
  orderings: [orderRankOrdering],
  preview: {
    select: {
      title: 'nome',
      categoria: 'categoria.nome',
      linha: 'linha',
      media: 'imagem',
      disponivel: 'disponivel',
      valor: 'precos.0.valor',
      por: 'precos.0.por',
    },
    prepare: ({title, categoria, linha, media, disponivel, valor, por}) => ({
      title,
      subtitle: [
        categoria,
        linha === 'varejo' ? 'Balcão' : 'Encomenda',
        valor != null ? `R$ ${Number(valor).toFixed(2).replace('.', ',')} / ${por ?? ''}` : null,
        disponivel === false ? 'INDISPONÍVEL' : null,
      ]
        .filter(Boolean)
        .join(' · '),
      media,
    }),
  },
})
