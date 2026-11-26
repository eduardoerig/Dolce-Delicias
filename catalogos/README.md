# Catálogos em PDF

Um PDF por unidade, mais um PDF com o catálogo completo da rede. A matriz não tem PDF
próprio: os links dela apontam para o `completo.pdf`. Cada unidade tem
alguns produtos diferentes, por isso o catálogo é separado por loja.

Onde esses arquivos aparecem no site:

- **Menu "Catálogo"** do topo — as unidades 1 a 5 e o **PDF completo** (que serve à matriz).
- **Página de unidades** — botão de catálogo em cada loja.

## Os arquivos aqui são PLACEHOLDER

TODO (R, G): montar o catálogo real de cada loja e o completo da rede, com o mesmo nome de arquivo.

Os PDFs desta pasta foram gerados só para os botões terem o que baixar durante o
desenvolvimento. **Não envie isto para o cliente.** Substitua cada um pelo catálogo
real, mantendo o nome do arquivo.

## De onde vem o link

Tudo vem do cadastro de unidades no Sanity (menu "Catálogo" pelo `assets/js/site.js`,
cartões de `pages/unidades.html` pelo `assets/js/paginas/unidades.js`):

- **Cada loja:** o PDF enviado em "Enviar catálogo PDF"; sem envio, o arquivo indicado
  em "Caminho do catálogo local" (os desta pasta).
- **PDF completo:** o PDF enviado na **matriz**; sem envio, o `completo.pdf` desta pasta.

O PDF enviado pelo Sanity é baixado direto (o link leva `?dl=`).
