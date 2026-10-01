# Catálogos em PDF

Um PDF por unidade, mais um PDF com o catálogo completo da rede. Cada unidade tem
alguns produtos diferentes, por isso o catálogo é separado por loja.

Onde esses arquivos aparecem no site:

- **Menu "Catálogo"** (`partials/header.php`) — lista todas as unidades para download
  e termina com o **PDF completo**.
- **Página de unidades** (`unidades.php` → `partials/unit-section.php`) — botão
  "Baixar catálogo" dentro da seção de cada loja.

## Os arquivos aqui são PLACEHOLDER

Os PDFs desta pasta foram gerados só para os botões terem o que baixar durante o
desenvolvimento. **Não envie isto para o cliente.** Substitua cada um pelo catálogo
real, mantendo o nome do arquivo.

## Nomes esperados

O nome do arquivo é o `slug` da unidade, definido em `data/units.php`:

| Unidade (`slug` em `data/units.php`) | Arquivo |
|---|---|
| `matriz` | `catalogos/matriz.pdf` |
| `unidade-1` | `catalogos/unidade-1.pdf` |
| `unidade-2` | `catalogos/unidade-2.pdf` |
| `unidade-3` | `catalogos/unidade-3.pdf` |
| `unidade-4` | `catalogos/unidade-4.pdf` |
| `unidade-5` | `catalogos/unidade-5.pdf` |
| — (rede inteira) | `catalogos/completo.pdf` |

Se você renomear um slug em `data/units.php`, renomeie o PDF junto — ou ajuste o
campo `catalogoPdf` daquela unidade.

O `completo.pdf` não pertence a nenhuma unidade: o caminho dele está fixo em
`dd_catalogo_completo()`, em `partials/bootstrap.php`.

## Se o arquivo não existir

O item some do menu e o botão da unidade vira **"Catálogo em breve"**, desabilitado
(o teste é `dd_tem_pdf()`, em `partials/bootstrap.php`). Nenhum link quebrado
aparece para o cliente.

## Quando houver back-end

O caminho fixo dá lugar a uma URL vinda do banco. Basta o campo `catalogoPdf`
devolver a nova URL — as views não precisam mudar.
