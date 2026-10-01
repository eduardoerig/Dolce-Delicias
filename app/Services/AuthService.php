<?php
declare(strict_types=1);
namespace App\Services;
use App\Repositories\Repository;
use App\Core\HttpException;
final class AuthService {
 public function __construct(private Repository $repo) {}
 public function verify(string $login,string $password,string $ip): ?array {
  // IP-only bucket prevents rotating account names from bypassing the limit.
  if(!$this->repo->consumeLoginAttempt(hash('sha256',$ip))) throw new HttpException(429,'Muitas tentativas. Aguarde 15 minutos.');
  $user=$this->repo->byLogin($login);
  $hash=$user['senha_hash']??'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.';
  $ok=password_verify($password,$hash);
  return $ok && $user && $user['ativo']?$user:null;
 }
 public function user(): ?array {
  if(empty($_SESSION['user_id']))return null;
  try {$u=$this->repo->find('usuarios',(int)$_SESSION['user_id']);}catch(HttpException){return null;}
  if(!$u['ativo'] || !hash_equals($_SESSION['auth_hash']??'',hash('sha256',$u['senha_hash'])))return null;
  return $u;
 }
}
