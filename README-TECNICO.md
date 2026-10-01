# Dolce Delícias — PHP MVC + PostgreSQL

Evolução da base `eduardoerig/Dolce-Delicias`: catálogo e identidade visual preservados, persistência PostgreSQL e painel administrativo em PHP puro. Não há pagamento on-line: o pedido é uma prévia enviada ao WhatsApp da matriz, sempre pelo preço cheio.

## Instalação com Docker

Requer Docker Engine com Compose. Copie `.env.example` para `.env` e preencha `DB_PASSWORD`, `ADMIN_LOGIN` e `ADMIN_PASSWORD` (12 a 72 bytes). `DB_HOST=db` deve ser mantido no Compose. Não versionar `.env`.

```bash
cp .env.example .env
# Preencha as senhas no editor antes de prosseguir.
docker compose up -d --build
docker compose exec app php bin/console.php migrate
docker compose exec app php bin/console.php seed
```

Abra `http://localhost:8000` e `http://localhost:8000/login`. O primeiro administrador usa as credenciais do `.env`; não existe senha padrão. O seed não altera senhas nem sobrescreve cadastros já importados. O banco usa volume persistente e healthcheck; uploads e logs têm volumes próprios. O bind HTTP local é restrito a 127.0.0.1.

Após alterar o código, execute `docker compose up -d --build`. O projeto não usa bind mount de código: dependências e CSS são compilados na imagem. Em produção, configure HTTPS, `APP_ENV=production`, servidor/reverse proxy, backups do PostgreSQL e do volume de uploads. A instalação antiga da Vercel está documentada em `docs/vercel-legacy.json`, mas não atende aos uploads persistentes desta aplicação e não é o deploy configurado.

## Desenvolvimento sem Docker

Requer PHP 8.2+, extensões PDO PostgreSQL, mbstring, fileinfo, DOM/XML, Composer, Node.js e PostgreSQL 16+. Crie um banco e usuário; configure `.env` com `DB_HOST=127.0.0.1` e credenciais correspondentes.

```bash
composer install
npm ci
npm run build
composer migrate
composer seed
composer serve
```

Use **somente `public/` como document root**. O servidor embutido é destinado ao desenvolvimento. `assets/` contém as fontes, e `npm run build` compila Tailwind/daisyUI e copia os arquivos para `public/assets/`.

## Arquitetura

- `public/index.php`: front controller, composição explícita de dependências e tratamento centralizado de erros.
- `routes/web.php`: rotas GET/POST públicas e administrativas; redirecionamentos para URLs antigas.
- `app/Controllers`: coordenação de requests e views, sem SQL.
- `app/Repositories`: PDO com identificadores fixos e parâmetros vinculados; consultas de catálogo e administração.
- `app/Services`: validação e persistência transacional, autenticação e uploads.
- `app/Models`: definição dos cadastros e regras de calendário de promoções.
- `app/Middleware`: autenticação e autorização. GESTOR gerencia o catálogo; somente ADMIN gerencia usuários.
- `resources/views/site`: templates originais adaptados. `partials/` permanece compartilhado, e os helpers recebem os dados carregados pelo controller, sem consultar SQL.
- `resources/views/admin`: login, dashboard, listagens paginadas e formulários.
- `database/migrations`: SQL versionado, registrado em `migrations` e executado sob lock. `rollback` reverte a última migration e é destrutivo: use somente conscientemente.
- `data/products.php`, `data/units.php`, `data/promocoes.php`: fontes históricas usadas exclusivamente pelo seed; não editar para administrar o site.
- `data/empresa.php`: textos institucionais preservados, ainda editáveis no arquivo.

Não foi necessário criar `produto_precos`: a auditoria encontrou exatamente uma faixa por produto. `preco` é o preço da embalagem descrita em `rotulo_preco`; o carrinho deriva o preço por peça sem arredondar prematuramente. O seed falha explicitamente se encontrar múltiplas faixas, evitando perda silenciosa.

## Modelo corrigido

Consulte `docs/modelo-corrigido.md` e `database/migrations/001_schema.up.sql`.

As tabelas de domínio são: `usuarios`, `categorias`, `produtos`, `unidades`, `produto_unidade`, `promocoes`, `promocao_produto`, `promocao_unidade`, `produto_sabores`, `tags`, `produto_tags`. Existem ainda tabelas técnicas de migrations, controle de seed e tentativas de login.

