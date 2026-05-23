<?php

use App\Core\Router;

$router = new Router();

$router->get('/', [App\Controllers\Auth\LoginController::class, 'showLogin']);
$router->post('/login', [App\Controllers\Auth\LoginController::class, 'authenticate']);
$router->get('/logout', [App\Controllers\Auth\LoginController::class, 'logout']);

$router->get('/admin/books', [App\Controllers\Admin\BookController::class, 'index']);
$router->get('/admin/books/create', [App\Controllers\Admin\BookController::class, 'create']);
$router->post('/admin/books', [App\Controllers\Admin\BookController::class, 'store']);
$router->get('/admin/books/{id}/edit', [App\Controllers\Admin\BookController::class, 'edit']);
$router->post('/admin/books/{id}/update', [App\Controllers\Admin\BookController::class, 'update']);
$router->post('/admin/books/{id}/delete', [App\Controllers\Admin\BookController::class, 'delete']);

return $router;
