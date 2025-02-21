<?php
require_once __DIR__ . '/config/config.php';

// Limpa a sessão
session_destroy();

// Redireciona para login
header('Location: login.php');
exit;
