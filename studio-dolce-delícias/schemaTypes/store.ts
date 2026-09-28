import {defineArrayMember, defineField, defineType} from 'sanity'
import {orderRankField, orderRankOrdering} from '@sanity/orderable-document-list'
import {HomeIcon} from '@sanity/icons/Home'

export const store = defineType({
  name: 'store',
  title: 'Unidade',
  type: 'document',
  icon: HomeIcon,
  fields: [
    defineField({
      name: 'nome',
      title: 'Nome',
      type: 'string',
      validation: (rule) => rule.required(),
    }),
    defineField({
      name: 'slug',
      title: 'Slug',
      type: 'slug',
      description: 'Âncora em unidades.php e nome do PDF do catálogo (ex.: unidade-1).',
      options: {source: 'nome'},
      validation: (rule) => rule.required(),
    }),
    defineField({
      name: 'matriz',
      title: 'É a matriz?',
      type: 'boolean',
      description: 'Pedidos do site vão para a matriz. Exatamente uma unidade deve ser a matriz.',
      initialValue: false,
    }),
    defineField({name: 'endereco', title: 'Endereço', type: 'string'}),
    defineField({
      name: 'whatsapp',
      title: 'WhatsApp',
      type: 'string',
      description: 'Só dígitos: 55 + DDD + número (ex.: 5511987654321).',
      validation: (rule) =>
        rule.regex(/^55\d{10,11}$/, {name: 'telefone'}).warning('Use 55 + DDD + número, só dígitos.'),
    }),
    defineField({
      name: 'horario',
      title: 'Horário de funcionamento',
      type: 'string',
    }),
    defineField({
      name: 'preparo',
      title: 'Tempo de preparo',
      type: 'string',
      description: 'Tempo mínimo para o pedido ficar pronto. Vazio esconde a linha.',
    }),
    defineField({name: 'sobre', title: 'Sobre a loja', type: 'text', rows: 4}),
    defineField({name: 'mapaUrl', title: 'Link do Google Maps', type: 'url'}),
    defineField({
      name: 'imagem',
      title: 'Foto da fachada',
      type: 'image',
      options: {hotspot: true},
      fields: [defineField({name: 'alt', title: 'Texto alternativo', type: 'string'})],
    }),
    defineField({
      name: 'catalogoPdf',
      title: 'Caminho do catálogo em PDF',
      type: 'string',
      description: 'Arquivo dentro de /catalogos/ no site (ex.: /catalogos/unidade-1.pdf).',
    }),
    defineField({
      name: 'avaliacao',
      title: 'Link de avaliação',
      type: 'url',
      description: 'Onde o cliente avalia o atendimento desta loja (ex.: Google Maps).',
    }),
    defineField({
      name: 'canais',
      title: 'Outros canais de venda',
      type: 'array',
      of: [
        defineArrayMember({
          type: 'object',
          name: 'canal',
          fields: [
            defineField({
              name: 'nome',
              title: 'Nome',
              type: 'string',
              validation: (rule) => rule.required(),
            }),
            defineField({
              name: 'url',
              title: 'Link',
              type: 'url',
              validation: (rule) => rule.required(),
            }),
          ],
        }),
      ],
    }),
    orderRankField({type: 'store'}),
  ],
  orderings: [orderRankOrdering],
  preview: {
    select: {title: 'nome', subtitle: 'endereco', media: 'imagem', matriz: 'matriz'},
    prepare: ({title, subtitle, media, matriz}) => ({
      title: matriz ? `${title} (matriz)` : title,
      subtitle,
      media,
    }),
  },
})
