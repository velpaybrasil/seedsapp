<?php
require_once '/home/u315624178/domains/alfadev.online/public_html/gcmanager/config/config.php';

// Limpa a sessão
session_destroy();

// Redireciona para login
header('Location: ' . APP_URL . '/login.php');
exit;
