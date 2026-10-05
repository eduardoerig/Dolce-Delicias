-- Para rodar na Vercel, onde o disco é temporário e cada requisição pode cair
-- numa instância diferente: fotos/PDFs do painel e a sessão de login vão para o banco.
CREATE TABLE arquivos (
 nome VARCHAR(60) PRIMARY KEY,
 tipo VARCHAR(40) NOT NULL,
 tamanho INTEGER NOT NULL CHECK (tamanho > 0),
 conteudo BYTEA NOT NULL,
 criado_em TIMESTAMPTZ NOT NULL DEFAULT NOW()
);
CREATE TABLE sessoes (
 id VARCHAR(128) PRIMARY KEY,
 dados TEXT NOT NULL,
 expira_em TIMESTAMPTZ NOT NULL
);
CREATE INDEX sessoes_expira_em ON sessoes (expira_em);
