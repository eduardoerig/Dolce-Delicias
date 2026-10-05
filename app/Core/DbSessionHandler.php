<?php
declare(strict_types=1);
namespace App\Core;
use PDO;
/**
 * Sessão guardada na tabela sessoes. Na Vercel cada requisição pode rodar numa
 * instância diferente, então a sessão em arquivo faria o login cair.
 */
final class DbSessionHandler implements \SessionHandlerInterface, \SessionUpdateTimestampHandlerInterface {
 public function __construct(private PDO $db,private int $ttl) {}
 public function open(string $path,string $name): bool {return true;}
 public function close(): bool {return true;}
 public function read(string $id): string|false {
  $st=$this->db->prepare('SELECT dados FROM sessoes WHERE id=? AND expira_em>NOW()');$st->execute([$id]);
  return (string)($st->fetchColumn() ?: '');
 }
 public function write(string $id,string $data): bool {
  $this->db->prepare("INSERT INTO sessoes(id,dados,expira_em) VALUES (?,?,NOW()+make_interval(secs=>?)) ON CONFLICT (id) DO UPDATE SET dados=EXCLUDED.dados, expira_em=EXCLUDED.expira_em")->execute([$id,$data,$this->ttl]);
  return true;
 }
 public function destroy(string $id): bool {$this->db->prepare('DELETE FROM sessoes WHERE id=?')->execute([$id]);return true;}
 public function gc(int $max_lifetime): int|false {return $this->db->exec('DELETE FROM sessoes WHERE expira_em<=NOW()');}
 public function validateId(string $id): bool {
  $st=$this->db->prepare('SELECT 1 FROM sessoes WHERE id=? AND expira_em>NOW()');$st->execute([$id]);
  return (bool)$st->fetchColumn();
 }
 public function updateTimestamp(string $id,string $data): bool {
  $this->db->prepare('UPDATE sessoes SET expira_em=NOW()+make_interval(secs=>?) WHERE id=?')->execute([$this->ttl,$id]);
  return true;
 }
}
