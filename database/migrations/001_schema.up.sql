CREATE TABLE usuarios (
 id_usuario BIGSERIAL PRIMARY KEY, nome VARCHAR(120) NOT NULL CHECK (btrim(nome)<>''),
 login VARCHAR(80) NOT NULL UNIQUE, senha_hash VARCHAR(255) NOT NULL,
 perfil VARCHAR(20) NOT NULL DEFAULT 'GESTOR' CHECK(perfil IN ('ADMIN','GESTOR')),
 ativo BOOLEAN NOT NULL DEFAULT TRUE, criado_em TIMESTAMPTZ NOT NULL DEFAULT NOW(), atualizado_em TIMESTAMPTZ NOT NULL DEFAULT NOW()
);
CREATE UNIQUE INDEX usuarios_login_ci ON usuarios(lower(login));
CREATE TABLE categorias (
 id_categoria BIGSERIAL PRIMARY KEY, nome VARCHAR(80) NOT NULL UNIQUE CHECK(btrim(nome)<>''),
 slug VARCHAR(100) NOT NULL UNIQUE CHECK(slug ~ '^[a-z0-9]+(-[a-z0-9]+)*$'), ativa BOOLEAN NOT NULL DEFAULT TRUE, criado_em TIMESTAMPTZ NOT NULL DEFAULT NOW(), atualizado_em TIMESTAMPTZ NOT NULL DEFAULT NOW()
);
CREATE TABLE unidades (
 id_unidade BIGSERIAL PRIMARY KEY, nome VARCHAR(120) NOT NULL CHECK(btrim(nome)<>''),
 slug VARCHAR(140) NOT NULL UNIQUE CHECK(slug ~ '^[a-z0-9]+(-[a-z0-9]+)*$'),
 tipo_unidade VARCHAR(20) NOT NULL CHECK(tipo_unidade IN ('MATRIZ','FILIAL')),
 endereco VARCHAR(255), telefone VARCHAR(20), whatsapp VARCHAR(20), horario_funcionamento VARCHAR(120),
 tempo_preparo VARCHAR(120), descricao TEXT, mapa_url VARCHAR(500), imagem VARCHAR(255), avaliacao_url VARCHAR(500),
 pdf_url VARCHAR(255), canais JSONB NOT NULL DEFAULT '[]', ativa BOOLEAN NOT NULL DEFAULT TRUE, criado_por BIGINT REFERENCES usuarios(id_usuario) ON DELETE SET NULL, atualizado_por BIGINT REFERENCES usuarios(id_usuario) ON DELETE SET NULL, criado_em TIMESTAMPTZ NOT NULL DEFAULT NOW(), atualizado_em TIMESTAMPTZ NOT NULL DEFAULT NOW()
);
CREATE UNIQUE INDEX unica_matriz ON unidades(tipo_unidade) WHERE tipo_unidade='MATRIZ';
CREATE TABLE produtos (
 id_produto BIGSERIAL PRIMARY KEY, id_categoria BIGINT NOT NULL REFERENCES categorias(id_categoria),
 nome VARCHAR(120) NOT NULL CHECK(btrim(nome)<>''), slug VARCHAR(140) NOT NULL UNIQUE CHECK(slug ~ '^[a-z0-9]+(-[a-z0-9]+)*$'),
 descricao TEXT, imagem VARCHAR(255), preco NUMERIC(10,2) NOT NULL CHECK(preco>=0),
 rotulo_preco VARCHAR(60) NOT NULL DEFAULT 'unidade', pedido_minimo INTEGER NOT NULL DEFAULT 1 CHECK(pedido_minimo>0),
 passo_quantidade INTEGER NOT NULL DEFAULT 1 CHECK(passo_quantidade>0),
 linha VARCHAR(20) NOT NULL DEFAULT 'ENCOMENDA' CHECK(linha IN ('ENCOMENDA','BALCAO','AMBOS')),
 destaque BOOLEAN NOT NULL DEFAULT FALSE, ativo BOOLEAN NOT NULL DEFAULT TRUE, criado_por BIGINT REFERENCES usuarios(id_usuario) ON DELETE SET NULL, atualizado_por BIGINT REFERENCES usuarios(id_usuario) ON DELETE SET NULL, criado_em TIMESTAMPTZ NOT NULL DEFAULT NOW(), atualizado_em TIMESTAMPTZ NOT NULL DEFAULT NOW()
);
CREATE TABLE produto_sabores (
 id_produto_sabor BIGSERIAL PRIMARY KEY, id_produto BIGINT NOT NULL REFERENCES produtos ON DELETE CASCADE,
 nome VARCHAR(120) NOT NULL, ordem INTEGER NOT NULL DEFAULT 0, UNIQUE(id_produto,nome)
);
CREATE TABLE tags (id_tag BIGSERIAL PRIMARY KEY, nome VARCHAR(80) NOT NULL UNIQUE, slug VARCHAR(100) NOT NULL UNIQUE);
CREATE TABLE produto_tags (id_produto BIGINT REFERENCES produtos ON DELETE CASCADE, id_tag BIGINT REFERENCES tags ON DELETE CASCADE, PRIMARY KEY(id_produto,id_tag));
CREATE TABLE produto_unidade (
 id_produto_unidade BIGSERIAL PRIMARY KEY, id_produto BIGINT NOT NULL REFERENCES produtos ON DELETE CASCADE,
 id_unidade BIGINT NOT NULL REFERENCES unidades ON DELETE CASCADE, disponivel BOOLEAN NOT NULL DEFAULT TRUE, UNIQUE(id_produto,id_unidade)
);
CREATE TABLE promocoes (
 id_promocao BIGSERIAL PRIMARY KEY, nome VARCHAR(120) NOT NULL CHECK(btrim(nome)<>''), slug VARCHAR(140) NOT NULL UNIQUE CHECK(slug ~ '^[a-z0-9]+(-[a-z0-9]+)*$'), descricao TEXT, selo VARCHAR(60),
 tipo_desconto VARCHAR(20) NOT NULL CHECK(tipo_desconto IN ('PERCENTUAL','VALOR_FIXO')),
 valor_desconto NUMERIC(10,2) NOT NULL CHECK(valor_desconto>=0),
 tipo_atendimento VARCHAR(20) NOT NULL CHECK(tipo_atendimento IN ('BALCAO','ENCOMENDA','AMBOS')),
 tipo_agenda VARCHAR(20) NOT NULL CHECK(tipo_agenda IN ('SEMANAL','MENSAL','PERIODO','SEMPRE')),
 dias_semana SMALLINT[] NOT NULL DEFAULT '{}', meses SMALLINT[] NOT NULL DEFAULT '{}',
 data_inicio DATE, data_fim DATE, ativa BOOLEAN NOT NULL DEFAULT TRUE, criado_por BIGINT REFERENCES usuarios(id_usuario) ON DELETE SET NULL, atualizado_por BIGINT REFERENCES usuarios(id_usuario) ON DELETE SET NULL, criado_em TIMESTAMPTZ NOT NULL DEFAULT NOW(), atualizado_em TIMESTAMPTZ NOT NULL DEFAULT NOW(),
 CHECK(tipo_desconto<>'PERCENTUAL' OR valor_desconto<=100),
 CHECK(data_inicio IS NULL OR data_fim IS NULL OR data_fim>=data_inicio),
 CHECK(dias_semana <@ ARRAY[0,1,2,3,4,5,6]::SMALLINT[] AND array_position(dias_semana,NULL) IS NULL),
 CHECK(meses <@ ARRAY[1,2,3,4,5,6,7,8,9,10,11,12]::SMALLINT[] AND array_position(meses,NULL) IS NULL),
 CHECK(
 (tipo_agenda='SEMANAL' AND cardinality(dias_semana)>0 AND cardinality(meses)=0) OR
 (tipo_agenda='MENSAL' AND cardinality(meses)>0 AND cardinality(dias_semana)=0) OR
 (tipo_agenda='PERIODO' AND cardinality(dias_semana)=0 AND cardinality(meses)=0 AND (data_inicio IS NOT NULL OR data_fim IS NOT NULL)) OR
 (tipo_agenda='SEMPRE' AND cardinality(dias_semana)=0 AND cardinality(meses)=0 AND data_inicio IS NULL AND data_fim IS NULL))
);
CREATE TABLE promocao_produto (
 id_promocao_produto BIGSERIAL PRIMARY KEY, id_promocao BIGINT NOT NULL REFERENCES promocoes ON DELETE CASCADE,
 id_produto BIGINT NOT NULL REFERENCES produtos ON DELETE CASCADE, UNIQUE(id_promocao,id_produto)
);
CREATE TABLE promocao_unidade (
 id_promocao_unidade BIGSERIAL PRIMARY KEY, id_promocao BIGINT NOT NULL REFERENCES promocoes ON DELETE CASCADE,
 id_unidade BIGINT NOT NULL REFERENCES unidades ON DELETE CASCADE, UNIQUE(id_promocao,id_unidade)
);
CREATE TABLE login_tentativas (chave VARCHAR(64) PRIMARY KEY, tentativas INTEGER NOT NULL, inicio TIMESTAMPTZ NOT NULL);
CREATE TABLE seed_versions (versao VARCHAR(80) PRIMARY KEY, aplicada_em TIMESTAMPTZ NOT NULL DEFAULT NOW());
CREATE INDEX produtos_categoria ON produtos(id_categoria);
CREATE INDEX produtos_ativos ON produtos(id_produto) WHERE ativo;
CREATE INDEX categorias_ativas ON categorias(id_categoria) WHERE ativa;
CREATE INDEX unidades_ativas ON unidades(id_unidade) WHERE ativa;
CREATE INDEX promocoes_agenda ON promocoes(data_inicio,data_fim) WHERE ativa;
CREATE INDEX produto_unidade_unidade ON produto_unidade(id_unidade);
CREATE INDEX produto_tags_tag ON produto_tags(id_tag);
CREATE INDEX promocao_produto_produto ON promocao_produto(id_produto);
CREATE INDEX promocao_unidade_unidade ON promocao_unidade(id_unidade);
CREATE FUNCTION touch_timestamp() RETURNS trigger LANGUAGE plpgsql AS $$
BEGIN NEW.atualizado_em=NOW(); RETURN NEW; END; $$;
CREATE FUNCTION validar_matriz() RETURNS trigger LANGUAGE plpgsql AS $$
BEGIN
 IF EXISTS(SELECT 1 FROM unidades) AND (SELECT count(*) FROM unidades WHERE tipo_unidade='MATRIZ' AND ativa)<>1 THEN
  RAISE EXCEPTION 'É necessário manter uma matriz ativa' USING ERRCODE='23514';
 END IF; RETURN NULL;
