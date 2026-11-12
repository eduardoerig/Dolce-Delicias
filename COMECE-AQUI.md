# Dolce Delícias + Sanity

Projeto: wdisx8zs · Dataset: production

## 1. Abrir o painel

Instale Node.js 22.12 ou superior (recomendado Node 24 LTS). Extraia este ZIP e abra o terminal dentro da pasta Dolce-Delicias-main.

```bash
cd studio
npm install
npx sanity login
npm run importar
npm run dev
```

Entre com a mesma conta usada para criar o projeto Sanity. A importação envia categorias, 18 produtos, fotos existentes, 6 lojas e 3 promoções inativas. Os dados de preços/endereço são os exemplos originais do projeto: revise antes de publicar o site. Não precisa criar ou compartilhar tokens.

Abra http://localhost:3333 e entre na sua conta. A importação preserva documentos existentes: pode repetir após uma interrupção, mas não serve para sobrescrever alterações feitas no painel.

## 2. Abrir o site

Em OUTRO terminal, na pasta Dolce-Delicias-main (fora de studio):

```bash
python3 -m http.server 5500 --bind localhost
```

Abra http://localhost:5500. Não abra index.html por duplo clique. No Sanity, http://localhost:5500 deve estar em CORS sem credenciais; http://localhost:3333 deve estar com credenciais.

## 3. Editar

- Produtos: nome, descrição, categoria, foto, preço da embalagem, quantidade mínima, incremento, destaque e disponibilidade. Clique em Publish e atualize o site.
- Categorias: crie a categoria, publique e selecione nos produtos. Os filtros são gerados pelas categorias dos produtos publicados.
- Promoções: preencha título, selo, texto, agenda e selecione produtos. Ative e publique. Lojas vazias significam todas. O selo é informativo: NÃO altera automaticamente o preço do carrinho. Ajuste o preço do produto se necessário.
- Unidades: os dados alimentam disponibilidade e o WhatsApp dos pedidos. Mantenha exatamente uma loja como matriz. Os textos institucionais já escritos nos HTML continuam sendo editados nos HTML.
- Fotos: envie pelo campo Foto. A imagem de reserva mantém o caminho antigo; para retirar completamente uma foto importada, substitua-a por outra no campo Foto.
- Slug: mantenha o identificador após publicar para preservar links e referências do carrinho.

Antes da importação, se o Sanity estiver vazio, o site usa os JSON originais. Após importar, ele usa os documentos publicados. Falhas de rede mostram o aviso de catálogo indisponível, evitando vender com preço antigo. Um catálogo intencionalmente vazio não reativa os produtos locais.

## 4. Publicar depois

O site continua sendo HTML/CSS/JS e pode ser publicado como antes. Adicione o domínio HTTPS exato do site em API → CORS origins, sem credenciais. Não envie a pasta studio/node_modules para a hospedagem do site.

Para publicar apenas o painel:

```bash
cd studio
npm run deploy
```

Escolha um nome disponível e siga as instruções do Sanity. O painel publicado exige login para editar. O dataset público deve conter apenas informações públicas do catálogo.

## Verificações

Altere um preço, publique e confira no início, no produto e no carrinho. Troque uma foto e confira essas três telas. Ative uma promoção com produtos vinculados e confira a agenda. Teste o pedido pelo WhatsApp com o número real da matriz.
