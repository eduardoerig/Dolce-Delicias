<?php
declare(strict_types=1);
require dirname(__DIR__).'/config/bootstrap.php';
use App\Core\Database;
use App\Repositories\Repository;
$command=$argv[1]??'';
try {
 $db=Database::connect();$repo=new Repository($db);
 $db->exec('SELECT pg_advisory_lock(782031)');
 if($command==='migrate') {
  $db->exec('CREATE TABLE IF NOT EXISTS migrations (version VARCHAR(120) PRIMARY KEY, applied_at TIMESTAMPTZ NOT NULL DEFAULT NOW())');
  foreach(glob(dirname(__DIR__).'/database/migrations/*.up.sql') as $file) {
   $version=basename($file);
   if($repo->query('SELECT 1 FROM migrations WHERE version=?',[$version])->fetchColumn())continue;
   $db->beginTransaction();$db->exec(file_get_contents($file));$repo->query('INSERT INTO migrations(version) VALUES (?)',[$version]);$db->commit();echo "Aplicada: $version\n";
  }
  echo "Migrations concluídas.\n";
 } elseif($command==='rollback') {
  $version=$repo->query('SELECT version FROM migrations ORDER BY version DESC LIMIT 1')->fetchColumn();
  if($version){$db->beginTransaction();$db->exec(file_get_contents(dirname(__DIR__).'/database/migrations/'.str_replace('.up.sql','.down.sql',$version)));$repo->query('DELETE FROM migrations WHERE version=?',[$version]);$db->commit();echo "Revertida: $version\n";}
 } elseif($command==='seed') {
  require dirname(__DIR__).'/database/seeds/legacy.php';
 } else {fwrite(STDERR,"Uso: php bin/console.php migrate|seed|rollback\n");exit(1);}
} catch(Throwable $e) {
 if(isset($db)&&$db->inTransaction())$db->rollBack();
 fwrite(STDERR,$e->getMessage()."\n");exit(1);
} finally {if(isset($db))$db->exec('SELECT pg_advisory_unlock(782031)');}
