<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Models\SystemModule;
use App\Models\UserPermission;
use PDOException;

class SystemModuleController extends Controller {
    private SystemModule $moduleModel;
    private UserPermission $permissionModel;

    public function __construct() {
        parent::__construct();
        $this->moduleModel = new SystemModule();
        $this->permissionModel = new UserPermission();
    }

    public function index() {
        try {
            if (!$this->userModel->hasPermission($_SESSION['user_id'], 'settings', 'view')) {
                $this->setFlashMessage('error', 'Você não tem permissão para acessar esta página.');
                $this->redirect('/dashboard');
                return;
            }

            $modules = $this->moduleModel->getModuleHierarchy();
            View::render('system_modules/index', ['modules' => $modules]);
        } catch (PDOException $e) {
            error_log('Erro ao listar módulos: ' . $e->getMessage());
            $this->setFlashMessage('error', 'Erro ao carregar módulos do sistema.');
            $this->redirect('/dashboard');
        }
    }

    public function create() {
        try {
            if (!$this->userModel->hasPermission($_SESSION['user_id'], 'settings', 'create')) {
                $this->setFlashMessage('error', 'Você não tem permissão para criar módulos.');
                $this->redirect('/system-modules');
                return;
            }

            $modules = $this->moduleModel->getAll();
            View::render('system_modules/create', ['modules' => $modules]);
        } catch (PDOException $e) {
            error_log('Erro ao carregar formulário de criação de módulo: ' . $e->getMessage());
            $this->setFlashMessage('error', 'Erro ao carregar formulário.');
            $this->redirect('/system-modules');
        }
    }

    public function store() {
        try {
            if (!$this->userModel->hasPermission($_SESSION['user_id'], 'settings', 'create')) {
                $this->setFlashMessage('error', 'Você não tem permissão para criar módulos.');
                $this->redirect('/system-modules');
                return;
            }

            $data = [
                'name' => $_POST['name'] ?? '',
                'description' => $_POST['description'] ?? '',
                'slug' => $_POST['slug'] ?? '',
                'icon' => $_POST['icon'] ?? null,
                'parent_id' => !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null,
                'order_index' => (int)($_POST['order_index'] ?? 0),
                'active' => isset($_POST['active']) ? 1 : 0
            ];

            if (empty($data['name']) || empty($data['slug'])) {
                $this->setFlashMessage('error', 'Nome e slug são obrigatórios.');
                $this->redirect('/system-modules/create');
                return;
            }

            // Verifica se o slug já existe
            if ($this->moduleModel->findBySlug($data['slug'])) {
                $this->setFlashMessage('error', 'Este slug já está em uso.');
                $this->redirect('/system-modules/create');
                return;
            }

            $this->moduleModel->create($data);
            $this->setFlashMessage('success', 'Módulo criado com sucesso!');
            $this->redirect('/system-modules');

        } catch (PDOException $e) {
            error_log('Erro ao criar módulo: ' . $e->getMessage());
            $this->setFlashMessage('error', 'Erro ao criar módulo.');
            $this->redirect('/system-modules/create');
        }
    }

    public function edit($id) {
        try {
            if (!$this->userModel->hasPermission($_SESSION['user_id'], 'settings', 'edit')) {
                $this->setFlashMessage('error', 'Você não tem permissão para editar módulos.');
                $this->redirect('/system-modules');
                return;
            }

            $module = $this->moduleModel->findById($id);
            if (!$module) {
                $this->setFlashMessage('error', 'Módulo não encontrado.');
                $this->redirect('/system-modules');
                return;
            }

            $modules = $this->moduleModel->getAll();
            View::render('system_modules/edit', [
                'module' => $module,
                'modules' => $modules
            ]);

        } catch (PDOException $e) {
            error_log('Erro ao carregar módulo para edição: ' . $e->getMessage());
            $this->setFlashMessage('error', 'Erro ao carregar módulo.');
            $this->redirect('/system-modules');
        }
    }

    public function update($id) {
        try {
            if (!$this->userModel->hasPermission($_SESSION['user_id'], 'settings', 'edit')) {
                $this->setFlashMessage('error', 'Você não tem permissão para editar módulos.');
                $this->redirect('/system-modules');
                return;
            }

            $module = $this->moduleModel->findById($id);
            if (!$module) {
                $this->setFlashMessage('error', 'Módulo não encontrado.');
                $this->redirect('/system-modules');
                return;
            }

            $data = [
                'name' => $_POST['name'] ?? '',
                'description' => $_POST['description'] ?? '',
                'slug' => $_POST['slug'] ?? '',
                'icon' => $_POST['icon'] ?? null,
                'parent_id' => !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null,
                'order_index' => (int)($_POST['order_index'] ?? 0),
                'active' => isset($_POST['active']) ? 1 : 0
            ];

            if (empty($data['name']) || empty($data['slug'])) {
                $this->setFlashMessage('error', 'Nome e slug são obrigatórios.');
                $this->redirect("/system-modules/{$id}/edit");
                return;
            }

            // Verifica se o slug já existe em outro módulo
            $existingModule = $this->moduleModel->findBySlug($data['slug']);
            if ($existingModule && $existingModule['id'] != $id) {
                $this->setFlashMessage('error', 'Este slug já está em uso por outro módulo.');
                $this->redirect("/system-modules/{$id}/edit");
                return;
            }

            $this->moduleModel->update($id, $data);
            $this->setFlashMessage('success', 'Módulo atualizado com sucesso!');
            $this->redirect('/system-modules');

        } catch (PDOException $e) {
            error_log('Erro ao atualizar módulo: ' . $e->getMessage());
            $this->setFlashMessage('error', 'Erro ao atualizar módulo.');
            $this->redirect("/system-modules/{$id}/edit");
        }
    }

    public function delete($id) {
        try {
            if (!$this->userModel->hasPermission($_SESSION['user_id'], 'settings', 'delete')) {
                $this->setFlashMessage('error', 'Você não tem permissão para excluir módulos.');
                $this->redirect('/system-modules');
                return;
            }

            $module = $this->moduleModel->findById($id);
            if (!$module) {
                $this->setFlashMessage('error', 'Módulo não encontrado.');
                $this->redirect('/system-modules');
                return;
            }

            $this->moduleModel->delete($id);
            $this->setFlashMessage('success', 'Módulo excluído com sucesso!');
            $this->redirect('/system-modules');

        } catch (\Exception $e) {
            error_log('Erro ao excluir módulo: ' . $e->getMessage());
            $this->setFlashMessage('error', $e->getMessage());
            $this->redirect('/system-modules');
        }
    }
}
