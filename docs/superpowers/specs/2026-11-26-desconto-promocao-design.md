# Desconto da promoção no carrinho e no fechamento do pedido

Data: 2026-11-26 · Tamanho: mudança pequena

## Por quê

Hoje a promoção aparece só como selo de texto ("15% OFF"). O carrinho, o total e a
mensagem do WhatsApp mostram o preço cheio, e o desconto fica para a matriz aplicar na
conversa. O cliente não vê quanto economiza, e isso não é intuitivo.

## Objetivo

No carrinho (gaveta e `pages/carrinho.html`) e na mensagem do WhatsApp, o item em
promoção mostra o preço original riscado, o preço com desconto e de qual promoção ele
vem, deixando claro que vale na Matriz — Centro.

**Fora do escopo:** preço riscado nos cards do catálogo e na página do produto (pode vir
depois), escolher outra unidade para o pedido (projeto separado: as outras lojas ainda
não têm WhatsApp real) e descontos que não sejam percentuais ("leve 3, pague 2").

**Seria um fracasso se:** o site mostrar um desconto que a matriz não vai dar, ou se o
preço riscado continuar aparecendo depois que a promoção acaba.

## Regra do desconto (decidida)

O item tem desconto só quando existe promoção que:
1. aparece no site (ativa, dentro das datas e com produto vendido na loja, como já é hoje em `promocaoAparece`);
2. **vale hoje** (`promocao.valeHoje`);
3. inclui a **matriz** nas lojas participantes (o pedido do site sempre vai para a matriz);
4. tem o campo **Desconto (%)** preenchido, maior que 0.

Se o produto estiver em mais de uma promoção que vale, usa o **maior** desconto (minha decisão).

## Abordagens consideradas

- **A. Campo "Desconto (%)" na promoção, no Sanity (escolhida).** Um número só por
  promoção, e o selo continua livre. Promoção sem o campo continua funcionando como hoje.
- B. "Preço promocional" em cada produto: o preço fica separado da promoção e continua
  aparecendo quando ela acaba. Isso viola a regra.
- C. Ler a porcentagem do texto do selo: quebra sem aviso com selos como "Combo".

## Como (decisões)

- **Sanity:** campo novo `desconto` (number, 1 a 90, opcional) em `promocao`,
  em `studio/schema.js`. A consulta já usa `...`, então o campo chega ao site sem mudar o GROQ.
  O `dados/promocoes.json` local também ganha o campo, para o modo sem Sanity.
- **Cálculo:** uma função em `dados.js` devolve, pelo slug, a melhor promoção que vale
  para o produto. O preço com desconto é calculado **na hora de desenhar**, a partir de
  `site.promocoes`. Nada de desconto é salvo no `localStorage`: um item adicionado na
  quarta fica sem desconto na quinta. Arredondamento para centavos.
- **Carrinho (gaveta e página):** a linha do item mostra o subtotal original riscado, o
  subtotal com desconto e a linha "‹título› −X% · válido na Matriz — Centro". A página
  do carrinho mostra Subtotal, Descontos e Total estimado quando há desconto. Sem
  desconto, fica como hoje.
- **WhatsApp:** cada item com desconto vira "de R$ X por R$ Y (‹título› −X%)" e entra a
  linha "Descontos: − R$ Z" antes do total.
- **Textos:** saem os avisos "o site mostra o preço cheio; o desconto é aplicado pela
  matriz" da home e da página do produto. O "Valor de referência. A matriz confirma..."
  do carrinho continua.

## Fica para quem implementar

Marcação e classes do preço riscado (seguir o visual da gaveta: `<s>` com
`text-crust`), o texto exato das linhas e onde a linha de Descontos entra na gaveta.
