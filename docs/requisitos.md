# Prompt — Evolução do Dolce Delícias para PHP + PostgreSQL + MVC

> Aplicado nesta implementação com as correções de `modelo-corrigido.md`. A agenda usa arrays de dias/meses para preservar a base; promoções comerciais incompletas são importadas inativas; a exclusão de produto arquiva o cadastro. Resultados reais e limites da validação estão em `validacao.md`.

Você é um engenheiro de software sênior especializado em PHP, PostgreSQL, arquitetura MVC, segurança web e evolução de sistemas legados. Sua tarefa é transformar o repositório existente **Dolce-Delicias** em uma aplicação completa, mantendo o site público atual e adicionando persistência em PostgreSQL e um painel administrativo.

## 1. Fonte principal e modo de trabalho

Trabalhe diretamente sobre o repositório clonado `Dolce-Delicias`. Antes de alterar qualquer arquivo:

1. Leia integralmente o `README.md`.
2. Analise a árvore do projeto e os arquivos atuais, principalmente:
   - `index.php`, `produto.php`, `carrinho.php`, `unidades.php` e `sobre.php`;
   - `partials/bootstrap.php` e os demais arquivos de `partials/`;
   - `data/products.php`, `data/units.php`, `data/promocoes.php` e `data/empresa.php`;
   - `assets/js/cart.js`, `assets/js/ui.js` e `assets/css/input.css`;
   - `catalogos/` e `api/index.php`.
3. Verifique se existe `AGENTS.md` ou outro arquivo de instruções e obedeça-o.
4. Verifique o estado do Git e preserve qualquer alteração existente do usuário.
5. Não recrie o projeto do zero. Evolua a base existente de maneira incremental.
6. Preserve a identidade visual atual: vermelho, creme, amarelo, tipografia marcante, Tailwind CSS 4, daisyUI 5, responsividade e componentes já implementados.
7. Preserve as funcionalidades públicas que já funcionam: catálogo, busca, filtros, página do produto, páginas institucionais, unidades, PDFs, carrinho em `localStorage` e fechamento do pedido pelo WhatsApp da matriz.
8. Não use Laravel, Symfony ou outro framework full stack. Implemente uma arquitetura MVC didática em **PHP puro**, com Composer e autoload PSR-4.
9. Não entregue somente uma explicação, pseudocódigo ou estrutura vazia. Implemente os arquivos, migrações, telas, validações e testes necessários.
10. Não deixe `TODO`, código fictício ou funcionalidade pela metade. Quando um dado comercial real não estiver disponível, mantenha os placeholders já existentes e documente onde substituí-los.

## 2. Objetivo do sistema

O sistema será o site institucional e catálogo da Dolce Delícias, acompanhado de um painel administrativo protegido por login.

O público poderá:

- visualizar produtos ativos;
- pesquisar e filtrar por categoria, linha de atendimento e restrições alimentares já suportadas pela interface;
- consultar detalhes, preços de referência, pedido mínimo, sabores e unidades que vendem cada item;
- visualizar promoções vigentes ou divulgadas;
- consultar dados das unidades e baixar o PDF específico de cada unidade;
- montar um pedido no navegador;
- revisar retirada ou entrega, endereço, pagamento e observações;
- finalizar o pedido pelo WhatsApp da matriz.

O gestor poderá, dentro do painel:

- autenticar-se com segurança;
- visualizar um dashboard resumido;
- gerenciar usuários administrativos;
- gerenciar categorias;
- gerenciar produtos, imagens, preços e disponibilidade;
- selecionar em quais unidades cada produto está disponível;
- gerenciar unidades e enviar/substituir o PDF de cada unidade;
- gerenciar promoções;
- selecionar os produtos e as unidades participantes de cada promoção;
- definir se a promoção vale para balcão, encomenda ou ambos;
- ativar e desativar registros sem precisar excluí-los.

O sistema **não realizará pagamento on-line** nesta versão. O carrinho continuará sendo uma prévia e o pedido continuará sendo confirmado pelo WhatsApp da matriz.

## 3. Tecnologias obrigatórias

