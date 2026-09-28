/**
 * Migração única (setembro/2026): troca o campo numérico `ordem` pelo `orderRank`
 * da lista arrastável e move as restrições alimentares de `tags` para `restricoes`.
 *
 *   npx sanity exec scripts/migrate-orderrank-restricoes.ts --with-user-token
 *
 * Aplica também nos rascunhos abertos, para a publicação não desfazer a migração.
 */
import {getCliClient} from 'sanity/cli'
import {LexoRank} from 'lexorank'
import {RESTRICOES} from '../schemaTypes/product'

const client = getCliClient({apiVersion: '2026-09-28'})
const VALORES = RESTRICOES.map((r) => r.value)

type Doc = {_id: string; tags?: string[]}

async function main() {
  const listas: Record<string, string> = {
    category: '*[_type == "category" && !(_id in path("drafts.**"))] | order(ordem asc)',
    store: '*[_type == "store" && !(_id in path("drafts.**"))] | order(ordem asc)',
    product:
      '*[_type == "product" && !(_id in path("drafts.**"))] | order(categoria->ordem asc, ordem asc)',
  }

  const existentes = new Set(await client.fetch<string[]>('*[_id in path("drafts.**")]._id'))
  const tx = client.transaction()

  for (const [tipo, groq] of Object.entries(listas)) {
    const docs = await client.fetch<Doc[]>(`${groq}{_id, tags}`)
    let rank = LexoRank.min().genNext().genNext()
    for (const doc of docs) {
      const set: Record<string, unknown> = {orderRank: rank.toString()}
      if (tipo === 'product') {
        const tags = doc.tags ?? []
        set.restricoes = tags.filter((t) => VALORES.includes(t))
        set.tags = tags.filter((t) => !VALORES.includes(t))
      }
      for (const id of [doc._id, `drafts.${doc._id}`]) {
        if (id === doc._id || existentes.has(id)) {
          tx.patch(id, (p) => p.set(set).unset(['ordem']))
        }
      }
      rank = rank.genNext().genNext()
    }
    console.log(`${tipo}: ${docs.length}`)
  }

  await tx.commit()
  console.log('Migração concluída.')
}

main().catch((err) => {
  console.error(err)
  process.exit(1)
})
