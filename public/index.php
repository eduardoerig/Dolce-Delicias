<?php
declare(strict_types=1);
use App\Core\{Database,Router,Session,Csrf,HttpException,View};
use App\Repositories\{Repository,CatalogRepository};
use App\Services\{AuthService,UploadService,CatalogService};
use App\Controllers\{AuthController,AdminController,SiteController};
use App\Middleware\AuthMiddleware;
ob_start();
try {
 require dirname(__DIR__).'/config/bootstrap.php';
 header('X-Content-Type-Options: nosniff');header('X-Frame-Options: DENY');header('Referrer-Policy: strict-origin-when-cross-origin');
 $db=Database::connect();
 $path=parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH)?:'/';
 $isGet=in_array($_SERVER['REQUEST_METHOD'],['GET','HEAD'],true);
 // Sessão (no banco) só no painel e no login: o site público não grava nada por visita.
 if(!$isGet || preg_match('~^/(admin|login|logout)(/|$)~',$path))Session::start($db);
 if(!$isGet)Csrf::check($_POST['_csrf']??null);
 $repo=new Repository($db);$catalog=new CatalogRepository($repo);
 $uploads=new UploadService($db);$auth=new AuthService($repo);
 $site=new SiteController($catalog,$uploads);$authController=new AuthController($auth);
 $admin=new AdminController($repo,$catalog,new CatalogService($repo,$uploads),new AuthMiddleware($auth));
 $router=new Router();require dirname(__DIR__).'/routes/web.php';
 $router->dispatch($_SERVER['REQUEST_METHOD']==='HEAD'?'GET':$_SERVER['REQUEST_METHOD'],$path);
 if($_SERVER['REQUEST_METHOD']==='HEAD')ob_clean();
 ob_end_flush();
} catch(Throwable $e) {
 ob_clean();$status=$e instanceof HttpException?$e->status:500;http_response_code($status);
 if($status===500){$log=dirname(__DIR__).'/storage/logs';if(is_writable($log))error_log((string)$e,3,$log.'/app.log');else error_log((string)$e);}
 $message=$status===500?'Não foi possível concluir agora. Tente novamente em instantes.':$e->getMessage();
 $errors=$e instanceof \App\Core\ValidationException?$e->errors:[];
 if(class_exists(View::class))View::render('error',compact('status','message','errors'));
 else echo 'Aplicação indisponível. Verifique a instalação.';
 ob_end_flush();
}