- PHP 8.2 ou superior, com `declare(strict_types=1)`;
- PostgreSQL 16 ou superior;
- PDO com prepared statements;
- arquitetura MVC em PHP puro;
- Composer com autoload PSR-4;
- HTML semântico;
- Tailwind CSS 4 e daisyUI 5 já presentes no projeto;
- JavaScript vanilla em módulos ES;
- Docker Compose para ambiente local com aplicação e PostgreSQL;
- PHPUnit para os testes automatizados;
- variáveis de ambiente para configurações e credenciais;
- Git preservando o histórico existente.

Dependências pequenas e justificadas podem ser usadas, como `vlucas/phpdotenv` para variáveis de ambiente. Não introduza um framework que esconda a arquitetura MVC solicitada.

## 4. Arquitetura desejada

Reorganize o projeto sem destruir os templates e componentes úteis. Use uma estrutura equivalente a esta, adaptando-a quando a base existente justificar outra decisão:

```text
Dolce-Delicias/
├── app/
│   ├── Controllers/
│   │   ├── Site/
│   │   ├── Admin/
│   │   └── AuthController.php
│   ├── Core/
│   │   ├── Application.php
│   │   ├── Router.php
│   │   ├── Controller.php
│   │   ├── Database.php
│   │   ├── View.php
│   │   ├── Session.php
│   │   ├── Csrf.php
│   │   └── Validator.php
│   ├── Models/
│   ├── Repositories/
│   ├── Services/
│   ├── Middleware/
│   └── Support/
├── config/
├── database/
│   ├── migrations/
│   └── seeds/
├── public/
│   ├── index.php
│   ├── assets/
│   └── uploads/
│       ├── produtos/
│       └── catalogos/
├── resources/views/
│   ├── layouts/
│   ├── site/
│   ├── admin/
│   └── components/
├── routes/
│   └── web.php
├── storage/logs/
├── tests/
├── .env.example
├── composer.json
├── docker-compose.yml
└── README.md
```

Requisitos arquiteturais:

- `public/index.php` deve ser o Front Controller.
- O roteador deve aceitar GET e POST e, se necessário, PUT/PATCH/DELETE por `_method`.
- Controllers devem coordenar a requisição, não conter SQL.
- Repositories devem concentrar o acesso com PDO.
- Services devem conter regras de negócio que não pertencem à apresentação.
- Models representam o domínio, mas evite Active Record excessivamente acoplado.
- Views não podem executar SQL.
- Use injeção de dependências simples e explícita.
- Crie tratamento centralizado de exceções e páginas 404/403/422/500 amigáveis.
- Preserve ou adapte os partials atuais como componentes de view, evitando duplicação.

## 5. Modelo de dados PostgreSQL

Use o MER fornecido como base obrigatória. Crie migrações SQL versionadas e reversíveis quando possível. O núcleo deve conter as tabelas abaixo.

### `usuarios`

- `id_usuario BIGSERIAL PRIMARY KEY`
- `nome VARCHAR(120) NOT NULL`
- `login VARCHAR(80) UNIQUE NOT NULL`
- `senha_hash VARCHAR(255) NOT NULL`
- `perfil VARCHAR(20) NOT NULL DEFAULT 'GESTOR' CHECK (perfil IN ('ADMIN','GESTOR'))`
- `ativo BOOLEAN NOT NULL DEFAULT TRUE`
- `criado_em TIMESTAMPTZ NOT NULL DEFAULT NOW()`
- `atualizado_em TIMESTAMPTZ NOT NULL DEFAULT NOW()`

Nunca armazene senha em texto puro. Use `password_hash()` e `password_verify()`.

### `categorias`

- `id_categoria BIGSERIAL PRIMARY KEY`
- `nome VARCHAR(80) UNIQUE NOT NULL`
- `slug VARCHAR(100) UNIQUE NOT NULL`
- `ativa BOOLEAN NOT NULL DEFAULT TRUE`
- timestamps

### `produtos`

