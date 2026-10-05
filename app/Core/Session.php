<?php
declare(strict_types=1);
namespace App\Core;
use PDO;
final class Session {
 public static function start(?PDO $db=null): void {
  ini_set('session.use_strict_mode','1');
  if($db) {
   $ttl=(int)ini_get('session.gc_maxlifetime') ?: 1440;
   session_set_save_handler(new DbSessionHandler($db,$ttl),true);
  }
  session_set_cookie_params(['httponly'=>true,'secure'=>($_ENV['APP_ENV']??getenv('APP_ENV'))==='production' || getenv('VERCEL'),'samesite'=>'Lax','path'=>'/']);
  session_start();
 }
 public static function redirect(string $path): never { header('Location: '.$path,true,303); exit; }
}
