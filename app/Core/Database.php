<?php
declare(strict_types=1);
namespace App\Core;
use PDO;
final class Database {
 /**
  * Local (Docker): DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD.
  * Vercel: DATABASE_URL (ou POSTGRES_URL), que o Neon/Supabase da Vercel
  * preenchem sozinhos; tem prioridade sobre as DB_*.
  */
 public static function connect(): PDO {
  $env = static fn(string $k, string $default=''): string => (string)($_ENV[$k] ?? getenv($k) ?: $default);
  $options=[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false];
  $url=$env('DATABASE_URL') ?: $env('POSTGRES_URL');
  if($url!=='') {
   $p=parse_url($url);
   if(!$p || !isset($p['host'])) throw new \RuntimeException('DATABASE_URL inválida.');
   parse_str($p['query']??'',$query);
   $dsn='pgsql:host='.$p['host'].';port='.($p['port']??5432).';dbname='.ltrim($p['path']??'/postgres','/').';sslmode='.($query['sslmode']??'require');
   return new PDO($dsn,urldecode($p['user']??''),urldecode($p['pass']??''),$options);
  }
  return new PDO('pgsql:host='.$env('DB_HOST','localhost').';port='.$env('DB_PORT','5432').';dbname='.$env('DB_DATABASE','dolce'), $env('DB_USERNAME','dolce'), $env('DB_PASSWORD'), $options);
 }
}
