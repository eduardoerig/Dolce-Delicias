<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\{View,Session,ValidationException};
use App\Models\{Entity,Promotion};
use App\Repositories\{Repository,CatalogRepository};
use App\Services\CatalogService;
use App\Middleware\AuthMiddleware;
final class AdminController {
 public function __construct(private Repository $repo,private CatalogRepository $catalog,private CatalogService $service,private AuthMiddleware $auth) {}
 public function dashboard(): void {
  $user=$this->auth->requireUser();$stats=$this->repo->dashboard();
  $stats['vigentes']=count(array_filter($this->catalog->promotions(false),fn($p)=>Promotion::vigente($p,new \DateTimeImmutable('today'))));
  View::render('admin/dashboard',compact('user','stats'));
 }
 public function listing(string $entity): void {
  $config=Entity::config($entity);$user=$this->auth->requireUser($entity==='usuarios');
  $search=is_string($_GET['q']??'')?mb_substr($_GET['q']??'',0,120):'';$page=max(1,min(100000,(int)($_GET['page']??1)));
  $result=$this->repo->page($entity,$search,$page);
  View::render('admin/list',compact('entity','config','user','search','page','result'));
 }
 public function form(string $entity,?int $id=null): void {
  $config=Entity::config($entity);$user=$this->auth->requireUser($entity==='usuarios');$errors=[];
  $record=$id?$this->repo->find($entity,$id)+$this->repo->selected($entity,$id):[];
  foreach(['dias_semana','meses'] as $key)if(isset($record[$key]))$record[$key]=trim($record[$key],'{}');
  if($_SERVER['REQUEST_METHOD']==='POST') {
   try {
    $saved=$this->service->save($entity,$_POST,$id,(int)$user['id_usuario'],$_FILES);
    $_SESSION['flash']='Cadastro salvo com sucesso.';Session::redirect('/admin/'.$entity.'/'.$saved.'/editar');
   }catch(ValidationException $e){$errors=$e->errors;$record=$_POST;http_response_code(422);}
  }
  unset($record['senha_hash']);
  $choices=['categorias'=>$this->repo->all('categorias'),'unidades'=>$this->repo->all('unidades'),'produtos'=>$this->repo->all('produtos')];
  View::render('admin/form',compact('entity','config','user','id','record','errors','choices'));
 }
 public function status(string $entity,int $id): void {
  $user=$this->auth->requireUser($entity==='usuarios');
  $this->service->status($entity,$id,(int)$user['id_usuario']);
  $_SESSION['flash']='Status atualizado.';Session::redirect('/admin/'.$entity);
 }
 public function archive(int $id): void {
  $this->auth->requireUser();$p=$this->repo->find('produtos',$id);
  if($p['ativo'])$this->status('produtos',$id);
  $_SESSION['flash']='Produto já desativado. Os vínculos foram preservados.';Session::redirect('/admin/produtos');
 }
 public function pdf(int $id): void {
  $u=$this->auth->requireUser();$this->service->pdf($id,$_FILES['pdf_url']??[],(int)$u['id_usuario']);
  $_SESSION['flash']='Catálogo substituído.';Session::redirect('/admin/unidades/'.$id.'/editar');
 }
}
