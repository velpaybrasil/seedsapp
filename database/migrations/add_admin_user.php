<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Core\Database\Database;
use App\Models\User;
use App\Models\Role;

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Criar novo usuário
    $userId = User::create([
        'email' => 'admin@paulogustavo.me',
        'password' => '123456789',
        'name' => 'Paulo Gustavo Admin',
        'active' => 1
    ]);

    if (!$userId) {
        throw new Exception('Erro ao criar usuário');
    }

    // Buscar ID do papel de administrador
    $sql = "SELECT id FROM roles WHERE name = 'Administrador' LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $adminRole = $stmt->fetch(\PDO::FETCH_ASSOC);
    
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
