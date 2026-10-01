<?php
declare(strict_types=1);
namespace App\Core;
final class Session {
 public static function start(): void {
  ini_set('session.use_strict_mode','1');
  session_set_cookie_params(['httponly'=>true,'secure'=>($_ENV['APP_ENV']??'local')==='production','samesite'=>'Lax','path'=>'/']);
  session_start();
 }
 public static function redirect(string $path): never { header('Location: '.$path,true,303); exit; }
}
