-- Volta só o esquema. Os valores antigos de linha/tipo_atendimento não são
-- restaurados: cadastre de novo no painel, se o balcão voltar.
ALTER TABLE promocoes ALTER COLUMN tipo_atendimento DROP DEFAULT;
