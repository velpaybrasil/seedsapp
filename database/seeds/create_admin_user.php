<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/paths.php';
require_once __DIR__ . '/../../app/Core/Model.php';
require_once __DIR__ . '/../../app/Models/User.php';

use App\Models\User;

try {
    $userModel = new User();
    
    // Dados do novo usuário
    $userData = [
        'name' => 'Paulo Gustavo',
        'email' => 'admin@paulogustavo.me',
        'password' => '12345678',
        'role' => 'admin',
        'is_owner' => 1,
        'active' => 1,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    // Criar o usuário
    $userId = $userModel->create($userData);
    
    echo "Usuário admin criado com sucesso! ID: " . $userId . "\n";
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
