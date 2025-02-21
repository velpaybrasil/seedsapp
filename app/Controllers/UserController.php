<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Core\Database\Database;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Module;

class UserController extends Controller {
    protected User $userModel;
    protected Role $roleModel;
    protected Permission $permissionModel;
    protected Module $moduleModel;
    protected Database $db;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
        $this->roleModel = new Role();
        $this->permissionModel = new Permission();
        $this->moduleModel = new Module();
        $this->db = Database::getInstance();
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
            $this->setFlash('error', 'Erro ao carregar usuários.');
            $this->redirect('/dashboard');
        }
    }

    public function create() {
        try {
            $roles = $this->roleModel->getAll();
            View::render('users/form', [
                'roles' => $roles,
                'title' => 'Novo Usuário',
                'isEdit' => false,
                'userRoles' => []
            ]);
        } catch (\PDOException $e) {
            error_log('Erro ao carregar papéis: ' . $e->getMessage());
            $this->setFlash('error', 'Erro ao carregar formulário.');
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
                $this->setFlash('error', 'Por favor, preencha todos os campos obrigatórios.');
                $this->redirect('/users/create');
                return;
            }

            if ($this->userModel->findByEmail($data['email'])) {
                $this->setFlash('error', 'Este e-mail já está em uso.');
                $this->redirect('/users/create');
                return;
            }

            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            
            $this->db = Database::getConnection();
            $this->db->beginTransaction();
            
            $userId = $this->userModel->create($data);
            
            // Associar papéis ao usuário
            if (!empty($data['roles'])) {
                $this->userModel->assignRoles($userId, $data['roles']);
            }
            
            $this->db->commit();

            $this->setFlash('success', 'Usuário criado com sucesso!');
            $this->redirect('/users');

        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log('Erro ao criar usuário: ' . $e->getMessage());
            $this->setFlash('error', 'Erro ao criar usuário.');
            $this->redirect('/users/create');
        }
    }

    public function edit($id) {
        try {
            // Busca o usuário
            $user = $this->userModel->find($id);
            if (!$user) {
                $this->setFlash('error', 'Usuário não encontrado.');
                $this->redirect('/users');
                return;
            }

            // Busca os papéis
            $roles = $this->roleModel->getAll();
            
            // Busca os papéis do usuário
            $userRoles = array_column($this->userModel->getUserRoles($id), 'role_id');
            
            // Busca os módulos ativos
            $modules = $this->moduleModel->getActive();
            
            // Busca as permissões do usuário
            $userPermissions = array_map(function($permission) {
                return $permission['module_id'] . '_' . $permission['slug'];
            }, $this->userModel->getUserPermissions($id));

            View::render('users/form', [
                'user' => $user,
                'roles' => $roles,
                'userRoles' => $userRoles,
                'modules' => $modules,
                'userPermissions' => $userPermissions,
                'title' => 'Editar Usuário'
            ]);

        } catch (\PDOException $e) {
            error_log('Erro ao carregar formulário de edição: ' . $e->getMessage());
            $this->setFlash('error', 'Erro ao carregar formulário.');
            $this->redirect('/users');
        }
    }

    public function update($id) {
        try {
            $user = $this->userModel->find((int)$id);
            if (!$user) {
                $this->setFlash('error', 'Usuário não encontrado.');
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

            if (empty($data['name']) || empty($data['email'])) {
                $this->setFlash('error', 'Por favor, preencha todos os campos obrigatórios.');
                $this->redirect("/users/{$id}/edit");
                return;
            }

            // Verificar se o e-mail já está em uso por outro usuário
            $existingUser = $this->userModel->findByEmail($data['email']);
            if ($existingUser && $existingUser['id'] != $id) {
                $this->setFlash('error', 'Este e-mail já está em uso.');
                $this->redirect("/users/{$id}/edit");
                return;
            }

            $this->db = Database::getConnection();
            $this->db->beginTransaction();

            $this->userModel->update((int)$id, $data);
            
            // Atualizar papéis do usuário
            $this->userModel->updateRoles((int)$id, $data['roles']);

            $this->db->commit();

            $this->setFlash('success', 'Usuário atualizado com sucesso!');
            $this->redirect('/users');

        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log('Erro ao atualizar usuário: ' . $e->getMessage());
            $this->setFlash('error', 'Erro ao atualizar usuário.');
            $this->redirect("/users/{$id}/edit");
        }
    }

    public function delete($id) {
        try {
            $user = $this->userModel->find($id);
            if (!$user) {
                $this->setFlash('error', 'Usuário não encontrado.');
                $this->redirect('/users');
                return;
            }

            // Não permitir excluir o próprio usuário logado
            if ($user['id'] == $_SESSION['user_id']) {
                $this->setFlash('error', 'Você não pode excluir seu próprio usuário.');
                $this->redirect('/users');
                return;
            }

            $this->db = Database::getConnection();
            $this->db->beginTransaction();

            // Remover todas as associações de papéis
            $this->userModel->removeAllRoles($id);

            // Excluir o usuário
            $this->userModel->delete($id);

            $this->db->commit();

            $this->setFlash('success', 'Usuário excluído com sucesso!');
            $this->redirect('/users');

        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log('Erro ao excluir usuário: ' . $e->getMessage());
            $this->setFlash('error', 'Erro ao excluir usuário.');
            $this->redirect('/users');
        }
    }

    public function roles() {
        try {
            $roles = $this->roleModel->getAll();
            View::render('users/roles/index', [
                'roles' => $roles,
                'title' => 'Papéis de Usuário'
            ]);
        } catch (\PDOException $e) {
            error_log('Erro ao listar papéis: ' . $e->getMessage());
            $this->setFlash('error', 'Erro ao carregar papéis.');
            $this->redirect('/dashboard');
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
            error_log('Erro ao carregar formulário de papel: ' . $e->getMessage());
            $this->setFlash('error', 'Erro ao carregar formulário.');
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
                $this->setFlash('error', 'Por favor, preencha o nome do papel.');
                $this->redirect('/users/roles/create');
                return;
            }

            $this->db = Database::getConnection();
            $this->db->beginTransaction();

            $roleId = $this->roleModel->create($data);
            
            if (!empty($data['permissions'])) {
                $this->roleModel->updatePermissions($roleId, $data['permissions']);
            }

            $this->db->commit();

            $this->setFlash('success', 'Papel criado com sucesso!');
            $this->redirect('/users/roles');

        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log('Erro ao criar papel: ' . $e->getMessage());
            $this->setFlash('error', 'Erro ao criar papel.');
            $this->redirect('/users/roles/create');
        }
    }

    public function editRole($id) {
        try {
            $role = $this->roleModel->getById($id);
            if (!$role) {
                $this->setFlash('error', 'Papel não encontrado.');
                $this->redirect('/users/roles');
                return;
            }

            $modules = $this->permissionModel->getModules();
            $permissions = $this->permissionModel->getAll();
            $rolePermissions = array_column($this->roleModel->getPermissions($id), 'id');

            View::render('users/roles/edit', [
                'role' => $role,
                'modules' => $modules,
                'permissions' => $permissions,
                'rolePermissions' => $rolePermissions,
                'title' => 'Editar Papel'
            ]);
        } catch (\PDOException $e) {
            error_log('Erro ao carregar papel: ' . $e->getMessage());
            $this->setFlash('error', 'Erro ao carregar papel.');
            $this->redirect('/users/roles');
        }
    }

    public function updateRole($id) {
        try {
            $role = $this->roleModel->getById((int)$id);
            if (!$role) {
                $this->setFlash('error', 'Papel não encontrado.');
                $this->redirect('/users/roles');
                return;
            }

            $data = [
                'name' => $_POST['name'] ?? '',
                'description' => $_POST['description'] ?? '',
                'permissions' => $_POST['permissions'] ?? []
            ];

            if (empty($data['name'])) {
                $this->setFlash('error', 'Por favor, preencha o nome do papel.');
                $this->redirect("/users/roles/{$id}/edit");
                return;
            }

            $this->db = Database::getConnection();
            $this->db->beginTransaction();

            $this->roleModel->update($id, $data);
            $this->roleModel->updatePermissions($id, $data['permissions']);

            $this->db->commit();

            $this->setFlash('success', 'Papel atualizado com sucesso!');
            $this->redirect('/users/roles');

        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log('Erro ao atualizar papel: ' . $e->getMessage());
            $this->setFlash('error', 'Erro ao atualizar papel.');
            $this->redirect("/users/roles/{$id}/edit");
        }
    }

    public function deleteRole($id) {
        try {
            $role = $this->roleModel->getById($id);
            if (!$role) {
                $this->setFlash('error', 'Papel não encontrado.');
                $this->redirect('/users/roles');
                return;
            }

            // Não permitir excluir papéis do sistema
            if ($role['is_system']) {
                $this->setFlash('error', 'Não é possível excluir papéis do sistema.');
                $this->redirect('/users/roles');
                return;
            }

            $this->db = Database::getConnection();
            $this->db->beginTransaction();

            // Remover todas as permissões do papel
            $this->roleModel->updatePermissions($id, []);

            // Excluir o papel
            $this->roleModel->delete($id);

            $this->db->commit();

            $this->setFlash('success', 'Papel excluído com sucesso!');
            $this->redirect('/users/roles');

        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log('Erro ao excluir papel: ' . $e->getMessage());
            $this->setFlash('error', 'Erro ao excluir papel.');
            $this->redirect('/users/roles');
        }
    }
}
