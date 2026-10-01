<?php
declare(strict_types=1);
use App\Core\Validator;
$db->beginTransaction();
try {
 $login=(string)($_ENV['ADMIN_LOGIN']??getenv('ADMIN_LOGIN')?:'');
 $password=(string)($_ENV['ADMIN_PASSWORD']??getenv('ADMIN_PASSWORD')?:'');
 if($repo->activeAdmins()===0) {
  $u=Validator::validate('usuarios',['nome'=>$_ENV['ADMIN_NAME']??'Administrador','login'=>$login,'senha'=>$password,'perfil'=>'ADMIN','ativo'=>'1'],null);
  $u['senha_hash']=password_hash($u['senha'],PASSWORD_DEFAULT);unset($u['senha']);$admin=$repo->save('usuarios',$u,null);
 }else{$admin=(int)$repo->query("SELECT id_usuario FROM usuarios WHERE perfil='ADMIN' AND ativo ORDER BY id_usuario LIMIT 1")->fetchColumn();}
 if($repo->query("SELECT 1 FROM seed_versions WHERE versao='legacy-v1'")->fetchColumn()) {
  $db->commit();echo "Seed já aplicado; alterações do painel preservadas.\n";return;
 }
 $units=[];
 foreach(require DD_BASE.'/data/units.php' as $u) {
  $existing=$repo->query('SELECT id_unidade FROM unidades WHERE slug=?',[$u['slug']])->fetchColumn();
  $units[$u['slug']]=$existing?(int)$existing:$repo->save('unidades',[
   'nome'=>$u['nome'],'slug'=>$u['slug'],'tipo_unidade'=>$u['matriz']?'MATRIZ':'FILIAL','endereco'=>$u['endereco'],
   'whatsapp'=>$u['whatsapp'],'horario_funcionamento'=>$u['horario'],'tempo_preparo'=>$u['preparo'],'descricao'=>$u['sobre'],
   'mapa_url'=>$u['mapaUrl'],'imagem'=>$u['imagem'],'avaliacao_url'=>$u['avaliacao'],
   'pdf_url'=>is_file(DD_BASE.$u['catalogoPdf'])?'legacy:'.basename($u['catalogoPdf']):null,
   'canais'=>json_encode($u['canais'],JSON_THROW_ON_ERROR),'ativa'=>true,'criado_por'=>$admin,'atualizado_por'=>$admin
  ],null);
 }
 foreach(require DD_BASE.'/data/products.php' as $p) {
  if(count($p['precos'])!==1)throw new RuntimeException('Mais de uma faixa detectada: adapte produto_precos antes de importar.');
  $category=(int)$repo->query('INSERT INTO categorias(nome,slug) VALUES (?,?) ON CONFLICT(nome) DO UPDATE SET nome=EXCLUDED.nome RETURNING id_categoria',[$p['categoria'],Validator::slug($p['categoria'])])->fetchColumn();
  if($repo->query('SELECT 1 FROM produtos WHERE slug=?',[$p['slug']])->fetchColumn())continue;
  $price=dd_faixa($p['precos'][0]);
  $id=$repo->save('produtos',[
   'id_categoria'=>$category,'nome'=>$p['nome'],'slug'=>$p['slug'],'descricao'=>$p['descricao'],'imagem'=>$p['imagem'],
   'preco'=>$price['valor'],'rotulo_preco'=>$price['por'],'pedido_minimo'=>$price['min'],'passo_quantidade'=>$price['passo'],
   'linha'=>in_array('atacado',$p['tags'],true)?'ENCOMENDA':'BALCAO','destaque'=>$p['destaque'],'ativo'=>true,'criado_por'=>$admin,'atualizado_por'=>$admin
  ],null);
  $ids=array_map(fn($slug)=>$units[$slug],$p['unidades']?:array_keys($units));
  $repo->relations('produtos',$id,['unidades'=>$ids,'sabores'=>$p['sabores']??[],'tags'=>$p['tags']??[]]);
  if(!($p['disponivel']??true))$repo->query('UPDATE produto_unidade SET disponivel=FALSE WHERE id_produto=?',[$id]);
 }
 foreach(require DD_BASE.'/data/promocoes.php' as $p) {
  if($repo->query('SELECT 1 FROM promocoes WHERE slug=?',[$p['slug']])->fetchColumn())continue;
  $q=$p['quando'];
  // Legacy campaigns do not identify discounts/products/units. Preserve drafts, not invented offers.
  $repo->save('promocoes',[
   'nome'=>$p['titulo'],'slug'=>$p['slug'],'descricao'=>$p['texto'],'selo'=>$p['selo'],
   'tipo_desconto'=>'PERCENTUAL','valor_desconto'=>'0','tipo_atendimento'=>'AMBOS','tipo_agenda'=>strtoupper($q['tipo']),
   'dias_semana'=>'{'.implode(',',$q['dias']??[]).'}','meses'=>'{'.implode(',',$q['meses']??[]).'}',
   'data_inicio'=>$q['de']??null,'data_fim'=>$q['ate']??null,'ativa'=>false,'criado_por'=>$admin,'atualizado_por'=>$admin
  ],null);
 }
 $repo->query("INSERT INTO seed_versions(versao) VALUES ('legacy-v1')");$db->commit();
 echo "Importação concluída. Promoções legadas salvas como rascunhos inativos: complete desconto e participantes no painel.\n";
} catch(Throwable $e){if($db->inTransaction())$db->rollBack();throw $e;}
