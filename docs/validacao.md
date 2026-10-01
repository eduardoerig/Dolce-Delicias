# Validação da entrega

Data: 18/09/2026. Base Git auditada: `fa918782c7c06479a0ace290f64d0a00126ec93d`, repositório `https://github.com/eduardoerig/Dolce-Delicias`. Sem push ou deploy remoto.

## Verificações aprovadas

| Verificação | Resultado observado |
|---|---|
| Sintaxe PHP | 55 arquivos aprovados em PHP 8.3.6 |
| PHPUnit — domínio | 8 testes, 20 assertions, todos aprovados |
| Autenticação integrada isolada | 1 teste, 4 assertions: senha correta/incorreta, usuário ausente e limite por IP |
| JavaScript | 3 testes aprovados: preço cheio, mensagem completa e retirada sem endereço antigo |
| CSS | `npm run build` concluído; Tailwind e daisyUI compilados e assets copiados |
| Migration/seed | Schema vazio criado, 18 produtos, 7 categorias, 6 unidades e 3 promoções inativas importados |
| Idempotência | Segundo seed preservou cadastros e confirmou versão já aplicada |
| SQL direto em PGlite | Sete violações corretamente rejeitadas: segunda matriz, matriz inativa, vínculo duplicado, FK inexistente, percentual acima de 100, datas invertidas, dia semanal inválido |
| Transação SQL direta | Inserção parcial seguida de FK inválida e rollback não deixou o cadastro parcial |
| Migration reversa | `down` executado e `up` reaplicado sem erro |
| HTTP | Rotas públicas, login, visitante redirecionado, cinco cadastros, edição, N:N, upload de imagem, PDF/substituição e promoção exercitados |
| Segurança HTTP | POST sem CSRF rejeitado (403), GESTOR impedido de gerir usuários (403), arquivos privados retornando 404 |
| Navegador Chromium | Fluxo real com Playwright aprovado, inclusive preço cheio no carrinho após criar promoção, filtros, imagem/PDF, desktop e viewport de 390 px sem overflow horizontal |
| Revisão visual | Dashboard desktop e mobile inspecionados; cores e templates públicos preservados |
| Diff | `git diff --check` sem erro |

## Limites e pendências reais

Este ambiente não disponibilizou Docker nem uma instância nativa de PostgreSQL. A validação de SQL, PDO, seed e HTTP utilizou **PGlite (PostgreSQL embarcado) com uma ponte TCP**. Isso não substitui homologação do Compose e PostgreSQL 16 nativo.

A execução conjunta inicial dos 16 testes PHPUnit contra essa ponte não passou: foram observados 2 erros e 1 falha, envolvendo recuperação após erro de banco, autenticação posterior e SQLSTATE de constraint. A hipótese é incompatibilidade da ponte com a sequência de erros/protocolo PDO; **não foi confirmada em PostgreSQL nativo**. As mesmas restrições e o rollback foram aprovados diretamente no motor SQL, e a autenticação passou isoladamente. A aplicação mantém prepared statements nativos; não foi introduzido modo de produção com SQL interpolado para contornar o ambiente.

Os testes de integração de `tests/DatabaseTest.php` permanecem incluídos e precisam passar em PostgreSQL nativo antes de considerar cumprido o aceite de “todos os testes passam”. Execute `RUN_DB_TESTS=1 composer test` num banco de testes ou o comando correspondente do README dentro do Compose. Os testes criam e removem schemas temporários.

O Dockerfile/Compose foi escrito e revisado, mas não construído/executado aqui. HTTPS, concorrência real, backup/restauração e operação em produção não foram homologados.

As fotos, contatos, endereços, PDFs e ofertas comerciais de exemplo exigem substituição/confirmação com a padaria. A aplicação não foi publicada.
