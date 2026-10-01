<?php
declare(strict_types=1);
namespace App\Core;
final class View {
 public static function render(string $file,array $data=[]): void {
  extract($data, EXTR_SKIP);
  require dirname(__DIR__,2).'/resources/views/'.$file.'.php';
 }
}
