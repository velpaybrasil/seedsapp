<?php

use App\Core\Env;

// Carrega o arquivo .env se ainda não foi carregado
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    Env::load($envFile);
}

// Application Constants
if (!defined('APP_NAME')) define('APP_NAME', Env::get('APP_NAME', 'SeedsApp'));
if (!defined('APP_ENV')) define('APP_ENV', Env::get('APP_ENV', 'production'));
if (!defined('APP_DEBUG')) define('APP_DEBUG', Env::get('APP_DEBUG', 'false') === 'true');
if (!defined('APP_URL')) define('APP_URL', Env::get('APP_URL', 'https://igrejamodelo.alfadev.online'));
if (!defined('APP_KEY')) define('APP_KEY', Env::get('APP_KEY', ''));

// Path Constants
if (!defined('ROOT_PATH')) define('ROOT_PATH', dirname(__DIR__));
if (!defined('APP_PATH')) define('APP_PATH', __DIR__);
if (!defined('CONFIG_PATH')) define('CONFIG_PATH', ROOT_PATH . '/config');
if (!defined('PUBLIC_PATH')) define('PUBLIC_PATH', ROOT_PATH . '/public');
if (!defined('STORAGE_PATH')) define('STORAGE_PATH', ROOT_PATH . '/storage');
if (!defined('VIEWS_PATH')) define('VIEWS_PATH', __DIR__ . '/Views');

// Church Constants
if (!defined('CHURCH_NAME')) define('CHURCH_NAME', Env::get('CHURCH_NAME', 'Igreja'));
if (!defined('APP_EMAIL')) define('APP_EMAIL', Env::get('APP_EMAIL', 'noreply@igrejamodelo.alfadev.online'));

// Database Constants
$dbConfig = require CONFIG_PATH . '/database.php';
if (!defined('DB_HOST')) define('DB_HOST', $dbConfig['default']['host']);
if (!defined('DB_NAME')) define('DB_NAME', $dbConfig['default']['database']);
if (!defined('DB_USER')) define('DB_USER', $dbConfig['default']['username']);
if (!defined('DB_PASS')) define('DB_PASS', $dbConfig['default']['password']);

// Session Constants
if (!defined('SESSION_LIFETIME')) define('SESSION_LIFETIME', 7200); // 2 hours in seconds
if (!defined('SESSION_NAME')) define('SESSION_NAME', 'seeds_session');

// Security Constants
if (!defined('PASSWORD_MIN_LENGTH')) define('PASSWORD_MIN_LENGTH', 8);
if (!defined('MAX_LOGIN_ATTEMPTS')) define('MAX_LOGIN_ATTEMPTS', 5);
if (!defined('LOCKOUT_TIME')) define('LOCKOUT_TIME', 15); // minutes
if (!defined('TOKEN_EXPIRY')) define('TOKEN_EXPIRY', 3600); // 1 hour in seconds

// Default Timezone
date_default_timezone_set('America/Sao_Paulo');

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
