<?php
// определение маршрутов и соответствующих им действий
$routes = [
    '/' => [
        'controller' => PollController::class,
        'method' => 'index',
        'auth' => true
    ],
    '/register' => [
        'controller' => UserController::class,
        'method' => 'registerPage',
        'auth' => false
    ],
    '/login' => [
        'controller' => UserController::class,
        'method' => 'loginPage',
        'auth' => false
    ],
    '/api/login' => [
        'controller' => UserController::class,
        'method' => 'login',
        'api' => true
    ],
    '/api/register' => [
        'controller' => UserController::class,
        'method' => 'register',
        'api' => true
    ],
    '/api/poll-destroy' => [
        'controller' => PollController::class,
        'method' => 'destroy',
        'api' => true,
        'auth' => true
    ],
    '/api/poll-store' => [
        'controller' => PollController::class,
        'method' => 'store',
        'api' => true,
        'auth' => true
    ],
    '/api/poll-update' => [
        'controller' => PollController::class,
        'method' => 'updatePoll',
        'api' => true,
        'auth' => true
    ],
    '/api/poll-update-get' => [
        'controller' => PollController::class,
        'method' => 'getPoll',
        'api' => true,
        'auth' => true
    ],
    '/api/poll-destroy-answer' => [
        'controller' => PollController::class,
        'method' => 'destroyAnswer',
        'api' => true,
        'auth' => true
    ],
    '/api/logout' => [
        'controller' => UserController::class,
        'method' => 'logout',
        'api' => true,
        'auth' => true
    ],
    '/api/get-random-poll' => [
        'controller' => PollController::class,
        'method' => 'randomPoll',
        'api' => true,
        'auth' => true
    ]
];
?>