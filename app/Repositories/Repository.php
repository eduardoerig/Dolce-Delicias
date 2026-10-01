<?php
declare(strict_types=1);
namespace App\Repositories;
use PDO;
use App\Models\Entity;
use App\Core\HttpException;
final class Repository {
 public function __construct(public readonly PDO $db) {}
 public function query(string $sql,array $params=[]): \PDOStatement {
  $s=$this->db->prepare($sql);
  foreach($params as $k=>$v) $s->bindValue(is_int($k)?$k+1:':'.$k,$v,is_bool($v)?PDO::PARAM_BOOL:(is_int($v)?PDO::PARAM_INT:($v===null?PDO::PARAM_NULL:PDO::PARAM_STR)));
  $s->execute(); return $s;
 }
 public function all(string $entity): array {Entity::config($entity); return $this->query("SELECT * FROM $entity ORDER BY nome")->fetchAll();}
 public function find(string $entity,int $id): array {
  $pk=Entity::config($entity)['id'];return $this->query("SELECT * FROM $entity WHERE $pk=?",[$id])->fetch()?:throw new HttpException(404,'Registro não encontrado.');
 }
 public function page(string $entity,string $search,int $page): array {
  $pk=Entity::config($entity)['id']; $params=['search'=>'%'.$search.'%'];
  $total=(int)$this->query("SELECT count(*) FROM $entity WHERE nome ILIKE :search",$params)->fetchColumn();
  $rows=$this->query("SELECT * FROM $entity WHERE nome ILIKE :search ORDER BY $pk DESC LIMIT 15 OFFSET :offset",$params+['offset'=>($page-1)*15])->fetchAll();
  return ['rows'=>$rows,'total'=>$total];
 }
 public function save(string $entity,array $data,?int $id): int {
  $c=Entity::config($entity);$pk=$c['id'];
  $allowed=array_merge(array_keys($c['fields']),['senha_hash','imagem','pdf_url','canais','criado_por','atualizado_por']);
  foreach(array_keys($data) as $key) if(!in_array($key,$allowed,true)||$key==='senha') throw new \LogicException('Coluna não permitida.');
  if($id!==null) {
   $set=implode(',',array_map(fn($k)=>"$k=:$k",array_keys($data)));
   $this->query("UPDATE $entity SET $set WHERE $pk=:_id",$data+['_id'=>$id]);return $id;
  }
  $columns=implode(',',array_keys($data));$values=':'.implode(',:',array_keys($data));
  return (int)$this->query("INSERT INTO $entity ($columns) VALUES ($values) RETURNING $pk",$data)->fetchColumn();
 }
 public function selected(string $entity,int $id): array {
  if($entity==='produtos') return [
   'unidades'=>array_column($this->query('SELECT id_unidade FROM produto_unidade WHERE id_produto=? AND disponivel',[$id])->fetchAll(),'id_unidade'),
   'sabores'=>implode("\n",array_column($this->query('SELECT nome FROM produto_sabores WHERE id_produto=? ORDER BY ordem',[$id])->fetchAll(),'nome')),
   'tags'=>implode(', ',array_column($this->query('SELECT t.nome FROM tags t JOIN produto_tags pt USING(id_tag) WHERE pt.id_produto=?',[$id])->fetchAll(),'nome'))];
  if($entity==='promocoes') return [
   'unidades'=>array_column($this->query('SELECT id_unidade FROM promocao_unidade WHERE id_promocao=?',[$id])->fetchAll(),'id_unidade'),
   'produtos'=>array_column($this->query('SELECT id_produto FROM promocao_produto WHERE id_promocao=?',[$id])->fetchAll(),'id_produto')];
  return [];
 }
 public function relations(string $entity,int $id,array $relations): void {
  $links=$entity==='produtos'?[['produto_unidade','id_produto','id_unidade','unidades']]:[['promocao_unidade','id_promocao','id_unidade','unidades'],['promocao_produto','id_promocao','id_produto','produtos']];
  foreach($links as [$table,$owner,$target,$key]) {
   $this->query("DELETE FROM $table WHERE $owner=?",[$id]);
   foreach($relations[$key]??[] as $other) $this->query("INSERT INTO $table ($owner,$target) VALUES (?,?)",[$id,$other]);
  }
  if($entity!=='produtos') return;
  $this->query('DELETE FROM produto_sabores WHERE id_produto=?',[$id]);
  foreach($relations['sabores'] as $order=>$name) $this->query('INSERT INTO produto_sabores (id_produto,nome,ordem) VALUES (?,?,?)',[$id,$name,$order]);
  $this->query('DELETE FROM produto_tags WHERE id_produto=?',[$id]);
  foreach($relations['tags'] as $name) {
   $slug=\App\Core\Validator::slug($name);
   $tag=$this->query('INSERT INTO tags (nome,slug) VALUES (?,?) ON CONFLICT (slug) DO UPDATE SET slug=EXCLUDED.slug RETURNING id_tag',[$name,$slug])->fetchColumn();
   $this->query('INSERT INTO produto_tags VALUES (?,?) ON CONFLICT DO NOTHING',[$id,$tag]);
  }
 }
 public function lockUsers(): void {$this->db->exec('LOCK TABLE usuarios IN SHARE ROW EXCLUSIVE MODE');}
 public function activeAdmins(): int {return (int)$this->query("SELECT count(*) FROM usuarios WHERE ativo AND perfil='ADMIN'")->fetchColumn();}
 public function byLogin(string $login): ?array {return $this->query('SELECT * FROM usuarios WHERE lower(login)=lower(?)',[$login])->fetch()?:null;}
 public function dashboard(): array {
  return $this->query("SELECT (SELECT count(*) FROM produtos WHERE ativo) produtos, (SELECT count(*) FROM categorias) categorias, (SELECT count(*) FROM unidades WHERE ativa) unidades, (SELECT count(*) FROM promocoes WHERE ativa) promocoes, (SELECT count(*) FROM produtos p WHERE ativo AND NOT EXISTS (SELECT 1 FROM produto_unidade pu JOIN unidades u USING(id_unidade) WHERE pu.id_produto=p.id_produto AND pu.disponivel AND u.ativa)) indisponiveis")->fetch();
 }
 public function consumeLoginAttempt(string $key): bool {
  $n=$this->query("INSERT INTO login_tentativas(chave,tentativas,inicio) VALUES (?,1,NOW()) ON CONFLICT(chave) DO UPDATE SET tentativas=CASE WHEN login_tentativas.inicio<NOW()-INTERVAL '15 minutes' THEN 1 ELSE login_tentativas.tentativas+1 END, inicio=CASE WHEN login_tentativas.inicio<NOW()-INTERVAL '15 minutes' THEN NOW() ELSE login_tentativas.inicio END RETURNING tentativas",[$key])->fetchColumn();
  return (int)$n<=10;
 }
}