- `id_produto BIGSERIAL PRIMARY KEY`
- `id_categoria BIGINT NOT NULL REFERENCES categorias(id_categoria)`
- `nome VARCHAR(120) NOT NULL`
- `slug VARCHAR(140) UNIQUE NOT NULL`
- `descricao TEXT`
- `imagem VARCHAR(255)`
- `preco NUMERIC(10,2) NOT NULL CHECK (preco >= 0)`
- `rotulo_preco VARCHAR(60) NOT NULL DEFAULT 'unidade'`
- `pedido_minimo INTEGER NOT NULL DEFAULT 1 CHECK (pedido_minimo > 0)`
- `passo_quantidade INTEGER NOT NULL DEFAULT 1 CHECK (passo_quantidade > 0)`
- `linha VARCHAR(20) NOT NULL DEFAULT 'ENCOMENDA' CHECK (linha IN ('ENCOMENDA','BALCAO','AMBOS'))`
- `destaque BOOLEAN NOT NULL DEFAULT FALSE`
- `ativo BOOLEAN NOT NULL DEFAULT TRUE`
- `criado_por BIGINT REFERENCES usuarios(id_usuario) ON DELETE SET NULL`
- `atualizado_por BIGINT REFERENCES usuarios(id_usuario) ON DELETE SET NULL`
- timestamps

Se o catálogo atual realmente exigir mais de uma faixa de preço por produto, crie também `produto_precos`, mantendo `produtos.preco` como preço principal ou migrando a regra de forma consistente. Preserve o comportamento atual de quantidade em peças e cálculo de preço unitário.

Para preservar sabores, tags e restrições já existentes, normalize quando fizer sentido:

- `produto_sabores(id_produto_sabor, id_produto, nome, ordem)`;
- `tags(id_tag, nome, slug)`;
- `produto_tags(id_produto, id_tag)`.

### `unidades`

- `id_unidade BIGSERIAL PRIMARY KEY`
- `nome VARCHAR(120) NOT NULL`
- `slug VARCHAR(140) UNIQUE NOT NULL`
- `tipo_unidade VARCHAR(20) NOT NULL CHECK (tipo_unidade IN ('MATRIZ','FILIAL'))`
- `endereco VARCHAR(255)`
- `telefone VARCHAR(20)`
- `whatsapp VARCHAR(20)`
- `horario_funcionamento VARCHAR(120)`
- `tempo_preparo VARCHAR(120)`
- `descricao TEXT`
- `mapa_url VARCHAR(500)`
- `imagem VARCHAR(255)`
- `avaliacao_url VARCHAR(500)`
- `pdf_url VARCHAR(255)`
- `ativa BOOLEAN NOT NULL DEFAULT TRUE`
- `criado_por BIGINT REFERENCES usuarios(id_usuario) ON DELETE SET NULL`
- `atualizado_por BIGINT REFERENCES usuarios(id_usuario) ON DELETE SET NULL`
- timestamps

Garanta no banco que exista no máximo uma unidade do tipo `MATRIZ`, usando índice único parcial.

### `produto_unidade`

- `id_produto_unidade BIGSERIAL PRIMARY KEY`
- `id_produto BIGINT NOT NULL REFERENCES produtos(id_produto) ON DELETE CASCADE`
- `id_unidade BIGINT NOT NULL REFERENCES unidades(id_unidade) ON DELETE CASCADE`
- `disponivel BOOLEAN NOT NULL DEFAULT TRUE`
- `UNIQUE (id_produto, id_unidade)`

O preço é único para toda a rede; essa tabela controla somente disponibilidade.

### `promocoes`

- `id_promocao BIGSERIAL PRIMARY KEY`
- `nome VARCHAR(120) NOT NULL`
- `slug VARCHAR(140) UNIQUE NOT NULL`
- `descricao TEXT`
- `selo VARCHAR(60)`
- `tipo_desconto VARCHAR(20) NOT NULL CHECK (tipo_desconto IN ('PERCENTUAL','VALOR_FIXO'))`
- `valor_desconto NUMERIC(10,2) NOT NULL CHECK (valor_desconto >= 0)`
- `tipo_atendimento VARCHAR(20) NOT NULL CHECK (tipo_atendimento IN ('BALCAO','ENCOMENDA','AMBOS'))`
- `tipo_agenda VARCHAR(20) NOT NULL DEFAULT 'PERIODO' CHECK (tipo_agenda IN ('SEMANAL','MENSAL','PERIODO','SEMPRE'))`
- `dia_semana SMALLINT CHECK (dia_semana BETWEEN 0 AND 6)`
- `mes SMALLINT CHECK (mes BETWEEN 1 AND 12)`
- `data_inicio DATE`
- `data_fim DATE`
- `ativa BOOLEAN NOT NULL DEFAULT TRUE`
- `criado_por BIGINT REFERENCES usuarios(id_usuario) ON DELETE SET NULL`
- `atualizado_por BIGINT REFERENCES usuarios(id_usuario) ON DELETE SET NULL`
- timestamps

