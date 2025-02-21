<?php

// Application Constants
define('APP_NAME', 'SeedsApp');
define('CHURCH_NAME', isset($_ENV['CHURCH_NAME']) ? $_ENV['CHURCH_NAME'] : 'Igreja');
define('PUBLIC_PATH', realpath(__DIR__ . '/../public'));
define('VIEWS_PATH', realpath(__DIR__ . '/../views'));
define('STORAGE_PATH', realpath(__DIR__ . '/../storage'));

// Default Timezone
date_default_timezone_set('America/Sao_Paulo');

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session Configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_start();
