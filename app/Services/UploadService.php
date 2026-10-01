<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\ValidationException;
final class UploadService {
 public function __construct(private string $directory) {}
 public function validate(string $path,string $kind,int $size): string {
  if($size<1 || $size>($kind==='pdf'?10:5)*1024*1024) throw new ValidationException(['arquivo'=>'Arquivo vazio ou acima do limite (imagem 5 MB; PDF 10 MB).']);
  $mime=(new \finfo(FILEINFO_MIME_TYPE))->file($path);
  if($kind==='pdf') {
   $stream=fopen($path,'rb');$head=fread($stream,5);fclose($stream);
   if($mime!=='application/pdf'||$head!=='%PDF-') throw new ValidationException(['arquivo'=>'Envie um PDF válido.']);
   return 'pdf';
  }
  $types=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
  $sizeInfo=@getimagesize($path);
  if(!isset($types[$mime]) || !$sizeInfo || $sizeInfo[0]>10000 || $sizeInfo[1]>10000) throw new ValidationException(['arquivo'=>'Envie JPEG, PNG ou WebP válido com até 10.000 pixels por lado.']);
  return $types[$mime];
 }
 public function store(array $file,string $kind): ?string {
  if(($file['error']??UPLOAD_ERR_NO_FILE)===UPLOAD_ERR_NO_FILE) return null;
  if($file['error']!==UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) throw new ValidationException(['arquivo'=>'Não foi possível receber o arquivo.']);
  $ext=$this->validate($file['tmp_name'],$kind,(int)filesize($file['tmp_name']));
  if(!is_dir($this->directory)) mkdir($this->directory,0750,true);
  $name=bin2hex(random_bytes(24)).'.'.$ext;
  if(!move_uploaded_file($file['tmp_name'],$this->directory.'/'.$name)) throw new \RuntimeException('Falha ao armazenar upload.');
  return $name;
 }
 public function path(string $name): ?string {
  if(!preg_match('/^[a-f0-9]{48}\.(pdf|jpg|png|webp)$/D',$name)) return null;
  $path=$this->directory.'/'.$name;return is_file($path)?$path:null;
 }
 public function remove(?string $name): void {if($name && ($path=$this->path($name))) unlink($path);}
}
