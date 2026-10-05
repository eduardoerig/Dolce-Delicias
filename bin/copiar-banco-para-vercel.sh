#!/bin/sh
# Copia o banco local (Docker) para o Postgres da Vercel (Neon/Supabase), que
# precisa estar VAZIO. Leva produtos, unidades, promoções, usuários e fotos.
#
# Uso (na pasta do projeto, com o Docker rodando):
#   DATABASE_URL='postgres://...' sh bin/copiar-banco-para-vercel.sh
set -eu
: "${DATABASE_URL:?Defina DATABASE_URL com a URL do banco da Vercel}"
docker compose exec -T -e DATABASE_URL db sh -c '
  set -eu
  tabelas=$(psql "$DATABASE_URL" -tAc "select count(*) from information_schema.tables where table_schema='"'"'public'"'"'")
  if [ "$tabelas" != "0" ]; then echo "O banco de destino já tem $tabelas tabela(s). Use um banco vazio." >&2; exit 1; fi
  pg_dump -U "$POSTGRES_USER" -d "$POSTGRES_DB" --no-owner --no-privileges \
    --exclude-table-data=sessoes --exclude-table-data=login_tentativas \
  | psql "$DATABASE_URL" -v ON_ERROR_STOP=1 -q
  echo "Banco copiado."
'
