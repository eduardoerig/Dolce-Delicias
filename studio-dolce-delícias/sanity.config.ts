import {defineConfig, isDev} from 'sanity'
import {structureTool} from 'sanity/structure'
import {visionTool} from '@sanity/vision'
import {ptBRLocale} from '@sanity/locale-pt-br'
import {schemaTypes} from './schemaTypes'
import {structure} from './structure'

export default defineConfig({
  name: 'default',
  title: 'Dolce Delícias',

  projectId: 'qup78m3c',
  dataset: 'production',

  plugins: [
    structureTool({structure, title: 'Conteúdo'}),
    ptBRLocale(),
    // Ferramenta de consultas GROQ: só aparece no `npm run dev`, não no Studio publicado.
    ...(isDev ? [visionTool()] : []),
  ],

  schema: {
    types: schemaTypes,
    // Modelos usados pelo "+" das listas filtradas (structure.ts), que já vêm preenchidos.
    templates: (prev) => [
      ...prev,
      {
        id: 'product-linha',
        title: 'Produto',
        schemaType: 'product',
        parameters: [{name: 'linha', type: 'string'}],
        value: ({linha}: {linha: string}) => ({linha}),
      },
      {
        id: 'product-categoria',
        title: 'Produto',
        schemaType: 'product',
        parameters: [{name: 'categoriaId', type: 'string'}],
        value: ({categoriaId}: {categoriaId: string}) => ({
          categoria: {_type: 'reference', _ref: categoriaId},
        }),
      },
    ],
  },

  document: {
    // O botão "Criar" do topo oferece só o que se cria no dia a dia: produto e promoção.
    newDocumentOptions: (prev, {creationContext}) =>
      creationContext.type === 'global'
        ? prev.filter((t) => ['product', 'promotion'].includes(t.templateId))
        : prev,
  },
})
