import {PGlite} from '@electric-sql/pglite';
import {readFileSync} from 'node:fs';
import assert from 'node:assert/strict';
const db=await PGlite.create();const root=new URL('..',import.meta.url).pathname.replace(/\/$/, '');
await db.exec(readFileSync(root+'/database/migrations/001_schema.up.sql','utf8'));
await db.exec("INSERT INTO usuarios(nome,login,senha_hash)VALUES('Teste','teste','hash'); INSERT INTO categorias(nome,slug)VALUES('Categoria','categoria'); INSERT INTO unidades(nome,slug,tipo_unidade) VALUES('Matriz','matriz','MATRIZ'); INSERT INTO produtos(id_categoria,nome,slug,preco)VALUES(1,'Produto','produto',120); INSERT INTO produto_unidade(id_produto,id_unidade)VALUES(1,1)");
for(const [sql,code] of [
 ["INSERT INTO unidades(nome,slug,tipo_unidade)VALUES('Outra','outra','MATRIZ')",'23505'],
 ["UPDATE unidades SET ativa=false WHERE id_unidade=1",'23514'],
 ["INSERT INTO produto_unidade(id_produto,id_unidade)VALUES(1,1)",'23505'],
 ["INSERT INTO promocao_produto(id_promocao,id_produto)VALUES(999,1)",'23503'],
 ["INSERT INTO promocoes(nome,slug,tipo_desconto,valor_desconto,tipo_atendimento,tipo_agenda,data_inicio,data_fim)VALUES('Oferta','oferta','PERCENTUAL',101,'AMBOS','PERIODO','2026-01-01','2026-02-01')",'23514'],
 ["INSERT INTO promocoes(nome,slug,tipo_desconto,valor_desconto,tipo_atendimento,tipo_agenda,data_inicio,data_fim)VALUES('Oferta','oferta','PERCENTUAL',10,'AMBOS','PERIODO','2026-02-01','2026-01-01')",'23514'],
 ["INSERT INTO promocoes(nome,slug,tipo_desconto,valor_desconto,tipo_atendimento,tipo_agenda,dias_semana)VALUES('Oferta','oferta','PERCENTUAL',10,'AMBOS','SEMANAL',ARRAY[7]::smallint[])",'23514']
]){await assert.rejects(()=>db.exec(sql),e=>e.code===code);}
await db.exec('BEGIN');await db.exec("INSERT INTO categorias(nome,slug)VALUES('Temporaria','temporaria')");await assert.rejects(()=>db.exec('INSERT INTO produto_unidade(id_produto,id_unidade)VALUES(1,999)'));await db.exec('ROLLBACK');assert.equal((await db.query("SELECT count(*)::int n FROM categorias WHERE slug='temporaria'")).rows[0].n,0);
await db.exec(readFileSync(root+'/database/migrations/001_schema.down.sql','utf8'));
await db.exec(readFileSync(root+'/database/migrations/001_schema.up.sql','utf8'));
console.log('SQL: 7 restrições, rollback transacional, migration down e reaplicação aprovados.');await db.close();
