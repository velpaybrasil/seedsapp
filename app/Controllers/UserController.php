<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Core\Database;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Module;

class UserController extends Controller {
    protected User $userModel;
    protected Role $roleModel;
    protected Permission $permissionModel;
    protected Module $moduleModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
        $this->roleModel = new Role();
        $this->permissionModel = new Permission();
        $this->moduleModel = new Module();
    }

    public function index() {
        try {
            // Parâmetros de paginação e filtros
            $page = $_GET['page'] ?? 1;
            $perPage = 10;
            $search = $_GET['search'] ?? null;
            $status = $_GET['status'] ?? null;

            // Busca usuários paginados com seus papéis
            $result = $this->userModel->getUsersWithRoles($search, $status, $page, $perPage);
            $users = $result['users'];
            $total = $result['total'];

            // Estatísticas de usuários
            $stats = [
                'total' => $this->userModel->count(),
                'active' => $this->userModel->countActiveUsers(),
                'inactive' => $this->userModel->count() - $this->userModel->countActiveUsers(),
                'roles' => $this->roleModel->getAll(),
                'last_registered' => $this->userModel->getRecentUsers(5)
            ];

            View::render('users/index', [
                'users' => $users,
                'stats' => $stats,
                'pagination' => [
                    'page' => $page,
                    'perPage' => $perPage,
                    'total' => $total
                ],
                'filters' => [
                    'search' => $search,
                    'status' => $status
                ],
                'title' => 'Usuários'
            ]);
        } catch (\PDOException $e) {
            error_log("[UserController] Erro ao listar usuários: " . $e->getMessage());
            $this->setFlash('error', 'Erro ao carregar usuários.');
            $this->redirect('/dashboard');
        }
    }

    public function store() {
        try {
            // Validate required fields
            $requiredFields = ['name', 'email', 'password'];
            foreach ($requiredFields as $field) {
                if (empty($_POST[$field])) {
                    $this->setFlash('error', 'Todos os campos obrigatórios devem ser preenchidos.');
                    $this->redirect('/users/create');
                    return;
                }
            }

            // Check if email already exists
            if ($this->userModel->findByEmail($_POST['email'])) {
                $this->setFlash('error', 'Este e-mail já está em uso.');
                $this->redirect('/users/create');
                return;
            }

            if (!Database::beginTransaction()) {
                throw new \PDOException('Não foi possível iniciar a transação');
            }

            // Create user
            $userId = $this->userModel->create([
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
                'active' => isset($_POST['active']) ? 1 : 0
            ]);

            // Assign roles if selected
            if (!empty($_POST['roles'])) {
                $this->userModel->assignRoles($userId, $_POST['roles']);
            }

            if (!Database::commit()) {
                throw new \PDOException('Não foi possível finalizar a transação');
            }

            $this->setFlash('success', 'Usuário criado com sucesso.');
            $this->redirect('/users');

        } catch (\PDOException $e) {
            Database::rollBack();
            error_log("[UserController] Erro ao criar usuário: " . $e->getMessage());
            $this->setFlash('error', 'Erro ao criar usuário. Por favor, tente novamente.');
            $this->redirect('/users/create');
        }
    }

    public function update(int $id) {
        try {
            // Validate required fields
            if (empty($_POST['name']) || empty($_POST['email'])) {
                $this->setFlash('error', 'Nome e e-mail são obrigatórios.');
                $this->redirect("/users/{$id}/edit");
                return;
            }

            // Check if email exists and belongs to another user
            $existingUser = $this->userModel->findByEmail($_POST['email']);
            if ($existingUser && $existingUser['id'] != $id) {
                $this->setFlash('error', 'Este e-mail já está em uso por outro usuário.');
                $this->redirect("/users/{$id}/edit");
                return;
            }

            if (!Database::beginTransaction()) {
                throw new \PDOException('Não foi possível iniciar a transação');
            }

            // Update user data
            $data = [
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'active' => isset($_POST['active']) ? 1 : 0
            ];

            // Only update password if provided
            if (!empty($_POST['password'])) {
                $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }

            $this->userModel->update($id, $data);

            // Update roles if provided
            if (isset($_POST['roles'])) {
                $this->userModel->updateRoles($id, $_POST['roles']);
            }

            if (!Database::commit()) {
                throw new \PDOException('Não foi possível finalizar a transação');
            }

            $this->setFlash('success', 'Usuário atualizado com sucesso.');
            $this->redirect('/users');

        } catch (\PDOException $e) {
            Database::rollBack();
            error_log("[UserController] Erro ao atualizar usuário: " . $e->getMessage());
            $this->setFlash('error', 'Erro ao atualizar usuário. Por favor, tente novamente.');
            $this->redirect("/users/{$id}/edit");
        }
    }

    public function delete(int $id) {
        try {
            if (!Database::beginTransaction()) {
                throw new \PDOException('Não foi possível iniciar a transação');
            }

            // Remove all roles first
            if (!$this->userModel->removeAllRoles($id)) {
                throw new \PDOException('Falha ao remover papéis do usuário');
            }

            // Then delete the user
            if (!$this->userModel->delete($id)) {
                throw new \PDOException('Falha ao excluir usuário');
            }

            if (!Database::commit()) {
                throw new \PDOException('Não foi possível finalizar a transação');
            }

            $this->setFlash('success', 'Usuário excluído com sucesso.');

        } catch (\PDOException $e) {
            Database::rollBack();
            error_log("[UserController] Erro ao excluir usuário: " . $e->getMessage());
            $this->setFlash('error', 'Erro ao excluir usuário. Por favor, tente novamente.');
        }

        $this->redirect('/users');
    }

    public function roles(): void {
        try {
            // Busca papéis com suas permissões e usuários
            $roles = $this->roleModel->getAll();
            
            // Busca módulos ativos para o formulário de permissões
            $modules = $this->moduleModel->getActive();

            // Se não houver papéis, inicializa como array vazio
            if (!$roles) {
                $roles = [];
            }

            View::render('users/roles/index', [
                'roles' => $roles,
                'modules' => $modules,
                'title' => 'Papéis de Usuário'
            ]);
        } catch (\PDOException $e) {
            error_log("[UserController] Erro ao listar papéis: " . $e->getMessage());
            $this->setFlash('error', 'Erro ao carregar papéis.');
            $this->redirect('/dashboard');
        }
    }

    public function storeRole(): void {
        $data = [
            'name' => $_POST['name'] ?? '',
            'description' => $_POST['description'] ?? '',
            'is_system' => isset($_POST['is_system']),
            'active' => isset($_POST['active']),
            'permissions' => $_POST['permissions'] ?? []
        ];

        try {
            if (empty($data['name'])) {
                $this->setFlash('error', 'O nome do papel é obrigatório.');
                $_SESSION['old'] = $data;
                $this->redirect('/users/roles/create');
                return;
            }

            if (!Database::beginTransaction()) {
                throw new \PDOException('Não foi possível iniciar a transação');
            }

            $roleId = $this->roleModel->create([
                'name' => $data['name'],
                'description' => $data['description'],
                'is_system' => $data['is_system'],
                'active' => $data['active']
            ]);

            if (!empty($data['permissions'])) {
                foreach ($data['permissions'] as $moduleId => $permissions) {
                    $this->roleModel->assignPermissions($roleId, $moduleId, $permissions);
                }
            }

            if (!Database::commit()) {
                throw new \PDOException('Não foi possível finalizar a transação');
            }

            $this->setFlash('success', 'Papel criado com sucesso!');
            $this->redirect('/users/roles');

        } catch (\PDOException $e) {
            Database::rollBack();
            error_log("[UserController] Erro ao criar papel: " . $e->getMessage());
            $this->setFlash('error', 'Erro ao criar papel.');
            $_SESSION['old'] = $data ?? [];
            $this->redirect('/users/roles/create');
        }
    }

    public function updateRole(int $id): void {
        $data = [
            'name' => $_POST['name'] ?? '',
            'description' => $_POST['description'] ?? '',
            'is_system' => isset($_POST['is_system']),
            'active' => isset($_POST['active']),
            'permissions' => $_POST['permissions'] ?? []
        ];

        try {
            if (empty($data['name'])) {
                $this->setFlash('error', 'O nome do papel é obrigatório.');
                $_SESSION['old'] = $data;
                $this->redirect("/users/roles/{$id}/edit");
                return;
            }

            if (!Database::beginTransaction()) {
                throw new \PDOException('Não foi possível iniciar a transação');
            }

            $this->roleModel->update($id, [
                'name' => $data['name'],
                'description' => $data['description'],
                'is_system' => $data['is_system'],
                'active' => $data['active']
            ]);

            // Atualizar permissões
            $this->roleModel->clearPermissions($id);
            if (!empty($data['permissions'])) {
                foreach ($data['permissions'] as $moduleId => $permissions) {
                    $this->roleModel->assignPermissions($id, $moduleId, $permissions);
                }
            }

            if (!Database::commit()) {
                throw new \PDOException('Não foi possível finalizar a transação');
            }

            $this->setFlash('success', 'Papel atualizado com sucesso!');
            $this->redirect('/users/roles');

        } catch (\PDOException $e) {
            Database::rollBack();
            error_log("[UserController] Erro ao atualizar papel: " . $e->getMessage());
            $this->setFlash('error', 'Erro ao atualizar papel.');
            $_SESSION['old'] = $data ?? [];
            $this->redirect("/users/roles/{$id}/edit");
        }
    }

    public function deleteRole(int $id): void {
        try {
            $role = $this->roleModel->find($id);
            if (!$role) {
                $this->setFlash('error', 'Papel não encontrado.');
                $this->redirect('/users/roles');
                return;
            }

            if ($role['is_system']) {
                $this->setFlash('error', 'Não é possível excluir um papel do sistema.');
                $this->redirect('/users/roles');
                return;
            }

            if (!Database::beginTransaction()) {
                throw new \PDOException('Não foi possível iniciar a transação');
            }
            
            // Remove todas as permissões do papel
            $this->roleModel->clearPermissions($id);
            
            // Remove o papel
            $this->roleModel->delete($id);

            if (!Database::commit()) {
                throw new \PDOException('Não foi possível finalizar a transação');
            }

            $this->setFlash('success', 'Papel excluído com sucesso!');
            $this->redirect('/users/roles');

        } catch (\PDOException $e) {
            Database::rollBack();
            error_log("[UserController] Erro ao excluir papel: " . $e->getMessage());
            $this->setFlash('error', 'Erro ao excluir papel.');
            $this->redirect('/users/roles');
        }
    }
}
