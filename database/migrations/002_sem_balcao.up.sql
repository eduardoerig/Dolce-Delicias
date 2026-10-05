-- Sem balcão: tudo é pedido com entrega. O painel deixa de perguntar
-- "Como é vendido" (produtos) e "Vale para" (promoções); as colunas ficam,
-- mas com um valor só, para a interseção promoção × produto continuar
-- valendo (CatalogRepository::promotions).
UPDATE produtos SET linha = 'ENCOMENDA' WHERE linha <> 'ENCOMENDA';
UPDATE promocoes SET tipo_atendimento = 'AMBOS' WHERE tipo_atendimento <> 'AMBOS';
ALTER TABLE promocoes ALTER COLUMN tipo_atendimento SET DEFAULT 'AMBOS';
