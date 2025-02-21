<?php

use App\Core\Env;

// Define o caminho raiz do projeto
define('ROOT_PATH', dirname(__DIR__));

// Configurações da aplicação
$config = [
    'name' => Env::get('APP_NAME', 'SeedsApp'),
    'env' => Env::get('APP_ENV', 'production'),
    'debug' => Env::get('APP_DEBUG', 'false') === 'true',
    'url' => Env::get('APP_URL', 'https://igrejamodelo.alfadev.online'),
    'base_path' => Env::get('APP_BASE_PATH', ''),
    'timezone' => 'America/Sao_Paulo',
    'locale' => 'pt_BR',
    'key' => Env::get('APP_KEY', ''),
    
    // Configurações de sessão
    'session' => [
        'name' => 'seedsapp_session',
        'lifetime' => 120,
        'expire_on_close' => false,
        'encrypt' => false,
        'secure' => true,
        'http_only' => true,
        'same_site' => 'lax',
    ],
    
    // Configurações de cookie
    'cookie' => [
        'prefix' => '',
        'domain' => '',
        'path' => '/',
        'secure' => true,
        'http_only' => true,
        'same_site' => 'lax',
    ],
    
    // Configurações de upload
    'upload' => [
        'max_size' => Env::get('UPLOAD_MAX_SIZE', 5242880),
        'allowed_extensions' => explode(',', Env::get('ALLOWED_EXTENSIONS', 'jpg,jpeg,png,pdf')),
        'directory' => ROOT_PATH . '/storage/uploads',
    ],
    
    // Configurações de log
    'log' => [
        'level' => 'debug',
        'directory' => ROOT_PATH . '/storage/logs',
    ],
];

// Define constantes globais
define('APP_NAME', $config['name']);
define('APP_ENV', $config['env']);
define('APP_DEBUG', $config['debug']);
define('APP_URL', $config['url']);
define('APP_BASE_PATH', $config['base_path']);
define('CHURCH_NAME', Env::get('CHURCH_NAME', 'Igreja Modelo'));

// Retorna a configuração
return $config;
