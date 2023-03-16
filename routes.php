<?php
// определение маршрутов и соответствующих им действий
$routes = [
    '/' => [
        'file' => 'home.php',
        'auth' => true
    ],
    '/register' => [
        'file' => 'register.php',
        'auth' => false
    ],
    '/login' => [
        'file' => 'login.php',
        'auth' => false
    ],
    '/api/login' => [
        'controller' => UserController::class,
        'method' => 'login'
    ],
    '/api/register' => [
        'controller' => UserController::class,
        'method' => 'register'
    ],
    '/api/poll-destroy' => [
        'controller' => PollController::class,
        'method' => 'destroy'
    ],
    '/api/poll-store' => [
        'controller' => PollController::class,
        'method' => 'store'
    ],
    '/api/poll-update' => [
        'controller' => PollController::class,
        'method' => 'update'
    ],
    '/api/poll-update-get' => [
        'controller' => PollController::class,
        'method' => 'get'
    ]
];
?>