- FKs de categoria e autoria; `senha_hash`, perfis e timestamps automáticos.
- Produto/unidade e promoção/produto/unidade separados. As ligações incorretas do desenho foram removidas.
- Índice único parcial limita a uma MATRIZ. Trigger de constraint diferida impede deixar uma rede cadastrada sem matriz ativa.
- Promoções percentuais limitadas a 100%, datas coerentes e agendas validadas no PHP e no banco.
- `dias_semana SMALLINT[]` e `meses SMALLINT[]` preservam as agendas com vários dias/meses já suportadas pela base.

## Fluxo administrativo

O dashboard mostra produtos ativos, categorias, unidades ativas, promoções ativas/vigentes e produtos sem disponibilidade em unidades ativas. Listagens oferecem busca, paginação, edição e mudança de status.

O produto possui categoria, preço/embalagem, quantidades, linha, imagem, sabores, tags e seleção de unidades disponíveis. Unidades possuem dados de contato, imagem e catálogo PDF. Promoções possuem agenda, desconto informativo, produtos, unidades e linha de atendimento.

Exclusão de produto significa **arquivamento/desativação**, preservando os vínculos. Categoria inativa oculta seus produtos no público. Só produtos disponíveis na matriz podem ser adicionados ao pedido do site; as demais unidades continuam aparecendo na consulta de disponibilidade e no contato direto.

Promoção divulgada só aparece se houver combinação efetiva entre produto ativo, categoria ativa, unidade ativa, disponibilidade e linha compatível. Semanal é divulgada durante a semana, dentro de seus limites de data; mensal e período aparecem somente na vigência. Os cards mostram regra, lojas, atendimento e agenda, sem alterar preço ou total. Ausência de matriz não redireciona pedidos para filial.

## Segurança e arquivos

Sessões HttpOnly/SameSite=Lax, Secure em produção; renovação de sessão no login; token CSRF em todos os POSTs; escape de HTML e JSON; prepared statements nativos com parâmetros; revalidação de usuário ativo/perfil em cada request; limite de 10 tentativas de login por IP em 15 minutos. O último ADMIN ativo não pode ser removido, e o administrador não pode desativar/rebaixar a própria conta. Mudança de senha invalida sessões anteriores.

Uploads ficam fora de `public/`, com nome aleatório. Imagens: JPEG/PNG/WebP, MIME e dimensões, limite 5 MB. PDFs: MIME e assinatura `%PDF-`, limite 10 MB. O arquivo antigo só é removido após commit do novo; falhas de gravação removem o upload novo. PDFs são entregues como download por `/catalogos/{slug}/download`, validando a unidade ativa. Imagens são servidas por `/media/{arquivo}`. Não são aceitos caminhos arbitrários.

## Conteúdo comercial pendente

A base contém números `55000000000`, nomes/endereço/textos `PREENCHER`, fotos ausentes e catálogos de exemplo. O checkout bloqueia o número de exemplo. Substitua dados de unidades, imagens e PDFs no painel antes de uso comercial. Confirme sabores/restrições e textos de `data/empresa.php` com o cliente.

As três promoções legadas não definem desconto nem participantes. O seed mantém nome, descrição e agenda, **inativas e sem vínculos inventados**, com valor técnico zero. Complete os campos no painel para ativar. Os PDFs e demais conteúdos de exemplo originais permanecem preservados.

## Testes

```bash
# Domínio: calendário, validações, senha, CSRF, uploads e preço por embalagem
composer test
# Carrinho e mensagem de WhatsApp
npm test
# Integração em banco de testes: schemas temporários criados e removidos por teste
RUN_DB_TESTS=1 composer test
# Dentro do Compose local, nunca apontando a produção:
docker compose exec -e RUN_DB_TESTS=1 app vendor/bin/phpunit
# Sintaxe
find app config routes resources partials database bin public tests -name '*.php' -exec php -l {} \;
```

O teste de navegador `tests/browser.cjs` usa Playwright como dependência opcional de desenvolvimento (`npm install --no-save playwright` e `npx playwright install chromium`). Execute somente sobre ambiente descartável, pois cria cadastros reais. Defina `ADMIN_LOGIN`, `ADMIN_PASSWORD` e opcionalmente `TEST_BASE_URL`, e execute `node tests/browser.cjs`.

Os resultados e limitações da validação desta entrega estão em `docs/validacao.md`.

Para repetir a verificação SQL alternativa da entrega: instale `@electric-sql/pglite` como dependência temporária de desenvolvimento e execute `node tests/sql-pglite.mjs`. Isso não substitui a suite em PostgreSQL nativo.
