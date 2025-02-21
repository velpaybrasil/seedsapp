<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\SystemSetting;

class SystemSettingsController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->requireAuth();
        
        // Verificar se o usuário tem permissão para gerenciar configurações
        if (!$this->checkPermission('settings', 'manage')) {
            $this->setFlash('error', 'Você não tem permissão para acessar as configurações do sistema.');
            $this->redirect('/dashboard');
        }
    }
    
    public function index() {
        $categories = SystemSetting::getAllCategories();
        $settings = [];
        
        foreach ($categories as $category) {
            $settings[$category] = SystemSetting::getByCategory($category, true);
        }
        
        $this->view('settings/system/index', [
            'title' => 'Configurações do Sistema',
            'categories' => $categories,
            'settings' => $settings
        ]);
    }
    
    public function create() {
        $this->view('settings/system/create', [
            'title' => 'Nova Configuração'
        ]);
    }
    
    public function store() {
        $this->validateCSRF();
        
        $data = $this->validateRequest([
            'category' => 'required|min:2|max:50',
            'key_name' => 'required|min:2|max:100',
            'value' => 'required',
            'description' => 'required',
            'is_public' => 'boolean'
        ]);
        
        if ($this->getFormErrors()) {
            return $this->redirect('/settings/system/create');
        }
        
        try {
            SystemSetting::set(
                $data['category'],
                $data['key_name'],
                $data['value'],
                $data['description'],
                $data['is_public'] ?? false
            );
            
            $this->setFlash('success', 'Configuração criada com sucesso!');
            return $this->redirect('/settings/system');
        } catch (\Exception $e) {
            $this->setFlash('error', 'Erro ao criar configuração: ' . $e->getMessage());
            return $this->redirect('/settings/system/create');
        }
    }
    
    public function edit(string $category, string $key) {
        $setting = SystemSetting::get($category, $key);
        
        if (!$setting) {
            $this->setFlash('error', 'Configuração não encontrada');
            return $this->redirect('/settings/system');
        }
        
        $this->view('settings/system/edit', [
            'title' => 'Editar Configuração',
            'setting' => $setting,
            'category' => $category,
            'key' => $key
        ]);
    }
    
    public function update(string $category, string $key) {
        $this->validateCSRF();
        
        $data = $this->validateRequest([
            'value' => 'required',
            'description' => 'required',
            'is_public' => 'boolean'
        ]);
        
        if ($this->getFormErrors()) {
            return $this->redirect("/settings/system/{$category}/{$key}/edit");
        }
        
        try {
            SystemSetting::set(
                $category,
                $key,
                $data['value'],
                $data['description'],
                $data['is_public'] ?? false
            );
            
            $this->setFlash('success', 'Configuração atualizada com sucesso!');
            return $this->redirect('/settings/system');
        } catch (\Exception $e) {
            $this->setFlash('error', 'Erro ao atualizar configuração: ' . $e->getMessage());
            return $this->redirect("/settings/system/{$category}/{$key}/edit");
        }
    }
    
    public function delete(string $category, string $key) {
        $this->validateCSRF();
        
        try {
            if (SystemSetting::deleteSetting($category, $key)) {
                $this->setFlash('success', 'Configuração excluída com sucesso!');
            } else {
                $this->setFlash('error', 'Configuração não encontrada');
            }
        } catch (\Exception $e) {
            $this->setFlash('error', 'Erro ao excluir configuração: ' . $e->getMessage());
        }
        
        return $this->redirect('/settings/system');
    }
}
