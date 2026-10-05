<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\{View,HttpException};
use App\Repositories\CatalogRepository;
use App\Services\UploadService;
final class SiteController {
 public function __construct(private CatalogRepository $catalog,private UploadService $uploads) {}
 public function page(string $page,?string $slug=null): void {
  // Load data before rendering; view helpers never query the database.
  $GLOBALS['dd_catalog']=['products'=>$this->catalog->products(),'units'=>$this->catalog->units(),'promotions'=>$this->catalog->promotions()];
  if($slug!==null)$_GET['slug']=$slug;
  View::render('site/'.$page);
 }
 public function pdf(string $slug): void {
  $stored=$slug==='completo'?'legacy:completo.pdf':$this->catalog->pdf($slug);
  $content=null;
  if($stored && preg_match('/^legacy:([a-z0-9-]+\.pdf)$/D',$stored,$m)){$path=dirname(__DIR__,2).'/catalogos/'.$m[1];if(is_file($path))$content=file_get_contents($path);}
  elseif($stored)$content=$this->uploads->get($stored)['conteudo']??null;
  if(!is_string($content))throw new HttpException(404,'Catálogo ainda não disponível.');
  header('Content-Type: application/pdf');header('Content-Disposition: attachment; filename="catalogo.pdf"');header('Content-Length: '.strlen($content));echo $content;
 }
 public function media(string $name): void {
  $file=str_ends_with($name,'.pdf')?null:$this->uploads->get($name);
  if(!$file)throw new HttpException(404,'Imagem não encontrada.');
  // O nome é aleatório e nunca é reaproveitado: pode ficar em cache por muito tempo.
  header('Content-Type: '.$file['tipo']);header('Content-Length: '.strlen($file['conteudo']));header('Cache-Control: public, max-age=31536000, immutable');echo $file['conteudo'];
 }
}
