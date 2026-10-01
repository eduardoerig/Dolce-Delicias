<?php
declare(strict_types=1);
namespace App\Core;
use PDO;
final class Database {
 public static function connect(): PDO {
  $env = static fn(string $k, string $default=''): string => (string)($_ENV[$k] ?? getenv($k) ?: $default);
  return new PDO('pgsql:host='.$env('DB_HOST','localhost').';port='.$env('DB_PORT','5432').';dbname='.$env('DB_DATABASE','dolce'), $env('DB_USERNAME','dolce'), $env('DB_PASSWORD'), [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]);
 }
}
