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
 Session::start();
 header('X-Content-Type-Options: nosniff');header('X-Frame-Options: DENY');header('Referrer-Policy: strict-origin-when-cross-origin');
 if($_SERVER['REQUEST_METHOD']!=='GET' && $_SERVER['REQUEST_METHOD']!=='HEAD')Csrf::check($_POST['_csrf']??null);
 $repo=new Repository(Database::connect());$catalog=new CatalogRepository($repo);
 $uploads=new UploadService(dirname(__DIR__).'/storage/uploads');$auth=new AuthService($repo);
 $site=new SiteController($catalog,$uploads);$authController=new AuthController($auth);
 $admin=new AdminController($repo,$catalog,new CatalogService($repo,$uploads),new AuthMiddleware($auth));
 $router=new Router();require dirname(__DIR__).'/routes/web.php';
 $path=parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH)?:'/';
 $router->dispatch($_SERVER['REQUEST_METHOD']==='HEAD'?'GET':$_SERVER['REQUEST_METHOD'],$path);
 if($_SERVER['REQUEST_METHOD']==='HEAD')ob_clean();
 ob_end_flush();
} catch(Throwable $e) {
 ob_clean();$status=$e instanceof HttpException?$e->status:500;http_response_code($status);
 if($status===500)error_log((string)$e,3,dirname(__DIR__).'/storage/logs/app.log');
 $message=$status===500?'Não foi possível concluir agora. Tente novamente em instantes.':$e->getMessage();
 $errors=$e instanceof \App\Core\ValidationException?$e->errors:[];
 if(class_exists(View::class))View::render('error',compact('status','message','errors'));
 else echo 'Aplicação indisponível. Verifique a instalação.';
 ob_end_flush();
}
