<?php

declare(strict_types=1);

use App\Controllers\HomeController;
use App\Controllers\TaskController;

/** @var \Core\Http\Router $router */

$router->get('/', [HomeController::class, 'index']);
$router->get('/tasks', [TaskController::class, 'index']);
$router->get('/tasks/create', [TaskController::class, 'create']);
$router->post('/tasks', [TaskController::class, 'store']);
$router->get('/tasks/{id}', [TaskController::class, 'show']);
$router->get('/tasks/{id}/edit', [TaskController::class, 'edit']);
$router->post('/tasks/{id}/update', [TaskController::class, 'update']);
$router->post('/tasks/{id}/delete', [TaskController::class, 'destroy']);
