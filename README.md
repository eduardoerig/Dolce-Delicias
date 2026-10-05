# Dolce Delícias — como executar no seu computador

Este pacote contém o site da loja e o painel administrativo, com PHP MVC, Apache e PostgreSQL. O frontend e o backend rodam juntos. Você pode testar tudo localmente, sem contratar hospedagem e sem usar o Railway.

## 1. O que você precisa instalar

- **Windows:** instale o [Docker Desktop](https://docs.docker.com/desktop/setup/install/windows-install/). Siga os requisitos e as etapas da página oficial. Use o backend WSL 2 quando solicitado. Depois da instalação, abra o Docker Desktop e espere o mecanismo iniciar. Este projeto usa containers Linux.
- **Linux:** instale Docker Engine e o plugin Docker Compose para sua distribuição. Consulte a [instalação no Ubuntu](https://docs.docker.com/engine/install/ubuntu/) e o [guia do Compose](https://docs.docker.com/compose/install/). Distribuições derivadas, como o Zorin, podem exigir ajustes específicos na instalação.
- **macOS:** use o [Docker Desktop para Mac](https://docs.docker.com/desktop/setup/install/mac-install/) e mantenha-o aberto.

Com Docker funcionando, não é necessário instalar PHP, Composer, Node.js, PostgreSQL ou pgAdmin separadamente. O build prepara as dependências. A primeira execução precisa de internet para baixar as imagens e os pacotes.

Confira no terminal:

```bash
docker --version
docker compose version
```

Ambos os comandos devem mostrar uma versão. Se algum falhar, resolva a instalação do Docker antes de continuar.

## 2. Extraia o projeto e abra o terminal

1. Baixe `Dolce-Delicias-Para-Colega.zip`.
2. Extraia todos os arquivos. O ZIP contém uma pasta chamada `Dolce-Delicias`.
3. Entre na pasta que contém `Dockerfile`, `docker-compose.yml` e `.env.example`.
4. Abra o terminal nessa pasta.

No Windows, use o PowerShell; pelo Explorador de Arquivos, você pode usar **Abrir no Terminal**. No Linux, use **Abrir no terminal** pelo gerenciador de arquivos. Também é possível abrir a pasta no VS Code e usar **Terminal → Novo Terminal**.

Todos os comandos seguintes devem ser executados na pasta do projeto, um por vez. Abrir os arquivos PHP diretamente no navegador não inicia a aplicação.

## 3. Crie o arquivo de configuração

Copie `.env.example` para um novo arquivo chamado `.env`.

**Windows — PowerShell:**

```powershell
Copy-Item .env.example .env
notepad .env
```

**Linux/macOS:**

```bash
cp .env.example .env
```

Depois abra `.env` em um editor de texto. No Linux, ative a exibição de arquivos ocultos se necessário; normalmente o atalho é **Ctrl + H**.

Configure este conteúdo, substituindo os dois campos de senha antes de salvar:

```dotenv
APP_ENV=local
APP_TIMEZONE=America/Sao_Paulo
DB_HOST=db
DB_PORT=5432
DB_DATABASE=dolce
DB_USERNAME=dolce
DB_PASSWORD=SUBSTITUA_PELA_SENHA_DO_BANCO
ADMIN_NAME=Administrador
ADMIN_LOGIN=admin
ADMIN_PASSWORD=SUBSTITUA_PELA_SENHA_DO_ADMIN
```

- Use senhas diferentes para o banco e o administrador.
- A senha do administrador precisa ter de **12 a 72 caracteres**. Para começar, use letras sem acento e números, com pelo menos 16 caracteres.
- Mantenha `DB_HOST=db`: esse é o nome do serviço PostgreSQL no Docker Compose.
- O arquivo deve se chamar exatamente `.env`, sem a extensão `.txt`.
- Guarde suas senhas e não publique o `.env` no GitHub.

Esta configuração é para uso local. O login administrativo usa o campo `ADMIN_LOGIN`, que neste exemplo é `admin`; não exige e-mail.

## 4. Construa e inicie a aplicação

```bash
docker compose up -d --build
```

A primeira construção pode demorar vários minutos. Espere o comando terminar antes de continuar.

Confira os serviços:

```bash
docker compose ps
```

O serviço `app` deve estar em execução e o serviço `db` deve aparecer saudável, normalmente com a indicação `healthy`.

## 5. Crie as tabelas e os dados iniciais

Primeiro execute:

```bash
docker compose exec app php bin/console.php migrate
```

O resultado deve terminar com `Migrations concluídas.`.

Depois execute:

```bash
docker compose exec app php bin/console.php seed
```

Esse comando importa produtos, categorias e unidades iniciais e cria o administrador com o login e a senha escolhidos no `.env`. O resultado informa que a importação foi concluída. Se já tiver sido executado, poderá informar que o seed já foi aplicado; isso é normal.

## 6. Abra o site e o painel

| Tela | Endereço |
| --- | --- |
| Site público | http://localhost:8000 |
| Login administrativo | http://localhost:8000/login |
| Painel após entrar | http://localhost:8000/admin |
| Promoções após entrar | http://localhost:8000/admin/promocoes |

Entre com `admin` e a senha definida em `ADMIN_PASSWORD`, se manteve o exemplo acima.

No painel você pode testar produtos, categorias, unidades, promoções e usuários, além de enviar imagens e PDFs. Os textos e dados iniciais incluem exemplos: configure os contatos reais da unidade antes de testar o envio de pedidos pelo WhatsApp.

## 7. Faça uma promoção aparecer no site

As promoções iniciais são rascunhos inativos. Para testar:

1. Entre em **Promoções** e adicione ou edite uma campanha.
2. Marque **Ativa**.
3. Defina um desconto válido, por exemplo, percentual de 10%.
4. Para o teste, use atendimento **AMBOS** e agenda **SEMPRE**, sem dias, meses ou datas preenchidos.
5. Vincule um produto ativo cuja categoria também esteja ativa.
6. Vincule uma unidade ativa onde esse produto esteja disponível.
7. Salve e abra http://localhost:8000/#promocoes.

Quando existe uma campanha válida, o site mostra a seção e os links de Promoções. O desconto é informativo: o carrinho mantém o preço cheio, e a oferta é confirmada no atendimento pelo WhatsApp.

## 8. Encerrar e abrir novamente

Para parar os serviços:

```bash
docker compose stop
```

Para iniciar novamente:

```bash
docker compose up -d
```

Não precisa repetir `migrate` e `seed` a cada abertura. Se receber uma atualização do código, reconstrua com `docker compose up -d --build` e execute `migrate` para aplicar eventuais novas migrações.

O Docker mantém banco e uploads em volumes locais. Evite `docker compose down -v` e a remoção desses volumes: isso apaga os dados. Copiar apenas a pasta do código não faz backup dos volumes.

## 9. Erros comuns

| Mensagem ou problema | O que fazer |
| --- | --- |
| `docker: comando não encontrado` ou comando não reconhecido | Instale Docker e Compose; depois reabra o terminal. No Linux, teste também no terminal normal do sistema, fora do editor. |
| `Cannot connect to the Docker daemon` | No Windows/macOS, abra o Docker Desktop e espere iniciar. No Linux, confira se o serviço Docker está em execução. |
| `permission denied` ao acessar o Docker no Linux | Verifique as permissões da instalação. Se seu sistema exigir, use `sudo` antes de cada comando `docker` deste guia. |
| `no configuration file provided` | O terminal está em outra pasta. Entre na pasta que contém `docker-compose.yml`. |
| Erro pedindo `DB_PASSWORD` | Confira se o `.env` existe e se a senha do banco foi preenchida e salva. |
| Porta `8000` ocupada | Altere somente a linha de portas no Compose para `127.0.0.1:8001:80`, execute `docker compose up -d` e use http://localhost:8001. |
| Tabela não existe | Execute o comando `migrate` da etapa 5. |
| Login não funciona | Confirme que executou `seed` e use as credenciais que estavam no `.env` naquele momento. Alterar `ADMIN_PASSWORD` depois não muda a senha de um administrador já criado. |
| Promoções não aparecem | Confira a etapa 7; campanhas inativas ou sem participantes válidos ficam ocultas. |

Para coletar informações quando houver outro erro:

```bash
docker compose ps
docker compose logs app --tail=50
docker compose logs db --tail=50
```

Compartilhe a mensagem de erro com o grupo, removendo eventuais credenciais. Não apague o banco para tentar resolver uma falha de conexão.

## 10. O que este ZIP inclui

Inclui o código completo, o painel, a integração de promoções, as migrações e os dados iniciais. O README técnico anterior foi preservado em `README-TECNICO.md`.

**Não inclui** senhas, `.env`, o banco do computador de Richard ou fotos/PDFs enviados por ele pelo painel. Cada colega terá um banco independente e poderá cadastrar os arquivos na sua própria instalação. Fotos de produto ausentes precisam ser adicionadas no painel; o pacote não recria fotos já cadastradas em outro computador.

`localhost` significa o computador onde você está abrindo o navegador. O link local de Richard não abre a aplicação no computador do colega; o colega precisa iniciar sua própria cópia conforme este guia.

Repositório do grupo: https://github.com/rocharichard061/dolce-delicias. Para acessar pelo GitHub, um repositório privado precisa permitir o acesso do colega; o ZIP pode ser usado sem Git.

## 11. Publicar na Vercel

O site roda na Vercel como uma função PHP (`api/index.php`, runtime `vercel-php`) e usa um Postgres hospedado. As fotos e os PDFs enviados pelo painel ficam no banco (tabela `arquivos`), e a sessão do login também (tabela `sessoes`), porque o disco da Vercel é temporário. Por isso o limite de upload é de 4 MB.

1. Na Vercel, abra o projeto e vá em **Storage → Create Database → Neon (Postgres)**. Escolha a região São Paulo (`sa-east-1`) e ligue o banco ao projeto. A Vercel cria a variável `DATABASE_URL` sozinha.
2. Em **Settings → Environment Variables**, confira se `DATABASE_URL` existe em Production e Preview.
3. Copie o banco local para o novo (ele precisa estar vazio). Pegue a URL em **Storage → o banco → .env.local** e rode, com o Docker ligado:

   ```bash
   DATABASE_URL='cole-a-url-aqui' sh bin/copiar-banco-para-vercel.sh
   ```

   Para começar com o banco vazio em vez de copiar, rode as migrations e o seed apontando para ele:

   ```bash
   docker compose run --rm -e DATABASE_URL='cole-a-url-aqui' app sh -c "php bin/console.php migrate && php bin/console.php seed"
   ```

4. Envie a branch para o GitHub. A Vercel gera o deploy sozinha.

Se você já tinha fotos enviadas pelo painel na versão em disco, rode `php bin/console.php importar-uploads` uma vez antes de copiar o banco.
