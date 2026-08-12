<?php
declare(strict_types=1);
session_start();

define('APP_NAME', 'STARE');
define('APP_VERSION', '2.0.0');
define('CURRENCY', '$');

$root = str_replace('\\', '/', dirname(__DIR__));
$docRoot = isset($_SERVER['DOCUMENT_ROOT']) ? rtrim(str_replace('\\','/',$_SERVER['DOCUMENT_ROOT']), '/') : '';
$base = $docRoot !== '' && str_starts_with($root, $docRoot) ? substr($root, strlen($docRoot)) : '';
define('BASE_URL', $base === '/' ? '' : $base);
define('DB_HOST', 'localhost');
define('DB_NAME', 'stare_management');
define('DB_USER', 'root');
define('DB_PASS', '');
