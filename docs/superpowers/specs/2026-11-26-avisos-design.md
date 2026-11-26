# Avisos (toasts) mais simples, com status, tempo e fechar

Data: 2026-11-26 · Tamanho: mudança pequena

## Por quê

No computador os avisos aparecem no canto superior direito, por cima da gaveta do
carrinho quando ela está aberta. E o aviso não diz se algo deu certo ou errado: todos
são iguais, e o de "adicionado" ainda carrega a foto do produto.

## Objetivo

- Aviso simples: ícone de status (✓ sucesso, ✕ erro) com cor (verde / vermelho),
  título, detalhe opcional, botão de fechar e uma barra que mostra o tempo restante.
- No computador: canto **inferior esquerdo**, longe da gaveta (que abre à direita).
- No celular: **topo, largura toda** (a gaveta ocupa a tela inteira e embaixo ficam a
  barra "Fechar pedido" e o botão do WhatsApp). Decidido: opção A.

**Fora do escopo:** pausar o tempo com o mouse em cima, botão de ação ("Desfazer"),
outros tipos (aviso/info).

## Abordagens

Uma só faz sentido: ajustar a função `mostrarAviso` existente e o CSS `.aviso`. Uma
biblioteca de toasts seria dependência nova para 6 avisos; um componente novo
duplicaria o que já existe.

## Como (decisões)

- **Assinatura:** `mostrarAviso(tipo, titulo, detalhe)`, com `tipo` `'sucesso'` ou
  `'erro'`. O parâmetro `foto` sai. Os 6 chamadores em `assets/js/carrinho.js`:

  | Aviso | Tipo |
  |---|---|
  | adicionado: título "‹nome› adicionado", detalhe "‹qtd› un no pedido" | sucesso |
  | "Item removido do pedido." | sucesso |
  | "Pedido esvaziado." | sucesso |
  | "Adicione pelo menos um item antes de fechar o pedido." | erro |
  | "Informe o endereço da entrega." | erro |
  | "A matriz ainda não tem WhatsApp cadastrado." | erro |

- **Visual:** mantém o cartão escuro (`--color-ink`). O ícone fica num círculo de 2rem
  preenchido com `--color-success` (✓) ou `--color-error` (✕), as cores do tema que já
  existem. A barra de tempo, de 3px na base do cartão e na cor do status, encolhe de
  100% a 0% em 4s (animação CSS linear).
- **Fechar:** botão ✕ à direita (`aria-label="Fechar aviso"`), reaproveitando o CSS
  `.aviso-fechar`, que já existe. Fechar, ou o fim dos 4s, aplica `.saindo` (fade que já
  existe) e remove o aviso.
- **Posição:** `.avisos` no celular `top: calc(.75rem + env(safe-area-inset-top))`,
  largura toda. A partir de 48rem: `left: 1.5rem; bottom: 1.5rem; width: 24rem`.
  Continua acima da gaveta (z-index 80 > 70).
- **Acessibilidade:** o leitor de tela continua ouvindo pelo `[data-aviso-leitor]`; erro
  vai com o prefixo "Erro: ". Com `prefers-reduced-motion`, a entrada fica sem animação
  e a barra continua (ela indica o tempo, não é enfeite).

## Fica para quem implementar

O SVG dos ícones (traço como os outros ícones do site) e o espaçamento fino do cartão.
