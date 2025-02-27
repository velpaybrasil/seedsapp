<?php

// Application Constants
define('APP_NAME', 'SeedsApp');
define('CHURCH_NAME', isset($_ENV['CHURCH_NAME']) ? $_ENV['CHURCH_NAME'] : 'Igreja');
define('PUBLIC_PATH', realpath(__DIR__ . '/../public'));
define('VIEWS_PATH', realpath(__DIR__ . '/Views'));
define('STORAGE_PATH', realpath(__DIR__ . '/../storage'));

// URL e Email
define('APP_URL', isset($_ENV['APP_URL']) ? $_ENV['APP_URL'] : 'https://igrejamodelo.alfadev.online');
define('APP_EMAIL', isset($_ENV['APP_EMAIL']) ? $_ENV['APP_EMAIL'] : 'no-reply@igrejamodelo.alfadev.online');

// Default Timezone
date_default_timezone_set('America/Sao_Paulo');

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
