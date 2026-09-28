import {defineField, defineType} from 'sanity'
import {SparklesIcon} from '@sanity/icons/Sparkles'

const DIAS = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado']
const MESES = [
  'Janeiro',
  'Fevereiro',
  'Março',
  'Abril',
  'Maio',
  'Junho',
  'Julho',
  'Agosto',
  'Setembro',
  'Outubro',
  'Novembro',
  'Dezembro',
]

export const promotion = defineType({
  name: 'promotion',
  title: 'Promoção',
  type: 'document',
  icon: SparklesIcon,
  fields: [
    defineField({
      name: 'titulo',
      title: 'Título',
      type: 'string',
      validation: (rule) => rule.required(),
    }),
    defineField({
      name: 'slug',
      title: 'Slug',
      type: 'slug',
      options: {source: 'titulo'},
      validation: (rule) => rule.required(),
    }),
    defineField({
      name: 'selo',
      title: 'Selo',
      type: 'string',
      description: 'O número que chama o olho: "20% OFF", "2 por R$ 15".',
    }),
    defineField({
      name: 'texto',
      title: 'Texto',
      type: 'text',
      rows: 3,
      description: '1–2 frases explicando a regra.',
    }),
    defineField({
      name: 'quando',
      title: 'Quando vale',
      type: 'object',
      validation: (rule) => rule.required(),
      fields: [
        defineField({
          name: 'tipo',
          title: 'Tipo',
          type: 'string',
          options: {
            list: [
              {title: 'Toda semana (dias fixos)', value: 'semanal'},
              {title: 'Meses do ano', value: 'mensal'},
              {title: 'Período com data', value: 'periodo'},
              {title: 'Sempre', value: 'sempre'},
            ],
            layout: 'radio',
          },
          initialValue: 'semanal',
          validation: (rule) => rule.required(),
        }),
        defineField({
          name: 'dias',
          title: 'Dias da semana',
          type: 'array',
          of: [{type: 'number'}],
          options: {list: DIAS.map((title, value) => ({title, value}))},
          hidden: ({parent}) => parent?.tipo !== 'semanal',
        }),
        defineField({
          name: 'meses',
          title: 'Meses',
          type: 'array',
          of: [{type: 'number'}],
          options: {list: MESES.map((title, i) => ({title, value: i + 1}))},
          hidden: ({parent}) => parent?.tipo !== 'mensal',
        }),
        defineField({
          name: 'de',
          title: 'De',
          type: 'date',
          hidden: ({parent}) => parent?.tipo !== 'periodo',
        }),
        defineField({
          name: 'ate',
          title: 'Até',
          type: 'date',
          hidden: ({parent}) => parent?.tipo !== 'periodo',
        }),
      ],
    }),
    defineField({
      name: 'ativo',
      title: 'Ativa',
      type: 'boolean',
      description: 'Desligada esconde a promoção sem apagar o cadastro.',
      initialValue: true,
    }),
  ],
  preview: {
    select: {title: 'titulo', selo: 'selo', ativo: 'ativo'},
    prepare: ({title, selo, ativo}) => ({
      title,
      subtitle: [selo, ativo === false ? 'inativa' : null].filter(Boolean).join(' · '),
    }),
  },
})
