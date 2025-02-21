<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\VisitorForm;
use App\Models\VisitorFormField;
use App\Models\VisitorFormSubmission;

class VisitorFormsController extends Controller
{
    public function index()
    {
        $this->checkPermission('visitor_forms', 'view');
        $forms = VisitorForm::getAll();
        
        // Adicionar contagem de submissões para cada formulário
        foreach ($forms as &$form) {
            $form['submissions_count'] = VisitorFormSubmission::countByFormId($form['id']);
        }
        
        return $this->view('visitor_forms/index', ['forms' => $forms]);
    }

    public function create()
    {
        $this->checkPermission('visitor_forms', 'create');
        return $this->view('visitor_forms/create');
    }

    public function store()
    {
        $this->checkPermission('visitor_forms', 'create');

        try {
            // Validar dados básicos do formulário
            if (empty($_POST['title'])) {
                throw new \Exception('O título do formulário é obrigatório');
            }

            $formData = [
                'title' => trim($_POST['title']),
                'description' => isset($_POST['description']) ? trim($_POST['description']) : '',
                'theme_color' => isset($_POST['theme_color']) ? trim($_POST['theme_color']) : '#007bff',
                'active' => isset($_POST['active']) ? 1 : 0,
                'slug' => $this->createSlug($_POST['title'])
            ];

            // Validar se já existe um formulário com o mesmo slug
            if (VisitorForm::findBySlug($formData['slug'])) {
                throw new \Exception('Já existe um formulário com este título');
            }

            // Iniciar transação
            $this->db->beginTransaction();

            // Criar o formulário
            $formId = VisitorForm::create($formData);

            if (!$formId) {
                throw new \Exception('Erro ao criar o formulário');
            }

            // Processar os campos do formulário
            if (!isset($_POST['fields']) || !is_array($_POST['fields'])) {
                throw new \Exception('É necessário definir pelo menos um campo no formulário');
            }

            foreach ($_POST['fields'] as $index => $field) {
                if (empty($field['field_name']) || empty($field['field_label']) || empty($field['field_type'])) {
                    throw new \Exception('Todos os campos precisam ter nome, rótulo e tipo definidos');
                }

                $fieldData = [
                    'form_id' => $formId,
                    'field_name' => trim($field['field_name']),
                    'field_label' => trim($field['field_label']),
                    'field_type' => trim($field['field_type']),
                    'is_required' => isset($field['is_required']) ? 1 : 0,
                    'display_order' => isset($field['display_order']) ? (int)$field['display_order'] : $index,
                    'placeholder' => isset($field['placeholder']) ? trim($field['placeholder']) : '',
                    'help_text' => isset($field['help_text']) ? trim($field['help_text']) : ''
                ];

                if (!VisitorFormField::create($fieldData)) {
                    throw new \Exception('Erro ao criar campo do formulário');
                }
            }

            // Commit da transação
            $this->db->commit();

            $this->flash->success('Formulário criado com sucesso!');
            return $this->redirect('/visitor-forms');

        } catch (\Exception $e) {
            // Rollback em caso de erro
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            $this->flash->error('Erro ao criar formulário: ' . $e->getMessage());
            return $this->redirect('/visitor-forms/create');
        }
    }

    public function show($id)
    {
        $this->checkPermission('visitor_forms', 'view');
        
        $form = VisitorForm::find($id);
        if (!$form) {
            $this->flash->error('Formulário não encontrado');
            return $this->redirect('/visitor-forms');
        }

        $fields = VisitorFormField::getByFormId($id);
        return $this->view('visitor_forms/show', ['form' => $form, 'fields' => $fields]);
    }

    public function showPublic($slug)
    {
        $form = VisitorForm::findBySlug($slug);
        if (!$form || !$form['active']) {
            $this->flash->error('Formulário não encontrado ou inativo');
            return $this->redirect('/');
        }

        $fields = VisitorFormField::getByFormId($form['id']);
        return $this->view('visitor_forms/show', ['form' => $form, 'fields' => $fields]);
    }

    public function edit($id)
    {
        $this->checkPermission('visitor_forms', 'edit');
        
        $form = VisitorForm::find($id);
        if (!$form) {
            $this->flash->error('Formulário não encontrado');
            return $this->redirect('/visitor-forms');
        }

        $fields = VisitorFormField::getByFormId($id);
        return $this->view('visitor_forms/edit', ['form' => $form, 'fields' => $fields]);
    }

