<?php

// Application Constants
if (!defined('APP_NAME')) define('APP_NAME', 'SeedsApp');
if (!defined('CHURCH_NAME')) define('CHURCH_NAME', isset($_ENV['CHURCH_NAME']) ? $_ENV['CHURCH_NAME'] : 'Igreja');
if (!defined('PUBLIC_PATH')) define('PUBLIC_PATH', realpath(__DIR__ . '/../public'));
if (!defined('VIEWS_PATH')) define('VIEWS_PATH', realpath(__DIR__ . '/Views'));
if (!defined('STORAGE_PATH')) define('STORAGE_PATH', realpath(__DIR__ . '/../storage'));

// URL e Email
if (!defined('APP_URL')) define('APP_URL', isset($_ENV['APP_URL']) ? $_ENV['APP_URL'] : 'https://igrejamodelo.alfadev.online');
if (!defined('APP_EMAIL')) define('APP_EMAIL', isset($_ENV['APP_EMAIL']) ? $_ENV['APP_EMAIL'] : 'noreply@igrejamodelo.alfadev.online');

// Carrega configurações do banco de dados
$dbConfig = require_once __DIR__ . '/../config/database.php';
if (!defined('DB_HOST')) define('DB_HOST', $dbConfig['default']['host']);
if (!defined('DB_NAME')) define('DB_NAME', $dbConfig['default']['database']);
if (!defined('DB_USER')) define('DB_USER', $dbConfig['default']['username']);
if (!defined('DB_PASS')) define('DB_PASS', $dbConfig['default']['password']);

// Default Timezone
date_default_timezone_set('America/Sao_Paulo');

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
