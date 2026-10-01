<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\{Validator,ValidationException};
use App\Repositories\Repository;
use App\Models\Entity;
final class CatalogService {
 public function __construct(private Repository $repo,private UploadService $uploads) {}
 public function save(string $entity,array $input,?int $id,int $actor,array $files=[]): int {
  $data=Validator::validate($entity,$input,$id);$relations=[];
  if(in_array($entity,['produtos','promocoes'],true)) {
   $relations['unidades']=Validator::ids($input['unidades']??[]);
   if($entity==='promocoes') {
    $relations['produtos']=Validator::ids($input['produtos']??[]);
    if(!$relations['unidades']||!$relations['produtos']) throw new ValidationException(['relacoes'=>'Selecione pelo menos um produto e uma unidade.']);
   } else {
    foreach(['sabores','tags'] as $key) {
     if(!is_string($input[$key]??'')) throw new ValidationException([$key=>'Texto inválido.']);
     $relations[$key]=array_values(array_unique(array_filter(array_map('trim',preg_split($key==='tags'?'/,/':'/\R/', $input[$key]??'')))));
     if(count($relations[$key])>100) throw new ValidationException([$key=>'Máximo de 100 itens.']);
     foreach($relations[$key] as $name) if(mb_strlen($name)>($key==='tags'?80:120)||Validator::slug($name)==='') throw new ValidationException([$key=>'Nome inválido ou muito longo.']);
    }
   }
  }
  if($entity==='usuarios') {
   if($data['senha']!=='') $data['senha_hash']=password_hash($data['senha'],PASSWORD_DEFAULT);
   unset($data['senha']);
  }
  if($entity==='promocoes') foreach(['dias_semana','meses'] as $key)$data[$key]='{'.implode(',',$data[$key]).'}';
  if(in_array($entity,['produtos','unidades','promocoes'],true)) {
   $data['atualizado_por']=$actor;if($id===null)$data['criado_por']=$actor;
  }
  $createdFiles=[];$oldFiles=[];
  $this->repo->db->beginTransaction();
  try {
   if($entity==='usuarios') $this->repo->lockUsers();
   $old=$id?$this->repo->find($entity,$id):[];
   if($entity==='usuarios' && $id) {
    if($id===$actor && (!$data['ativo']||$data['perfil']!=='ADMIN')) throw new ValidationException(['perfil'=>'Você não pode desativar ou rebaixar sua própria conta.']);
    if($old['ativo'] && $old['perfil']==='ADMIN' && (!$data['ativo']||$data['perfil']!=='ADMIN') && $this->repo->activeAdmins()<=1) throw new ValidationException(['perfil'=>'Mantenha pelo menos um administrador ativo.']);
   }
   if(in_array($entity,['produtos','unidades'],true)) {
    foreach(['imagem'=>'image','pdf_url'=>'pdf'] as $column=>$kind) {
     if($column==='pdf_url' && $entity!=='unidades')continue;
     $new=$this->uploads->store($files[$column]??[],$kind);
     if($new) {
      $createdFiles[]=$new;$oldFiles[]=isset($old[$column])?basename($old[$column]):null;
      $data[$column]=$kind==='image'?'/media/'.$new:$new;
     }
    }
   }
   $saved=$this->repo->save($entity,$data,$id);
   if($relations) $this->repo->relations($entity,$saved,$relations);
   $this->repo->db->commit();
  } catch(\Throwable $e) {
   if($this->repo->db->inTransaction())$this->repo->db->rollBack();
   foreach($createdFiles as $file)$this->uploads->remove($file);
   if($e instanceof \PDOException && in_array($e->getCode(),['23505','23503','23514','22001'],true)) throw new ValidationException(['cadastro'=>'Verifique os valores únicos, os vínculos e a regra de uma matriz ativa.']);
   throw $e;
  }
  foreach($oldFiles as $file)$this->uploads->remove($file);
  return $saved;
 }
 public function status(string $entity,int $id,int $actor): void {
  $old=$this->repo->find($entity,$id);$config=Entity::config($entity);
  $input=$old+$this->repo->selected($entity,$id);
  $input[$config['status']]=!$old[$config['status']];
  foreach($config['fields'] as $key=>$field) {
   if($field[1]==='bool') {if($input[$key])$input[$key]='1';else unset($input[$key]);}
   if(in_array($field[1],['days','months'],true))$input[$key]=trim($input[$key],'{}');
  }
  $input['senha']='';
  $this->save($entity,$input,$id,$actor);
 }
 public function pdf(int $id,array $file,int $actor): void {
  $new=$this->uploads->store($file,'pdf');
  if(!$new)throw new ValidationException(['arquivo'=>'Selecione um PDF.']);
  $this->repo->db->beginTransaction();
  try {
   $old=$this->repo->find('unidades',$id);
   $this->repo->save('unidades',['pdf_url'=>$new,'atualizado_por'=>$actor],$id);
   $this->repo->db->commit();
  }catch(\Throwable $e){if($this->repo->db->inTransaction())$this->repo->db->rollBack();$this->uploads->remove($new);throw $e;}
  $this->uploads->remove($old['pdf_url']);
 }
}
