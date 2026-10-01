# Plano de redesign — Admin + Site público

Objetivo: painel administrativo simples para qualquer funcionário usar sem treinamento, e site público mais limpo, com menos ruído na primeira tela.

## Diagnóstico rápido

### Admin (`resources/views/admin/*`, `app/Models/Entity.php`)
- Formulário único e genérico para todos os cadastros: todos os campos numa grade só, sem agrupamento.
- Campos técnicos expostos: `Slug`, `Passo de quantidade`, valores crus como `ENCOMENDA`, `BALCAO`, `VALOR_FIXO`.
- Promoções: dias e meses digitados como números separados por vírgula (`0=domingo a 6=sábado`). Sete condições para a promoção aparecer, e o painel não diz qual está faltando.
- WhatsApp "somente dígitos", sem máscara.
- Lista sem foto, sem filtro por status e sem ação rápida além de ativar/desativar.
- Visão geral mostra só contagens; não aponta pendências como produto sem foto ou unidade sem WhatsApp.

### Público (`resources/views/site/index.php`, `partials/*`)
Ao entrar, a pessoa vê, em sequência: hero com tipografia gigante, faixa rolante com 18 produtos duplicada, faixa de resumo (3 blocos), promoções, catálogo com 3 grupos de filtros abertos (categoria, atendimento, restrições), 18 cards com "foto em breve", "Como encomendar" em 3 passos, bloco de empresas e rodapé grande. Muitas camadas competem antes do produto.

## Skills de design disponíveis

| Skill | Uso neste projeto |
|---|---|
| `superpowers:brainstorming` | Fechar direção visual e escopo antes de codar |
| `design:design-critique` | Crítica estruturada das telas atuais (baseline) |
| `design:design-system` | Tokens: cor, tipografia, espaçamento, componentes compartilhados admin/público |
| `frontend-design:frontend-design` | Direção estética: identidade da marca sem cara de template |
| `frontend-ui-engineering` | Implementar componentes, layouts e estados com qualidade de produção |
| `design:ux-copy` | Rótulos, ajudas, mensagens de erro e estados vazios em linguagem simples |
| `design:accessibility-review` | Auditoria WCAG (contraste, foco, teclado, leitores de tela) |
| `design:user-research` / `design:research-synthesis` | Se quiser validar com quem vai usar o painel |
| `figma:figma-generate-design` / `figma:figma-design-to-code` | Opcional: prototipar no Figma antes (Figma precisa estar autenticado) |
| `browser-testing-with-devtools` / `web-perf` | Verificar no navegador e medir carregamento após mudanças |
| `humanizer` | Revisar textos do site para soarem naturais |

## Plano

### Fase 0 — Preparação
1. `git init` e commit do estado atual (hoje a pasta não é repositório: sem ponto de volta).
2. Screenshots de referência (desktop e celular) de todas as telas admin e públicas.
3. `design:design-critique` sobre essas telas e `superpowers:brainstorming` para decidir direção visual.
4. Confirmar build do CSS: `app.css` é gerado a partir de `input.css` (Tailwind v4 + daisyUI) via `npm run build`.

### Fase 1 — Base visual compartilhada (`design:design-system`)
- Revisar tema daisyUI `dolce` em `assets/css/input.css`: menos cores, escala tipográfica menor, espaçamento consistente.
- Componentes base: botão, campo, toggle, chip, card, badge de status, estado vazio, toast.
- Substituir `admin.css` avulso por classes do mesmo sistema.

### Fase 2 — Admin simples
1. **Estrutura**: sidebar enxuta com ícone + nome; menu recolhível no celular; conta e "Sair" no rodapé da sidebar.
2. **Formulários por cadastro**: `Entity::config` passa a ter seções, rótulos amigáveis e textos de ajuda.
   - Produto: "Informações" → "Preço e quantidade" → "Onde vende" → "Foto".
   - Slug gerado automaticamente do nome (editável só em "Avançado").
   - Enums humanizados: "Encomenda", "Balcão", "Os dois"; "% de desconto" / "R$ de desconto".
   - Ativo/Destaque como toggles; prévia da foto antes de salvar; barra "Salvar" fixa no rodapé.
3. **Promoções**:
   - Dias da semana e meses como chips clicáveis; período com calendário; agenda mostra só os campos do tipo escolhido.
   - Painel "Aparece no site?" com checklist do que falta (ativa, desconto, produto ativo, unidade ativa, agenda vigente).
4. **Listas**: miniatura, filtro Ativos/Inativos/Todos, toggle de status direto na linha, busca instantânea, estado vazio com ação.
5. **Visão geral**: cartões de pendências clicáveis (produtos sem foto, promoções em rascunho, unidades sem WhatsApp) e atalhos.
6. Backend: ajustes mínimos em `Entity.php`, `CatalogService` (slug automático) e `Validator`; validações atuais mantidas.

### Fase 3 — Site público mais limpo
1. **Hero** menor: título curto, uma frase e um botão. Remover a faixa rolante.
2. **Resumo**: juntar os 3 blocos numa linha discreta abaixo do hero ou remover.
3. **Catálogo**: categorias como chips horizontais; atendimento e restrições dentro de um botão "Filtros".
4. **Card de produto**: placeholder bonito quando não há foto (ou esconder a área de imagem); menos texto, só nome, preço e mínimo.
5. **Promoções**: só aparece com promoção vigente (já é assim), em formato mais compacto.
6. **Como encomendar** compacto (uma linha com 3 passos); bloco Empresas vira link no rodapé.
7. **Header e rodapé**: menos links, rodapé em colunas curtas.

### Fase 4 — Verificação
- `design:accessibility-review` em admin e público.
- Teste no navegador em desktop e celular (`browser-testing-with-devtools`), incluindo fluxo completo: cadastrar produto, criar promoção, ver no site, montar carrinho, abrir WhatsApp.
- `npm test` e `phpunit` existentes; `web-perf` na home.

## Ordem sugerida
Fase 0 → 1 → Admin (2) → Público (3) → 4. Cada fase entregue em commits pequenos e revisada no navegador antes da próxima.

## Perguntas em aberto
1. Manter o vermelho e o logo atual como base da identidade, ou repaginar a paleta também?
2. Quem usa o painel no dia a dia (dono, atendente, gerente de unidade)? Isso define o nível de simplificação.
3. Há fotos reais dos produtos? Sem elas, o catálogo continua com placeholders.
4. Prefere prototipar no Figma antes ou ir direto para o código?
