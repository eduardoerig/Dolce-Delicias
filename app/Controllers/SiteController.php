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
  $path=null;
  if($stored && preg_match('/^legacy:([a-z0-9-]+\.pdf)$/D',$stored,$m))$path=dirname(__DIR__,2).'/catalogos/'.$m[1];
  elseif($stored)$path=$this->uploads->path($stored);
  if(!$path||!is_file($path))throw new HttpException(404,'Catálogo ainda não disponível.');
  header('Content-Type: application/pdf');header('Content-Disposition: attachment; filename="catalogo.pdf"');header('Content-Length: '.filesize($path));readfile($path);
 }
 public function media(string $name): void {
  $path=$this->uploads->path($name);
  if(!$path || str_ends_with($name,'.pdf'))throw new HttpException(404,'Imagem não encontrada.');
  header('Content-Type: '.(new \finfo(FILEINFO_MIME_TYPE))->file($path));header('Content-Length: '.filesize($path));readfile($path);
 }
}
