<?php

use App\Core\Router;

$router = new Router();

$router->get('/', [App\Controllers\HomeController::class, 'index']);
$router->get('/login', [App\Controllers\Auth\LoginController::class, 'showLogin']);
$router->post('/login', [App\Controllers\Auth\LoginController::class, 'authenticate']);
$router->get('/logout', [App\Controllers\Auth\LoginController::class, 'logout']);

$router->get('/admin', [App\Controllers\Admin\DashboardController::class, 'index']);
$router->get('/admin/books', [App\Controllers\Admin\BookController::class, 'index']);
$router->get('/admin/books/create', [App\Controllers\Admin\BookController::class, 'create']);
$router->post('/admin/books', [App\Controllers\Admin\BookController::class, 'store']);
$router->get('/admin/books/{id}/edit', [App\Controllers\Admin\BookController::class, 'edit']);
$router->post('/admin/books/{id}/update', [App\Controllers\Admin\BookController::class, 'update']);
$router->post('/admin/books/{id}/delete', [App\Controllers\Admin\BookController::class, 'delete']);

$router->get('/admin/users', [App\Controllers\Admin\UserController::class, 'index']);
$router->get('/admin/users/create', [App\Controllers\Admin\UserController::class, 'create']);
$router->post('/admin/users', [App\Controllers\Admin\UserController::class, 'store']);
$router->get('/admin/users/{id}/edit', [App\Controllers\Admin\UserController::class, 'edit']);
$router->post('/admin/users/{id}', [App\Controllers\Admin\UserController::class, 'update']);
$router->post('/admin/users/{id}/delete', [App\Controllers\Admin\UserController::class, 'delete']);
$router->post('/admin/users/{id}/toggle-status', [App\Controllers\Admin\UserController::class, 'toggleStatus']);
$router->post('/admin/users/{id}/change-role', [App\Controllers\Admin\UserController::class, 'changeRole']);
$router->post('/admin/users/{id}/reset-password', [App\Controllers\Admin\UserController::class, 'resetPassword']);

return $router;