Adicione `CHECK` para impedir `data_fim < data_inicio` e valide coerência entre `tipo_agenda` e os campos de agenda.

### `promocao_produto`

- `id_promocao_produto BIGSERIAL PRIMARY KEY`
- `id_promocao BIGINT NOT NULL REFERENCES promocoes(id_promocao) ON DELETE CASCADE`
- `id_produto BIGINT NOT NULL REFERENCES produtos(id_produto) ON DELETE CASCADE`
- `UNIQUE (id_promocao, id_produto)`

### `promocao_unidade`

- `id_promocao_unidade BIGSERIAL PRIMARY KEY`
- `id_promocao BIGINT NOT NULL REFERENCES promocoes(id_promocao) ON DELETE CASCADE`
- `id_unidade BIGINT NOT NULL REFERENCES unidades(id_unidade) ON DELETE CASCADE`
- `UNIQUE (id_promocao, id_unidade)`

Crie índices para FKs, slugs, registros ativos, categorias de produtos e consultas de promoções por período.

## 6. Regras de negócio obrigatórias

1. Deve existir somente uma matriz ativa.
2. O pedido montado no site é enviado somente ao WhatsApp da matriz.
3. As filiais aparecem para consulta, contato direto e download do próprio catálogo PDF.
4. Cada produto pertence a uma categoria.
5. Um produto pode existir em várias unidades e uma unidade pode vender vários produtos.
6. A disponibilidade por unidade é controlada por `produto_unidade`.
7. O preço do produto é o mesmo em todas as unidades.
8. Uma promoção pode incluir vários produtos e valer em várias unidades.
9. Uma promoção define se vale para balcão, encomenda ou ambos.
10. Promoções semanais, como “Quarta do Salgado”, podem ficar visíveis durante a semana para divulgação, mas devem indicar claramente quando estão vigentes.
11. Promoções mensais e por período só devem aparecer quando estiverem dentro da janela configurada.
12. O desconto **não altera o total do carrinho**. O site sempre mostra o preço cheio. A matriz aplica e confirma o desconto ao fechar o pedido no WhatsApp.
13. Quando um produto participar de promoção divulgável, mostre no card um destaque visual com nome, selo, regra, unidades participantes e tipo de atendimento, sem substituir o preço cheio.
14. Produtos, categorias, unidades e promoções inativos não aparecem no site público, mas continuam no banco.
15. Exclusões que possam quebrar histórico ou relacionamento devem preferir desativação. Quando a exclusão física for permitida, respeite as FKs e mostre confirmação no painel.
16. O upload de PDF pertence à unidade. Ao substituir o arquivo, atualize `pdf_url` com segurança e remova o arquivo antigo somente depois de confirmar o novo upload.
17. O painel deve registrar qual usuário criou e alterou produtos, unidades e promoções.

## 7. Site público

Mantenha o design atual e substitua gradualmente os arrays de `data/` por consultas aos repositories.

Implemente as rotas públicas:

```text
GET  /
GET  /produtos
GET  /produtos/{slug}
GET  /unidades
GET  /sobre
GET  /carrinho
GET  /catalogos/{unidadeSlug}/download
```

Preserve:

- home com hero, indicadores, promoções, catálogo e explicação do pedido;
- filtros e pesquisa do catálogo;
- página detalhada do produto;
- carrinho no `localStorage`;
- retirada ou entrega, endereço, forma de pagamento e observação;
- montagem segura da mensagem de WhatsApp;
- comportamento responsivo e acessível;
- metadados básicos de SEO e títulos por página;
- fallback visual quando imagem ou PDF não existir.

