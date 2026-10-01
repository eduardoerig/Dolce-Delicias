<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\{View,Session,ValidationException};
use App\Services\AuthService;
final class AuthController {
 public function __construct(private AuthService $auth) {}
 public function login(): void {
  $error='';
  if($_SERVER['REQUEST_METHOD']==='POST') {
   $login=$_POST['login']??'';$password=$_POST['senha']??'';
   if(!is_string($login)||!is_string($password)||strlen($login)>80||strlen($password)>72)throw new ValidationException(['login'=>'Credenciais inválidas.']);
   $u=$this->auth->verify(trim($login),$password,$_SERVER['REMOTE_ADDR']??'unknown');
   if($u) {
    session_regenerate_id(true);$_SESSION=['user_id'=>$u['id_usuario'],'auth_hash'=>hash('sha256',$u['senha_hash'])];
    Session::redirect('/admin');
   }
   http_response_code(422);$error='Login ou senha inválidos.';
  }
  View::render('admin/login',compact('error'));
 }
 public function logout(): never {
  $_SESSION=[];session_destroy();
  setcookie(session_name(),'', ['expires'=>time()-3600,'path'=>'/','httponly'=>true,'secure'=>($_ENV['APP_ENV']??'local')==='production','samesite'=>'Lax']);
  Session::redirect('/login');
 }
}
