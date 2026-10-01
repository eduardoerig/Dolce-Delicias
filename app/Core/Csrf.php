<?php
declare(strict_types=1);
namespace App\Core;
final class Csrf {
 public static function token(): string { return $_SESSION['csrf'] ??= bin2hex(random_bytes(32)); }
 public static function check(mixed $value): void {
  if (!is_string($value) || !isset($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $value)) throw new HttpException(403,'Sua sessão expirou. Atualize a página e tente novamente.');
 }
}
