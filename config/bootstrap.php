<?php
declare(strict_types=1);
require dirname(__DIR__) . '/vendor/autoload.php';
Dotenv\Dotenv::createImmutable(dirname(__DIR__))->safeLoad();
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'America/Sao_Paulo');
ini_set('display_errors', '0');
require_once dirname(__DIR__) . '/partials/bootstrap.php';
