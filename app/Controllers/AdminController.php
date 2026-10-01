<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\{View,Session,ValidationException};
use App\Models\{Entity,Promotion};
use App\Repositories\{Repository,CatalogRepository};
use App\Services\CatalogService;
use App\Middleware\AuthMiddleware;
final class AdminController {
 private const PER_PAGE=25;
 /** Filtros aceitos por cadastro, além de todos/ativos/inativos. */
 private const EXTRA_FILTERS=['produtos'=>['sem-foto'=>'Sem foto','indisponiveis'=>'Sem unidade'],'unidades'=>['sem-foto'=>'Sem foto']];
 public function __construct(private Repository $repo,private CatalogRepository $catalog,private CatalogService $service,private AuthMiddleware $auth) {}
 public function dashboard(): void {
  $user=$this->auth->requireUser();$stats=$this->repo->dashboard();$today=new \DateTimeImmutable('today');
  $promos=$this->catalog->promotions(false);
  $checks=array_map(fn($p)=>Promotion::checklist($p,$today),$promos);
  $stats['no_site']=count(array_filter($checks,fn($c)=>$c['no_site']));
  $attention=[];
  $units=$this->repo->all('unidades');
  $matriz=array_values(array_filter($units,fn($u)=>$u['tipo_unidade']==='MATRIZ' && $u['ativa']))[0]??null;
  if(!$matriz) $attention[]=['danger','Nenhuma matriz ativa','Os pedidos do site precisam de uma matriz ativa com WhatsApp.','/admin/unidades','Ver unidades'];
  elseif(self::placeholderPhone($matriz['whatsapp']??'')) $attention[]=['danger','WhatsApp da matriz não configurado','Os pedidos do site estão indo para um número de exemplo.','/admin/unidades/'.$matriz['id_unidade'].'/editar','Corrigir agora'];
  $sample=count(array_filter($units,fn($u)=>$u['ativa'] && (str_contains(mb_strtoupper($u['nome'].' '.($u['endereco']??'')),'PREENCHER'))));
  if($sample) $attention[]=['warning',$sample.($sample>1?' unidades com dados de exemplo':' unidade com dados de exemplo'),'Troque nome e endereço pelos dados reais.','/admin/unidades','Revisar'];
  $noPhoto=count(array_filter($this->repo->activeProducts(),fn($p)=>!dd_imagem($p['imagem'])));
  if($noPhoto) $attention[]=['warning',$noPhoto.($noPhoto>1?' produtos sem foto':' produto sem foto'),'Produtos com foto vendem mais.','/admin/produtos?filtro=sem-foto','Adicionar fotos'];
  if($stats['indisponiveis']) $attention[]=['warning',$stats['indisponiveis'].($stats['indisponiveis']>1?' produtos sem unidade':' produto sem unidade'),'Não aparecem para compra porque nenhuma unidade ativa vende.','/admin/produtos?filtro=indisponiveis','Resolver'];
  $hidden=0;foreach($promos as $i=>$p) if($p['ativa'] && !$checks[$i]['no_site'])$hidden++;
  if($hidden) $attention[]=['warning',$hidden.($hidden>1?' promoções ativas fora do site':' promoção ativa fora do site'),'Falta produto, unidade ou estão fora da agenda.','/admin/promocoes?filtro=ativos','Ver o que falta'];
  View::render('admin/dashboard',compact('user','stats','attention'));
 }
 public function listing(string $entity): void {
  $config=Entity::config($entity);$user=$this->auth->requireUser($entity==='usuarios');
  $search=is_string($_GET['q']??'')?trim(mb_substr($_GET['q']??'',0,120)):'';$page=max(1,min(100000,(int)($_GET['page']??1)));
  $filters=['todos'=>'Todos','ativos'=>'Ativos','inativos'=>'Inativos']+(self::EXTRA_FILTERS[$entity]??[]);
  $filter=is_string($_GET['filtro']??null) && isset($filters[$_GET['filtro']])?$_GET['filtro']:'todos';
  $rows=$this->repo->listing($entity);$status=$config['status'];
  $siteStatus=[];
  if($entity==='promocoes') foreach($this->catalog->promotions(false) as $p) $siteStatus[$p['id_promocao']]=Promotion::checklist($p,new \DateTimeImmutable('today'))['no_site'];
  $counts=[];
  foreach($filters as $key=>$_) $counts[$key]=count(array_filter($rows,fn($r)=>self::matches($r,$key,$status)));
  if($search!=='') $rows=array_filter($rows,fn($r)=>str_contains(mb_strtolower(dd_ascii($r['nome'].' '.($r['login']??'').' '.($r['categoria']??''))),mb_strtolower(dd_ascii($search))));
  $rows=array_values(array_filter($rows,fn($r)=>self::matches($r,$filter,$status)));
  $total=count($rows);$rows=array_slice($rows,($page-1)*self::PER_PAGE,self::PER_PAGE);
  $result=['rows'=>$rows,'total'=>$total,'pages'=>max(1,(int)ceil($total/self::PER_PAGE))];
  View::render('admin/list',compact('entity','config','user','search','page','result','filters','filter','counts','siteStatus'));
 }
 public function form(string $entity,?int $id=null): void {
  $config=Entity::config($entity);$user=$this->auth->requireUser($entity==='usuarios');$errors=[];
  $record=$id?$this->repo->find($entity,$id)+$this->repo->selected($entity,$id):[];
  foreach(['dias_semana','meses'] as $key)if(isset($record[$key]))$record[$key]=trim($record[$key],'{}');
  if($_SERVER['REQUEST_METHOD']==='POST') {
   $input=self::normalize($entity,$_POST);
   try {
    $saved=$this->service->save($entity,$input,$id,(int)$user['id_usuario'],$_FILES);
    $_SESSION['flash']=$id?'Alterações salvas.':'Cadastro salvo.';
    Session::redirect('/admin/'.$entity.'/'.$saved.'/editar');
   }catch(ValidationException $e){$errors=$e->errors;$record=$input+($id?['imagem'=>$record['imagem']??null,'pdf_url'=>$record['pdf_url']??null]:[]);http_response_code(422);}
  }
  unset($record['senha_hash']);
  $choices=['categorias'=>$this->repo->all('categorias'),'unidades'=>$this->repo->all('unidades'),'produtos'=>$this->repo->all('produtos')];
  $checklist=null;
  if($entity==='promocoes' && $id) foreach($this->catalog->promotions(false) as $p) if((int)$p['id_promocao']===$id) $checklist=Promotion::checklist($p,new \DateTimeImmutable('today'));
  View::render('admin/form',compact('entity','config','user','id','record','errors','choices','checklist'));
 }
 public function status(string $entity,int $id): void {
  $user=$this->auth->requireUser($entity==='usuarios');
  $this->service->status($entity,$id,(int)$user['id_usuario']);
  $row=$this->repo->find($entity,$id);
  $config=Entity::config($entity);$suffix=$config['new']==='nova'?'a.':'o.';
  $_SESSION['flash']=$row['nome'].' '.($row[$config['status']]?'ativad':'desativad').$suffix;
  $back=is_string($_POST['voltar']??null)?$_POST['voltar']:'';
  Session::redirect(preg_match('~^/admin/'.$entity.'(\?[\w=&%+.-]*)?$~D',$back)?$back:'/admin/'.$entity);
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
 /** Converte o que os controles do formulário enviam para o formato que o Validator espera. */
 private static function normalize(string $entity,array $input): array {
  foreach(['dias_semana','meses'] as $key) if(isset($input[$key]) && is_array($input[$key])) $input[$key]=implode(',',array_filter($input[$key],fn($v)=>is_string($v)&&ctype_digit($v)));
  if($entity==='promocoes') {
   // Campos de agendas não escolhidas ficam escondidos; descartá-los evita erro por sobra.
   $type=$input['tipo_agenda']??'';
   if($type!=='SEMANAL')$input['dias_semana']='';
   if($type!=='MENSAL')$input['meses']='';
   if($type==='SEMPRE'){$input['data_inicio']='';$input['data_fim']='';}
  }
  if($entity==='unidades' && is_string($input['whatsapp']??null)) $input['whatsapp']=preg_replace('/\D+/','',$input['whatsapp']);
  foreach(['preco','valor_desconto'] as $key) if(is_string($input[$key]??null)) $input[$key]=str_replace([' ','R$','%'],'',$input[$key]);
  return $input;
 }
 private static function matches(array $row,string $filter,string $status): bool {
  return match($filter) {
   'ativos'=>(bool)$row[$status],
   'inativos'=>!$row[$status],
   'sem-foto'=>(bool)$row[$status] && !dd_imagem($row['imagem']??null),
   'indisponiveis'=>(bool)$row[$status] && !empty($row['indisponivel']),
   default=>true,
  };
 }
 /** Número vazio ou de exemplo (ex.: 55000000000). */
 private static function placeholderPhone(string $phone): bool {return !preg_match('/^\d{10,15}$/D',$phone) || preg_match('/^\d{0,3}0{8,}$/D',$phone)===1;}
}
