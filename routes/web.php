<?php
declare(strict_types=1);
/** @var \App\Core\Router $router */
$router->add('GET','/',fn()=>$site->page('index'));
$router->add('GET','/produtos',fn()=>$site->page('index'));
$router->add('GET','/produtos/{slug}',fn($slug)=>$site->page('produto',$slug));
foreach(['unidades','sobre','carrinho'] as $page)$router->add('GET','/'.$page,fn()=>$site->page($page));
$router->add('GET','/catalogos/{slug}/download',fn($slug)=>$site->pdf($slug));
$router->add('GET','/media/{name}',fn($name)=>$site->media($name));
foreach(['GET','POST'] as $method)$router->add($method,'/login',fn()=>$authController->login());
$router->add('POST','/logout',fn()=>$authController->logout());
$router->add('GET','/admin',fn()=>$admin->dashboard());
foreach(['produtos','categorias','unidades','promocoes','usuarios'] as $entity) {
 $new=\App\Models\Entity::config($entity)['new'];
 $router->add('GET','/admin/'.$entity,fn()=>$admin->listing($entity));
 foreach(['GET','POST'] as $method) {
  $router->add($method,'/admin/'.$entity.'/'.$new,fn()=>$admin->form($entity));
  $router->add($method,'/admin/'.$entity.'/{id}/editar',fn($id)=>$admin->form($entity,ctype_digit($id)?(int)$id:0));
 }
 $router->add('POST','/admin/'.$entity.'/{id}/status',fn($id)=>$admin->status($entity,(int)$id));
}
$router->add('POST','/admin/produtos/{id}/excluir',fn($id)=>$admin->archive((int)$id));
$router->add('POST','/admin/unidades/{id}/pdf',fn($id)=>$admin->pdf((int)$id));
// Old bookmarked URLs remain valid, while all templates use canonical routes.
foreach(['index'=>'','unidades'=>'unidades','sobre'=>'sobre','carrinho'=>'carrinho'] as $old=>$new) {
 $router->add('GET','/'.$old.'.php',function() use($new){header('Location: /'.$new,true,301);});
}
$router->add('GET','/produto.php',function(){header('Location: /produtos/'.rawurlencode(is_string($_GET['slug']??null)?$_GET['slug']:''),true,301);});
