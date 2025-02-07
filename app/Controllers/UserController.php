<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

class UserController extends Controller {
    private User $userModel;
    private Role $roleModel;
    private Permission $permissionModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
        $this->roleModel = new Role();
        $this->permissionModel = new Permission();
    }

    public function index() {
        try {
            // Estatísticas de usuários
            $stats = [
                'total' => $this->userModel->count(),
                'active' => $this->userModel->countActiveUsers(),
                'inactive' => $this->userModel->count() - $this->userModel->countActiveUsers(),
                'roles' => $this->roleModel->getAll(),
                'last_registered' => $this->userModel->getRecentUsers(5)
            ];

            // Lista de usuários com seus papéis
            $users = $this->userModel->getAllWithRoles();

            View::render('users/index', [
                'users' => $users,
                'stats' => $stats,
                'title' => 'Usuários'
            ]);
        } catch (\PDOException $e) {
            error_log('Erro ao listar usuários: ' . $e->getMessage());
            $this->setFlashMessage('error', 'Erro ao carregar usuários.');
            $this->redirect('/dashboard');
        }
    }

    public function create() {
        try {
            $roles = $this->roleModel->getAll();
            View::render('users/create', [
                'roles' => $roles,
                'title' => 'Novo Usuário'
            ]);
        } catch (\PDOException $e) {
            error_log('Erro ao carregar papéis: ' . $e->getMessage());
            $this->setFlashMessage('error', 'Erro ao carregar formulário.');
            $this->redirect('/users');
        }
    }

    public function store() {
        try {
            $data = [
                'name' => $_POST['name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'password' => $_POST['password'] ?? '',
                'active' => isset($_POST['active']) ? 1 : 0,
                'roles' => $_POST['roles'] ?? []
            ];

            if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
                $this->setFlashMessage('error', 'Por favor, preencha todos os campos obrigatórios.');
                $this->redirect('/users/create');
                return;
            }

            if ($this->userModel->findByEmail($data['email'])) {
                $this->setFlashMessage('error', 'Este e-mail já está em uso.');
                $this->redirect('/users/create');
                return;
            }

            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            
            $this->db->beginTransaction();
            
            $userId = $this->userModel->create($data);
            
            // Associar papéis ao usuário
            if (!empty($data['roles'])) {
                $this->userModel->assignRoles($userId, $data['roles']);
            }
            
            $this->db->commit();

            $this->setFlashMessage('success', 'Usuário criado com sucesso!');
            $this->redirect('/users');

        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log('Erro ao criar usuário: ' . $e->getMessage());
            $this->setFlashMessage('error', 'Erro ao criar usuário.');
            $this->redirect('/users/create');
        }
    }

    public function edit($id) {
        try {
            $user = $this->userModel->findById($id);
            if (!$user) {
                $this->setFlashMessage('error', 'Usuário não encontrado.');
                $this->redirect('/users');
                return;
            }

            $roles = $this->roleModel->getAll();
            $userRoles = $this->userModel->getUserRoles($id);

            View::render('users/edit', [
                'user' => $user,
                'roles' => $roles,
                'userRoles' => array_column($userRoles, 'role_id'),
                'title' => 'Editar Usuário'
            ]);
        } catch (\PDOException $e) {
            error_log('Erro ao carregar usuário: ' . $e->getMessage());
            $this->setFlashMessage('error', 'Erro ao carregar usuário.');
            $this->redirect('/users');
        }
    }

    public function update($id) {
        try {
            $user = $this->userModel->findById($id);
            if (!$user) {
                $this->setFlashMessage('error', 'Usuário não encontrado.');
                $this->redirect('/users');
                return;
            }

            $data = [
                'name' => $_POST['name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'active' => isset($_POST['active']) ? 1 : 0,
                'roles' => $_POST['roles'] ?? []
            ];

            if (!empty($_POST['password'])) {
                $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }

            $this->db->beginTransaction();

            $this->userModel->update($id, $data);
            $this->userModel->updateRoles($id, $data['roles']);

            $this->db->commit();

            $this->setFlashMessage('success', 'Usuário atualizado com sucesso!');
            $this->redirect('/users');

        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log('Erro ao atualizar usuário: ' . $e->getMessage());
            $this->setFlashMessage('error', 'Erro ao atualizar usuário.');
            $this->redirect("/users/edit/{$id}");
        }
    }

    public function delete($id) {
        try {
            if ($id == $_SESSION['user_id']) {
                $this->setFlashMessage('error', 'Você não pode excluir seu próprio usuário.');
                $this->redirect('/users');
                return;
            }

            $this->userModel->delete($id);
            $this->setFlashMessage('success', 'Usuário excluído com sucesso!');
            
        } catch (\PDOException $e) {
            error_log('Erro ao excluir usuário: ' . $e->getMessage());
            $this->setFlashMessage('error', 'Erro ao excluir usuário.');
        }
        
        $this->redirect('/users');
    }

    // Gerenciamento de Papéis
    public function roles() {
        try {
            $roles = $this->roleModel->getAll();
            View::render('users/roles/index', [
                'roles' => $roles,
                'title' => 'Papéis de Usuário'
            ]);
        } catch (\PDOException $e) {
            error_log('Erro ao listar papéis: ' . $e->getMessage());
            $this->setFlashMessage('error', 'Erro ao carregar papéis.');
            $this->redirect('/users');
        }
    }

    public function createRole() {
        try {
            $modules = $this->permissionModel->getModules();
            $permissions = $this->permissionModel->getAll();

            View::render('users/roles/create', [
                'modules' => $modules,
                'permissions' => $permissions,
                'title' => 'Novo Papel'
            ]);
        } catch (\PDOException $e) {
            error_log('Erro ao carregar formulário: ' . $e->getMessage());
            $this->setFlashMessage('error', 'Erro ao carregar formulário.');
            $this->redirect('/users/roles');
        }
    }

    public function storeRole() {
        try {
            $data = [
                'name' => $_POST['name'] ?? '',
                'description' => $_POST['description'] ?? '',
                'permissions' => $_POST['permissions'] ?? []
            ];

            if (empty($data['name'])) {
                $this->setFlashMessage('error', 'O nome do papel é obrigatório.');
                $this->redirect('/users/roles/create');
                return;
            }

            $this->db->beginTransaction();

            $roleId = $this->roleModel->create($data);
            if (!empty($data['permissions'])) {
                $this->roleModel->updatePermissions($roleId, $data['permissions']);
            }

            $this->db->commit();

            $this->setFlashMessage('success', 'Papel criado com sucesso!');
            $this->redirect('/users/roles');

        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log('Erro ao criar papel: ' . $e->getMessage());
            $this->setFlashMessage('error', 'Erro ao criar papel.');
            $this->redirect('/users/roles/create');
        }
    }

    public function editRole($id) {
        try {
            $role = $this->roleModel->getById($id);
            if (!$role) {
                $this->setFlashMessage('error', 'Papel não encontrado.');
                $this->redirect('/users/roles');
                return;
            }

            $modules = $this->permissionModel->getModules();
            $permissions = $this->permissionModel->getAll();
            $rolePermissions = $this->permissionModel->getPermissionsByRole($id);

            View::render('users/roles/edit', [
                'role' => $role,
                'modules' => $modules,
                'permissions' => $permissions,
                'rolePermissions' => $rolePermissions,
                'title' => 'Editar Papel'
            ]);
        } catch (\PDOException $e) {
            error_log('Erro ao carregar papel: ' . $e->getMessage());
            $this->setFlashMessage('error', 'Erro ao carregar papel.');
            $this->redirect('/users/roles');
        }
    }

    public function updateRole($id) {
        try {
            $role = $this->roleModel->getById($id);
            if (!$role) {
                $this->setFlashMessage('error', 'Papel não encontrado.');
                $this->redirect('/users/roles');
                return;
            }

            $data = [
                'name' => $_POST['name'] ?? '',
                'description' => $_POST['description'] ?? '',
                'permissions' => $_POST['permissions'] ?? []
            ];

            if (empty($data['name'])) {
                $this->setFlashMessage('error', 'O nome do papel é obrigatório.');
                $this->redirect("/users/roles/edit/{$id}");
                return;
            }

            $this->db->beginTransaction();

            $this->roleModel->update($id, $data);
            $this->roleModel->updatePermissions($id, $data['permissions']);

            $this->db->commit();

            $this->setFlashMessage('success', 'Papel atualizado com sucesso!');
            $this->redirect('/users/roles');

        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log('Erro ao atualizar papel: ' . $e->getMessage());
            $this->setFlashMessage('error', 'Erro ao atualizar papel.');
            $this->redirect("/users/roles/edit/{$id}");
        }
    }

    public function deleteRole($id) {
        try {
            $role = $this->roleModel->getById($id);
            if (!$role) {
                $this->setFlashMessage('error', 'Papel não encontrado.');
                $this->redirect('/users/roles');
                return;
            }

            if ($role['is_system']) {
                $this->setFlashMessage('error', 'Não é possível excluir um papel do sistema.');
                $this->redirect('/users/roles');
                return;
            }

            $this->roleModel->delete($id);
            $this->setFlashMessage('success', 'Papel excluído com sucesso!');
            
        } catch (\PDOException $e) {
            error_log('Erro ao excluir papel: ' . $e->getMessage());
            $this->setFlashMessage('error', 'Erro ao excluir papel.');
        }
        
        $this->redirect('/users/roles');
    }
}
