<?php

use App\Core\Env;

// Define o caminho raiz do projeto
define('ROOT_PATH', dirname(__DIR__));

// Define constantes globais
define('APP_NAME', Env::get('APP_NAME', 'SeedsApp'));
define('APP_ENV', Env::get('APP_ENV', 'production'));
define('APP_DEBUG', Env::get('APP_DEBUG', 'false') === 'true');
define('APP_URL', Env::get('APP_URL', 'https://igrejamodelo.alfadev.online'));
define('APP_BASE_PATH', Env::get('APP_BASE_PATH', ''));
define('APP_KEY', Env::get('APP_KEY', ''));
define('SESSION_NAME', 'seedsapp_session');
define('SESSION_LIFETIME', 120);
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('CHURCH_NAME', Env::get('CHURCH_NAME', 'Igreja Modelo'));

// Configurações da aplicação
$config = [
    'name' => APP_NAME,
    'env' => APP_ENV,
    'debug' => APP_DEBUG,
    'url' => APP_URL,
    'base_path' => APP_BASE_PATH,
    'timezone' => 'America/Sao_Paulo',
    'locale' => 'pt_BR',
    'key' => APP_KEY,
    
    // Configurações de sessão
    'session' => [
        'name' => SESSION_NAME,
        'lifetime' => SESSION_LIFETIME,
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
        'directory' => STORAGE_PATH . '/uploads',
    ],
    
    // Configurações de log
    'log' => [
        'level' => 'debug',
        'directory' => STORAGE_PATH . '/logs',
    ],
];

// Retorna a configuração
return $config;
