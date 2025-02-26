<?php

require_once __DIR__ . '/../../app/Core/Database/Database.php';
require_once __DIR__ . '/../../app/Models/User.php';
require_once __DIR__ . '/../../app/Models/Role.php';

use App\Core\Database\Database;
use App\Models\User;
use App\Models\Role;

try {
    $db = Database::getInstance();
    
    // Criar novo usuário
    $userId = User::create([
        'email' => 'admin@paulogustavo.me',
        'password' => '123456789',
        'name' => 'Paulo Gustavo Admin',
        'active' => 1
    ]);

    // Buscar ID do papel de administrador
    $roleModel = new Role();
    $adminRole = $roleModel->findByName('Administrador');
    
    if (!$adminRole) {
        throw new Exception('Papel de Administrador não encontrado');
    }

    // Atribuir papel ao usuário
    User::assignRoles($userId, [$adminRole['id']]);

    echo "Usuário administrativo criado com sucesso!\n";
    echo "Email: admin@paulogustavo.me\n";
    echo "Senha: 123456789\n";

} catch (Exception $e) {
    echo "Erro ao criar usuário administrativo: " . $e->getMessage() . "\n";
    exit(1);
}