Não exponha caminhos internos de arquivos. Downloads de PDF devem passar por uma rota controlada, validar a unidade e enviar cabeçalhos corretos.

## 8. Autenticação e painel administrativo

Implemente as rotas protegidas com prefixo `/admin`.

```text
GET|POST /login
POST     /logout

GET      /admin
GET      /admin/produtos
GET|POST /admin/produtos/novo
GET|POST /admin/produtos/{id}/editar
POST     /admin/produtos/{id}/status
POST     /admin/produtos/{id}/excluir

GET      /admin/categorias
GET|POST /admin/categorias/nova
GET|POST /admin/categorias/{id}/editar
POST     /admin/categorias/{id}/status

GET      /admin/unidades
GET|POST /admin/unidades/nova
GET|POST /admin/unidades/{id}/editar
POST     /admin/unidades/{id}/pdf
POST     /admin/unidades/{id}/status

GET      /admin/promocoes
GET|POST /admin/promocoes/nova
GET|POST /admin/promocoes/{id}/editar
POST     /admin/promocoes/{id}/status

GET      /admin/usuarios
GET|POST /admin/usuarios/novo
GET|POST /admin/usuarios/{id}/editar
POST     /admin/usuarios/{id}/status
```

O dashboard deve exibir pelo menos:

- total de produtos ativos;
- total de categorias;
- total de unidades ativas;
- total de promoções ativas e vigentes;
- produtos indisponíveis;
- atalhos para os cadastros principais.

No formulário de produto, inclua categoria, nome, slug automático editável, descrição, imagem, preço, rótulo do preço, pedido mínimo, passo de quantidade, linha, destaque, ativo, sabores/tags e seleção múltipla das unidades.

No formulário de promoção, inclua nome, slug, descrição, selo, tipo e valor do desconto, atendimento, agenda, período, seleção múltipla de produtos, seleção múltipla de unidades e status.

Use transações ao salvar entidades junto com relacionamentos N:N.

## 9. Segurança obrigatória

- Sessões com cookie `HttpOnly`, `SameSite=Lax` e `Secure` em produção.
- Regenerar o ID da sessão depois do login.
- Middleware de autenticação e autorização.
- Token CSRF em todos os formulários POST/PUT/PATCH/DELETE.
- Prepared statements em todas as consultas.
- Escape de saída HTML com `htmlspecialchars`.
- Validação no servidor, mesmo quando houver validação no navegador.
- Mensagens de erro claras, sem revelar SQL, credenciais ou stack trace em produção.
- Rate limit simples para tentativas de login.
- Uploads aceitando somente tipos esperados, com limite de tamanho e nome aleatório.
- Para imagens, validar MIME real e aceitar apenas JPEG, PNG e WebP.
- Para catálogos, validar MIME e assinatura de PDF.
- Armazenar uploads fora de diretórios executáveis ou bloquear execução neles.
- Impedir path traversal nos downloads e exclusões.
- Nunca versionar `.env`, senhas ou dados secretos.
- Criar `.env.example` sem valores secretos.

## 10. Migração dos dados existentes

Crie seeds idempotentes para transformar os arrays existentes em dados do PostgreSQL:

- categorias derivadas de `data/products.php`;
- produtos, preços, sabores e tags;
- unidades de `data/units.php`;
- disponibilidade de produtos por unidade;
- promoções de `data/promocoes.php`;
- vínculos de promoções com produtos e unidades;
- primeiro usuário administrador, usando credenciais vindas de variáveis de ambiente no momento do seed.

Não apague os arrays originais antes de validar que a importação e as telas funcionam. Depois da migração, mova-os para uma área de legado ou documente claramente que são apenas fonte histórica, evitando duas fontes de verdade em produção.

## 11. Banco, migrations e ambiente local

Entregue:

- `docker-compose.yml` com aplicação PHP e PostgreSQL;
- volume persistente para o banco;
- healthcheck do PostgreSQL;
- configuração por `.env`;
- comando simples de instalação;
- comando para aplicar migrations;
- comando para executar seeds;
- comando para rodar testes;
- comando para compilar o CSS;
- script seguro para aguardar o banco antes das migrations, se necessário.