    public function update($id)
    {
        $this->checkPermission('visitor_forms', 'edit');

        try {
            $form = VisitorForm::find($id);
            if (!$form) {
                throw new \Exception('Formulário não encontrado');
            }

            // Validar dados básicos do formulário
            if (empty($_POST['title'])) {
                throw new \Exception('O título do formulário é obrigatório');
            }

            $formData = [
                'title' => trim($_POST['title']),
                'description' => isset($_POST['description']) ? trim($_POST['description']) : '',
                'theme_color' => isset($_POST['theme_color']) ? trim($_POST['theme_color']) : '#007bff',
                'active' => isset($_POST['active']) ? 1 : 0
            ];

            // Se o título mudou, atualizar o slug
            if ($formData['title'] !== $form['title']) {
                $formData['slug'] = $this->createSlug($formData['title']);
                
                // Verificar se o novo slug já existe
                $existingForm = VisitorForm::findBySlug($formData['slug']);
                if ($existingForm && $existingForm['id'] != $id) {
                    throw new \Exception('Já existe um formulário com este título');
                }
            }

            // Iniciar transação
            $this->db->beginTransaction();

            // Atualizar o formulário
            if (!VisitorForm::update($id, $formData)) {
                throw new \Exception('Erro ao atualizar o formulário');
            }

            // Atualizar campos
            if (!isset($_POST['fields']) || !is_array($_POST['fields'])) {
                throw new \Exception('É necessário definir pelo menos um campo no formulário');
            }

            // Remover campos existentes
            VisitorFormField::deleteByFormId($id);

            // Criar novos campos
            foreach ($_POST['fields'] as $index => $field) {
                if (empty($field['field_name']) || empty($field['field_label']) || empty($field['field_type'])) {
                    throw new \Exception('Todos os campos precisam ter nome, rótulo e tipo definidos');
                }

                $fieldData = [
                    'form_id' => $id,
                    'field_name' => trim($field['field_name']),
                    'field_label' => trim($field['field_label']),
                    'field_type' => trim($field['field_type']),
                    'is_required' => isset($field['is_required']) ? 1 : 0,
                    'display_order' => isset($field['display_order']) ? (int)$field['display_order'] : $index,
                    'placeholder' => isset($field['placeholder']) ? trim($field['placeholder']) : '',
                    'help_text' => isset($field['help_text']) ? trim($field['help_text']) : ''
                ];

                if (!VisitorFormField::create($fieldData)) {
                    throw new \Exception('Erro ao atualizar campo do formulário');
                }
            }

            // Commit da transação
            $this->db->commit();

            $this->flash->success('Formulário atualizado com sucesso!');
            return $this->redirect('/visitor-forms');

        } catch (\Exception $e) {
            // Rollback em caso de erro
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            $this->flash->error('Erro ao atualizar formulário: ' . $e->getMessage());
            return $this->redirect("/visitor-forms/{$id}/edit");
        }
    }

    public function delete($id)
    {
        $this->checkPermission('visitor_forms', 'delete');

        try {
            $form = VisitorForm::find($id);
            if (!$form) {
                throw new \Exception('Formulário não encontrado');
            }

            // Iniciar transação
            $this->db->beginTransaction();

            // Remover campos
            VisitorFormField::deleteByFormId($id);

            // Remover formulário
            if (!VisitorForm::delete($id)) {
                throw new \Exception('Erro ao excluir o formulário');
            }

            // Commit da transação
            $this->db->commit();

            $this->flash->success('Formulário excluído com sucesso!');
            return $this->redirect('/visitor-forms');

        } catch (\Exception $e) {
            // Rollback em caso de erro
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            $this->flash->error('Erro ao excluir formulário: ' . $e->getMessage());
            return $this->redirect('/visitor-forms');
        }
    }

    public function submissions($formId)
    {
        $this->checkPermission('visitor_forms', 'view');

        $form = VisitorForm::find($formId);
        if (!$form) {
            $this->flash->error('Formulário não encontrado');
            return $this->redirect('/visitor-forms');
        }

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 20;

        $submissions = VisitorFormSubmission::getByFormId($formId, $page, $perPage);
        $totalSubmissions = VisitorFormSubmission::countByFormId($formId);
        $totalPages = ceil($totalSubmissions / $perPage);

        return $this->view('visitor_forms/submissions', [
            'form' => $form,
            'submissions' => $submissions,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }

    public function viewSubmission($formId, $submissionId)
    {
        $this->checkPermission('visitor_forms', 'view');

        $form = VisitorForm::find($formId);
        if (!$form) {
            $this->flash->error('Formulário não encontrado');
            return $this->redirect('/visitor-forms');
        }

        $submission = VisitorFormSubmission::find($submissionId);
        if (!$submission || $submission['form_id'] != $formId) {
            $this->flash->error('Submissão não encontrada');
            return $this->redirect("/visitor-forms/{$formId}/submissions");
        }

        $fields = VisitorFormField::getByFormId($formId);

        return $this->view('visitor_forms/view_submission', [
            'form' => $form,
            'submission' => $submission,
            'fields' => $fields
        ]);
    }

    private function createSlug($title)
    {
        // Remove acentos
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $title);
        // Converte para minúsculas
        $slug = strtolower($slug);
        // Remove caracteres especiais
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        // Remove hífens múltiplos
        $slug = preg_replace('/-+/', '-', $slug);
        // Remove hífens do início e fim
        $slug = trim($slug, '-');
        
        return $slug;
    }
}
