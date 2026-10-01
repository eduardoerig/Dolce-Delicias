<?php
declare(strict_types=1);
$path=parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH)?:'/';
$file=realpath(__DIR__.$path);
if(str_starts_with($path,'/assets/') && $file && str_starts_with($file,__DIR__.'/assets/') && is_file($file) && in_array(pathinfo($file,PATHINFO_EXTENSION),['css','js','jpg','jpeg','png','webp','svg','woff2','ico'],true)) return false;
require __DIR__.'/index.php';
