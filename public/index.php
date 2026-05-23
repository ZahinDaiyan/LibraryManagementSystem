<?php

declare(strict_types=1);

session_start();

// Show errors during migration/debugging; remove in production
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Normalize URLs that accidentally repeat the project folder twice (e.g. /LibraryManagementSystem/LibraryManagementSystem/...)
$proj = basename(dirname(__DIR__));
$dup = '/' . $proj . '/' . $proj;
if (!empty($_SERVER['REQUEST_URI']) && str_contains($_SERVER['REQUEST_URI'], $dup)) {
    $new = str_replace($dup, '/' . $proj, $_SERVER['REQUEST_URI']);
    header('Location: ' . $new, true, 301);
    exit;
}

// Redirect requests that point to /Controllers/... without the project folder
$req = $_SERVER['REQUEST_URI'] ?? '';
if (str_starts_with($req, '/Controllers/')) {
    header('Location: ' . '/' . $proj . $req, true, 301);
    exit;
}

$autoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
} else {
    spl_autoload_register(function (string $class): void {
        $prefix = 'App\\';
        if (!str_starts_with($class, $prefix)) {
            return;
        }

        $relativeClass = substr($class, strlen($prefix));
        $file = __DIR__ . '/../app/' . str_replace('\\', '/', $relativeClass) . '.php';

        if (file_exists($file)) {
            require_once $file;
        }
    });
}

use App\Core\Application;

$app = new Application(__DIR__ . '/../');
$app->run();
