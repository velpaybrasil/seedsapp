<?php

use App\Core\Env;

// Configurações do banco de dados
return [
    'default' => [
        'driver' => 'mysql',
        'host' => Env::get('DB_HOST', 'localhost'),
        'database' => Env::get('DB_NAME', 'u315624178_seedsapp'),
        'username' => Env::get('DB_USER', 'u315624178_seedsapp'),
        'password' => Env::get('DB_PASS', 'gugaLima8*'),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'options' => [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ]
    ]
];
