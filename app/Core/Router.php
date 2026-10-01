<?php
declare(strict_types=1);
namespace App\Core;
final class Router {
 private array $routes=[];
 public function add(string $method,string $path,callable $action): void {
  $pattern=preg_replace('/\\\{[a-zA-Z]+\\\}/','([^/]+)',preg_quote($path,'~'));
  $this->routes[]=[$method,'~^'.$pattern.'$~D',$action];
 }
 public function dispatch(string $method,string $path): void {
  foreach($this->routes as [$verb,$pattern,$action]) {
   if($verb===$method && preg_match($pattern,$path,$m)) { array_shift($m); $action(...$m); return; }
  }
  throw new HttpException(404,'Página não encontrada.');
 }
}
