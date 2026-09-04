# Catálogos em PDF

Um PDF por unidade. O botão **"Baixar catálogo"** de cada card em
"Nossas unidades" aponta para o arquivo indicado no campo `catalogoPdf` de
`data/units.php`.

## Os arquivos aqui são PLACEHOLDER

Os PDFs desta pasta foram gerados só para o botão ter o que baixar durante o
desenvolvimento. **Não envie isto para o cliente.** Substitua cada um pelo
catálogo real da unidade, mantendo o nome do arquivo.

## Nomes esperados

O nome do arquivo é o `slug` da unidade:

| Unidade (`slug` em `data/units.php`) | Arquivo |
|---|---|
| `matriz` | `catalogos/matriz.pdf` |
| `unidade-2` | `catalogos/unidade-2.pdf` |
| `unidade-3` | `catalogos/unidade-3.pdf` |
| `unidade-4` | `catalogos/unidade-4.pdf` |
| `unidade-5` | `catalogos/unidade-5.pdf` |
| `unidade-6` | `catalogos/unidade-6.pdf` |

Se você renomear um slug em `data/units.php`, renomeie o PDF junto — ou ajuste o
campo `catalogoPdf` daquela unidade.

## Se o arquivo não existir

O botão vira **"Catálogo em breve"** e fica desabilitado (veja
`partials/unit-card.php`). Nenhum link quebrado aparece para o cliente.

## Quando houver back-end

O caminho fixo dá lugar a uma URL vinda do banco. Basta o campo `catalogoPdf`
devolver a nova URL — `partials/unit-card.php` não precisa mudar.
