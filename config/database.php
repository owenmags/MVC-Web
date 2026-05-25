<?php

return [
    'driver' => 'sqlite',
    'sqlite' => [
        'path' => dirname(__DIR__) . '/storage/database.sqlite',
    ],
    'mysql' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'database' => 'task_manager',
        'username' => 'root',
        'password' => '',
    ],
];