As migrations devem executar em ordem e registrar quais versões já foram aplicadas. Não dependa de execução manual de SQL pelo pgAdmin.

## 12. Testes mínimos

Crie testes automatizados para:

- autenticação válida e inválida;
- bloqueio de rota administrativa para visitante;
- hash e verificação de senha;
- CRUD ou service principal de produtos;
- vínculo produto-unidade sem duplicidade;
- regra de uma única matriz;
- validação das datas da promoção;
- cálculo de vigência semanal, mensal, por período e sempre;
- filtro de promoções visíveis;
- garantia de que o desconto não altera o total do carrinho;
- geração da mensagem de WhatsApp com os campos do pedido;
- validação de upload de imagem e PDF;
- proteção CSRF.

Quando uma parte em JavaScript não for simples de testar no PHPUnit, extraia a lógica pura e use uma estratégia de teste apropriada, sem adicionar um framework de front-end.

## 13. Qualidade e experiência de uso

- Interface administrativa coerente com a marca, mas mais sóbria e orientada a gestão.
- Feedback de sucesso e erro após ações.
- Confirmação antes de exclusões e desativações importantes.
- Paginação e pesquisa nas listagens administrativas.
- Estados vazios úteis.
- Formulários acessíveis com label, ajuda e mensagens de erro por campo.
- Navegação por teclado, foco visível e contraste adequado.
- Layout responsivo para desktop e celular.
- Código em português ou inglês de maneira consistente; nomes do domínio podem permanecer em português.
- Métodos pequenos, responsabilidades claras e comentários apenas onde explicam uma decisão não óbvia.

## 14. Critérios de aceite

Considere a tarefa concluída somente quando:

1. O projeto sobe com os comandos documentados.
2. A conexão com PostgreSQL funciona por variáveis de ambiente.
3. Todas as migrations e seeds executam em banco vazio.
4. É possível entrar no painel com o administrador inicial.
5. CRUDs de categorias, produtos, unidades, promoções e usuários funcionam.
6. As relações produto-unidade, promoção-produto e promoção-unidade persistem corretamente.
7. O upload e a substituição de imagem e PDF funcionam com validação.
8. Alterações feitas no painel aparecem no site público.
9. O catálogo e o carrinho atual continuam funcionando.
10. O pedido abre o WhatsApp da matriz com mensagem correta.
11. Promoções são exibidas conforme agenda, produtos, unidades e atendimento.
12. O carrinho continua exibindo preço cheio, sem aplicar desconto automaticamente.
13. Rotas administrativas recusam visitantes.
14. CSRF, escaping e prepared statements estão presentes.
15. O layout atual continua responsivo e visualmente consistente.
16. Os testes passam.
17. `README.md` explica instalação, arquitetura, banco, comandos, credenciais iniciais por ambiente e fluxo do sistema.
18. Não existem erros de sintaxe PHP, links internos quebrados ou warnings visíveis.

## 15. Forma de execução e entrega

Trabalhe em etapas verificáveis:

1. Faça a auditoria inicial e resuma o que será reaproveitado.
2. Crie a base MVC, configuração e conexão PDO.
3. Implemente migrations e seeds.
4. Migre o site público para repositories, mantendo o visual.
5. Implemente login, sessão e middlewares.
6. Implemente o painel e os CRUDs.
7. Implemente uploads e relacionamentos.
8. Implemente e valide as promoções.
9. Execute testes, análise de sintaxe e build do CSS.
10. Atualize o README.

Após cada etapa, execute verificações reais. Ao final, informe de forma objetiva:

- o que foi implementado;
- principais decisões arquiteturais;
- migrations e tabelas criadas;
- como iniciar o projeto;
- como criar ou obter o primeiro acesso administrativo;
- testes executados e resultados;
- eventuais limitações que dependem de dados comerciais reais.

Não faça push, deploy ou alterações remotas sem autorização explícita. Não invente resultados de testes: execute-os e apresente a saída real resumida.
