import {defineField, defineType} from 'sanity'
import {orderRankField, orderRankOrdering} from '@sanity/orderable-document-list'
import {TagIcon} from '@sanity/icons/Tag'

export const category = defineType({
  name: 'category',
  title: 'Categoria',
  type: 'document',
  icon: TagIcon,
  description: 'Arraste as categorias na lista para mudar a ordem dos botões de filtro do site.',
  fields: [
    defineField({
      name: 'nome',
      title: 'Nome da categoria',
      type: 'string',
      validation: (rule) => rule.required().error('Dê um nome à categoria.'),
    }),
    orderRankField({type: 'category'}),
  ],
  orderings: [orderRankOrdering],
  preview: {select: {title: 'nome'}},
})
