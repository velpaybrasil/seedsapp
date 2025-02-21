<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';

try {
    // Conecta ao banco de dados
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);

    // Gera o hash da senha
    $password = '12345678';
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Atualiza a senha do usuário
    $sql = "UPDATE users SET password = :password WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':password' => $hash,
        ':email' => 'oi@paulogustavo.me'
    ]);

    echo "Hash gerado: " . $hash . "\n";
    echo "Senha atualizada com sucesso!\n";

} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
