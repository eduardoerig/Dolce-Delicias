<?php
declare(strict_types=1);
namespace App\Middleware;
use App\Services\AuthService;
use App\Core\{Session,HttpException};
final class AuthMiddleware {
 public function __construct(private AuthService $auth) {}
 public function requireUser(bool $admin=false): array {
  $u=$this->auth->user();if(!$u)Session::redirect('/login');
  if($admin && $u['perfil']!=='ADMIN')throw new HttpException(403,'Somente administradores podem gerenciar usuários.');
  return $u;
 }
}
