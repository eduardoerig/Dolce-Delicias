<?php
$root = dirname(__DIR__);
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = ($path === '/' || $path === '') ? 'index.php' : ltrim($path, '/');

$bloqueados = ['partials', 'data', 'api', 'node_modules'];
$target = realpath("$root/$file");

if (
    !$target ||
    !is_file($target) ||
    pathinfo($target, PATHINFO_EXTENSION) !== 'php' ||
    !str_starts_with($target, $root . DIRECTORY_SEPARATOR) ||
    in_array(explode('/', $file)[0], $bloqueados, true)
) {
    http_response_code(404);
    echo 'Página não encontrada';
    exit;
}

chdir($root);
$_SERVER['SCRIPT_NAME'] = '/' . $file;
require $target;