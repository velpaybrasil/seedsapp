<?php

require_once __DIR__ . '/../../bootstrap/app.php';

try {
    // Conecta ao banco de dados
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    // Cria o hash da senha
    $password = password_hash('gugaLima8*', PASSWORD_DEFAULT);
    
    // Adiciona o usuário
    $stmt = $pdo->prepare("
        INSERT INTO users (name, email, password, active, role) 
        VALUES (:name, :email, :password, 1, 'admin')
    ");
    
    $stmt->execute([
        'name' => 'Paulo Gustavo',
        'email' => 'pgustavodlima@gmail.com',
        'password' => $password
    ]);
    
    // Pega o ID do usuário
    $userId = $pdo->lastInsertId();
    
    // Adiciona como voluntário
    $stmt = $pdo->prepare("
        INSERT INTO volunteers (user_id, ministry) 
        VALUES (:user_id, 'Administrador')
    ");
    
    $stmt->execute(['user_id' => $userId]);
    
    echo "Usuário administrativo adicionado com sucesso!\n";
} catch (Exception $e) {
    echo "Erro ao adicionar usuário administrativo: " . $e->getMessage() . "\n";
}
