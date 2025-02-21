<?php

use App\Core\Env;

return [
    'name' => Env::get('APP_NAME', 'GC Manager'),
    'env' => Env::get('APP_ENV', 'production'),
    'debug' => Env::get('APP_DEBUG', false),
    'url' => Env::get('APP_URL', 'https://igrejamodelo.alfadev.online'),
    'timezone' => 'America/Sao_Paulo',
    'locale' => 'pt_BR',
    'key' => Env::get('APP_KEY', null),
    
    'database' => [
        'host' => Env::get('DB_HOST', 'localhost'),
        'name' => Env::get('DB_NAME', 'u315624178_gcmanager'),
        'user' => Env::get('DB_USER', 'u315624178_gcmanager'),
        'pass' => Env::get('DB_PASS', 'gugaLima8*')
    ],
    
    'mail' => [
        'host' => Env::get('MAIL_HOST', 'smtp.mailtrap.io'),
        'port' => Env::get('MAIL_PORT', 2525),
        'username' => Env::get('MAIL_USERNAME', ''),
        'password' => Env::get('MAIL_PASSWORD', ''),
        'from_address' => Env::get('MAIL_FROM_ADDRESS', 'noreply@gcmanager.com'),
        'from_name' => Env::get('MAIL_FROM_NAME', 'SeedsApp')
    ],
    
    'session' => [
        'cookie_httponly' => true,
        'use_only_cookies' => true,
        'cookie_secure' => true,
        'cookie_samesite' => 'Lax',
        'gc_maxlifetime' => 1800 // 30 minutos
    ]
];