END; $$;
CREATE CONSTRAINT TRIGGER matriz_ativa AFTER INSERT OR UPDATE OR DELETE ON unidades DEFERRABLE INITIALLY DEFERRED FOR EACH ROW EXECUTE FUNCTION validar_matriz();
CREATE TRIGGER usuarios_timestamp BEFORE UPDATE ON usuarios FOR EACH ROW EXECUTE FUNCTION touch_timestamp();
CREATE TRIGGER categorias_timestamp BEFORE UPDATE ON categorias FOR EACH ROW EXECUTE FUNCTION touch_timestamp();
CREATE TRIGGER produtos_timestamp BEFORE UPDATE ON produtos FOR EACH ROW EXECUTE FUNCTION touch_timestamp();
CREATE TRIGGER unidades_timestamp BEFORE UPDATE ON unidades FOR EACH ROW EXECUTE FUNCTION touch_timestamp();
CREATE TRIGGER promocoes_timestamp BEFORE UPDATE ON promocoes FOR EACH ROW EXECUTE FUNCTION touch_timestamp();
CREATE INDEX produtos_criado_por ON produtos(criado_por);
CREATE INDEX produtos_atualizado_por ON produtos(atualizado_por);
CREATE INDEX unidades_criado_por ON unidades(criado_por);
CREATE INDEX unidades_atualizado_por ON unidades(atualizado_por);
CREATE INDEX promocoes_criado_por ON promocoes(criado_por);
CREATE INDEX promocoes_atualizado_por ON promocoes(atualizado_por);
