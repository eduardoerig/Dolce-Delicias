<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\ValidationException;
use PDO;
/**
 * Fotos e PDFs enviados pelo painel ficam no banco (tabela arquivos), não no
 * disco: na Vercel o disco é temporário e some entre uma execução e outra.
 * Na Vercel o corpo da requisição e da resposta vai até 4,5 MB, por isso o
 * limite de 4 MB para os dois tipos.
 */
final class UploadService {
 public const LIMITE_MB=4;
 private const NOME='/^[a-f0-9]{48}\.(pdf|jpg|png|webp)$/D';
 private const TIPOS=['jpg'=>'image/jpeg','png'=>'image/png','webp'=>'image/webp','pdf'=>'application/pdf'];
 public function __construct(private ?PDO $db=null) {}
 public function validate(string $path,string $kind,int $size): string {
  if($size<1 || $size>self::LIMITE_MB*1024*1024) throw new ValidationException(['arquivo'=>'Arquivo vazio ou acima de '.self::LIMITE_MB.' MB.']);
  $mime=(new \finfo(FILEINFO_MIME_TYPE))->file($path);
  if($kind==='pdf') {
   $stream=fopen($path,'rb');$head=fread($stream,5);fclose($stream);
   if($mime!=='application/pdf'||$head!=='%PDF-') throw new ValidationException(['arquivo'=>'Envie um PDF válido.']);
   return 'pdf';
  }
  $types=array_flip(array_slice(self::TIPOS,0,3,true));
  $sizeInfo=@getimagesize($path);
  if(!isset($types[$mime]) || !$sizeInfo || $sizeInfo[0]>10000 || $sizeInfo[1]>10000) throw new ValidationException(['arquivo'=>'Envie JPEG, PNG ou WebP válido com até 10.000 pixels por lado.']);
  return $types[$mime];
 }
 public function store(array $file,string $kind): ?string {
  if(($file['error']??UPLOAD_ERR_NO_FILE)===UPLOAD_ERR_NO_FILE) return null;
  if($file['error']!==UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) throw new ValidationException(['arquivo'=>'Não foi possível receber o arquivo.']);
  $ext=$this->validate($file['tmp_name'],$kind,(int)filesize($file['tmp_name']));
  $name=bin2hex(random_bytes(24)).'.'.$ext;
  $this->save($name,(string)file_get_contents($file['tmp_name']));
  return $name;
 }
 /** Grava o conteúdo com o nome dado (também usado para importar os arquivos antigos do disco). */
 public function save(string $name,string $content): void {
  if(!preg_match(self::NOME,$name)) throw new \InvalidArgumentException('Nome de arquivo inválido.');
  $st=$this->db->prepare('INSERT INTO arquivos(nome,tipo,tamanho,conteudo) VALUES (?,?,?,?) ON CONFLICT (nome) DO NOTHING');
  $st->bindValue(1,$name);$st->bindValue(2,self::TIPOS[pathinfo($name,PATHINFO_EXTENSION)]);$st->bindValue(3,strlen($content),PDO::PARAM_INT);$st->bindValue(4,$content,PDO::PARAM_LOB);
  $st->execute();
 }
 /** @return array{tipo:string,conteudo:string}|null */
 public function get(string $name): ?array {
  if(!preg_match(self::NOME,$name) || !$this->db) return null;
  $st=$this->db->prepare('SELECT tipo,conteudo FROM arquivos WHERE nome=?');$st->execute([$name]);
  $row=$st->fetch(PDO::FETCH_ASSOC);if(!$row) return null;
  $content=$row['conteudo'];if(is_resource($content))$content=stream_get_contents($content);
  return ['tipo'=>$row['tipo'],'conteudo'=>(string)$content];
 }
 public function remove(?string $name): void {
  if($name && preg_match(self::NOME,$name)) $this->db->prepare('DELETE FROM arquivos WHERE nome=?')->execute([$name]);
 }
}
