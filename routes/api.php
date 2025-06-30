<?php

use App\Controllers\UserController;

return function ($router) {
    $router->get('/', [UserController::class, 'index']);
    $router->post('/api/users', [UserController::class, 'store']);
    $router->post('/api/reset', [UserController::class, 'reset']);
};
