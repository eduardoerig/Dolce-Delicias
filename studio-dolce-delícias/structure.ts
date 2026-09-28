import type {StructureResolver} from 'sanity/structure'
import {orderableDocumentListDeskItem} from '@sanity/orderable-document-list'
import {BasketIcon} from '@sanity/icons/Basket'
import {PackageIcon} from '@sanity/icons/Package'
import {HomeIcon} from '@sanity/icons/Home'
import {SparklesIcon} from '@sanity/icons/Sparkles'
import {TagIcon} from '@sanity/icons/Tag'
import {SortIcon} from '@sanity/icons/Sort'
import {ThListIcon} from '@sanity/icons/ThList'

/** Lista de produtos filtrada, com o botão "+" já preenchendo a linha. */
const produtosDaLinha = (S: Parameters<StructureResolver>[0], linha: 'atacado' | 'varejo', title: string) =>
  S.documentList()
    .title(title)
    .schemaType('product')
    .filter('_type == "product" && linha == $linha')
    .params({linha})
    .defaultOrdering([{field: 'orderRank', direction: 'asc'}])
    .initialValueTemplates([S.initialValueTemplateItem('product-linha', {linha})])

export const structure: StructureResolver = (S, context) =>
  S.list()
    .title('Dolce Delícias')
    .items([
      S.listItem()
        .title('Produtos')
        .icon(BasketIcon)
        .child(
          S.list()
            .title('Produtos')
            .items([
              S.listItem()
                .title('Encomendas')
                .icon(PackageIcon)
                .child(produtosDaLinha(S, 'atacado', 'Encomendas')),
              S.listItem()
                .title('Balcão')
                .icon(BasketIcon)
                .child(produtosDaLinha(S, 'varejo', 'Balcão')),
              S.listItem()
                .title('Por categoria')
                .icon(TagIcon)
                .child(
                  S.documentTypeList('category')
                    .title('Categorias')
                    .defaultOrdering([{field: 'orderRank', direction: 'asc'}])
                    .child((categoriaId) =>
                      S.documentList()
                        .title('Produtos')
                        .schemaType('product')
                        .filter('_type == "product" && categoria._ref == $categoriaId')
                        .params({categoriaId})
                        .defaultOrdering([{field: 'orderRank', direction: 'asc'}])
                        .initialValueTemplates([
                          S.initialValueTemplateItem('product-categoria', {categoriaId}),
                        ]),
                    ),
                ),
              S.divider(),
              S.documentTypeListItem('product').title('Todos os produtos').icon(ThListIcon),
              orderableDocumentListDeskItem({
                type: 'product',
                title: 'Mudar a ordem (arrastar)',
                icon: SortIcon,
                S,
                context,
              }),
            ]),
        ),
      S.listItem()
        .title('Promoções')
        .icon(SparklesIcon)
        .child(S.documentTypeList('promotion').title('Promoções')),
      S.divider(),
      orderableDocumentListDeskItem({
        type: 'category',
        title: 'Categorias',
        icon: TagIcon,
        S,
        context,
      }),
      orderableDocumentListDeskItem({
        type: 'store',
        title: 'Unidades (lojas)',
        icon: HomeIcon,
        S,
        context,
      }),
    ])
