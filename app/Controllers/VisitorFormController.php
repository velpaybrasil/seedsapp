<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Models\Visitor;
use App\Models\VisitorForm;
use App\Models\VisitorFormSubmission;
use App\Core\Database\Database;
use App\Core\Session;

class VisitorFormController extends Controller {
    private $visitorModel;
    protected $db;
    protected $session;
    protected $submissionModel;
    
    public function __construct() {
        parent::__construct();
        $this->visitorModel = new Visitor();
        $this->db = Database::getInstance()->getConnection();
        $this->session = new Session();
        $this->submissionModel = new VisitorFormSubmission();
    }
    
    public function index() {
        $page = $_GET['page'] ?? 1;
        $perPage = 10;
        
        $forms = VisitorForm::paginate($page, $perPage);
        foreach ($forms as &$form) {
            $form['submissions_count'] = VisitorForm::countSubmissions($form['id']);
        }
        
        View::render('visitor_forms/index', [
            'title' => 'Formulários de Visitantes',
            'forms' => $forms,
            'currentPage' => $page,
            'totalPages' => ceil(VisitorForm::count() / $perPage)
        ]);
    }
    
    public function create() {
        try {
            // Verifica se o usuário está autenticado
            if (!$this->isLoggedIn()) {
                $this->setFlash('error', 'Você precisa estar logado para acessar esta página.');
                redirect('/login');
                return;
            }

            View::render('visitor_forms/edit', [
                'title' => 'Novo Formulário',
                'form' => null,
                'fields' => [],
                'errors' => $_SESSION['errors'] ?? [],
                'old' => $_SESSION['old'] ?? []
            ]);

            // Limpa os dados da sessão após renderizar
            unset($_SESSION['errors'], $_SESSION['old']);
        } catch (\Exception $e) {
            error_log('Erro ao renderizar formulário de visitante: ' . $e->getMessage());
            $this->setFlash('error', 'Erro ao carregar o formulário. Por favor, tente novamente.');
            redirect('/visitor-forms');
        }
    }
    
    public function store() {
        $data = $this->validateFormData($_POST);
        if (!$data) {
            return;
        }
        
        $formId = VisitorForm::create($data);
        if ($formId) {
            $this->setFlash('success', 'Formulário criado com sucesso!');
            redirect("/visitor-forms/{$formId}/edit");
        } else {
            $this->setFlash('error', 'Erro ao criar formulário.');
            redirect('/visitor-forms/create');
        }
    }
    
    public function edit($id) {
        $form = VisitorForm::find($id);
        if (!$form) {
            $this->setFlash('error', 'Formulário não encontrado.');
            redirect('/visitor-forms');
        }
        
        $fields = VisitorForm::getFields($id);
        
        View::render('visitor_forms/edit', [
            'title' => 'Editar Formulário de Visitante',
            'form' => $form,
            'fields' => $fields
        ]);
    }
    
    public function update($id) {
        $form = VisitorForm::find($id);
        if (!$form) {
            $this->setFlash('error', 'Formulário não encontrado.');
            redirect('/visitor-forms');
        }
        
        $data = $this->validateFormData($_POST);
        if (!$data) {
            return;
        }
        
        if (VisitorForm::update($id, $data)) {
            $this->setFlash('success', 'Formulário atualizado com sucesso!');
        } else {
            $this->setFlash('error', 'Erro ao atualizar formulário.');
        }
        
        redirect("/visitor-forms/{$id}/edit");
    }
    
    public function delete($id) {
        if (VisitorForm::delete($id)) {
            $this->setFlash('success', 'Formulário excluído com sucesso!');
        } else {
            $this->setFlash('error', 'Erro ao excluir formulário.');
        }
        
        redirect('/visitor-forms');
    }
    
    public function addField($formId) {
        // Verificar CSRF token
        $csrfToken = $_POST['csrf_token'] ?? $_SERVER['HTTP_CSRF_TOKEN'] ?? null;
        if (!$csrfToken || $csrfToken !== $_SESSION['csrf_token']) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Token CSRF inválido']);
            return;
        }

        // Obter dados do campo
        $input = file_get_contents('php://input');
        if (!empty($input)) {
            // Se os dados vierem como JSON
            $data = json_decode($input, true);
        } else {
            // Se os dados vierem como FormData
            $data = $_POST;
        }

        $validatedData = $this->validateFieldData($data);
        if (!$validatedData['success']) {
            http_response_code(400);
            echo json_encode($validatedData);
            return;
        }
        
        if (VisitorForm::addField($formId, $validatedData['data'])) {
            echo json_encode(['success' => true, 'message' => 'Campo adicionado com sucesso!']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erro ao adicionar campo.']);
        }
    }
    
    public function updateField($formId, $fieldId) {
        $data = $this->validateFieldData($_POST);
        if (!$data['success']) {
            return;
        }
        
        if (VisitorForm::updateField($fieldId, $data['data'])) {
            $this->setFlash('success', 'Campo atualizado com sucesso!');
        } else {
            $this->setFlash('error', 'Erro ao atualizar campo.');
        }
        
        redirect("/visitor-forms/{$formId}/edit");
    }
    
    public function deleteField($formId, $fieldId) {
        // Verificar CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Token CSRF inválido']);
            return;
        }

        if (VisitorForm::deleteField($fieldId)) {
            echo json_encode(['success' => true, 'message' => 'Campo excluído com sucesso!']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erro ao excluir campo.']);
        }
    }
    
    protected function getDB()
    {
        return \App\Core\Database\Database::getInstance()->getConnection();
    }

    protected function isAjaxRequest(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    public function updateFieldOrder($formId)
    {
        if (!$this->isAjaxRequest() || !isset($_POST['fields'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Requisição inválida']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            $data = ['fields' => $_POST['fields']];
        }

        try {
            $db = $this->getDB();
            $db->beginTransaction();

            foreach ($data['fields'] as $field) {
                if (!isset($field['id']) || !isset($field['display_order'])) {
                    continue;
                }

                $sql = "UPDATE visitor_form_fields SET display_order = :display_order WHERE id = :id AND form_id = :form_id";
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    'display_order' => $field['display_order'],
                    'id' => $field['id'],
                    'form_id' => $formId
                ]);
            }

            $db->commit();
            echo json_encode(['success' => true, 'message' => 'Ordem dos campos atualizada com sucesso']);
        } catch (\Exception $e) {
            if ($db && $db->inTransaction()) {
                $db->rollBack();
            }
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erro ao atualizar ordem dos campos']);
        }
    }
    
    public function show($slug) {
        $form = VisitorForm::findBySlug($slug);
        if (!$form) {
            $this->setFlash('error', 'Formulário não encontrado.');
            redirect('/');
        }
        
        // Se o formulário estiver inativo e o usuário não estiver logado, redireciona
        if (!$form['active'] && !$this->isLoggedIn()) {
            $this->setFlash('error', 'Formulário não encontrado ou inativo.');
            redirect('/');
        }
        
        $fields = VisitorForm::getFields($form['id']);
        
        View::render('visitor_forms/show', [
            'title' => 'Formulário de Visitante',
            'form' => $form,
            'fields' => $fields
        ]);
    }
    
    public function submit($slug) {
        $form = VisitorForm::findBySlug($slug);
        if (!$form) {
            $this->setFlash('error', 'Formulário não encontrado.');
            redirect('/');
        }

        if (!$form['active']) {
            $this->setFlash('error', 'Este formulário não está mais aceitando respostas.');
            redirect('/');
        }

        $fields = VisitorForm::getFields($form['id']);
        $data = $this->validateSubmissionData($_POST, $fields);
        
        if (!$data['success']) {
            $this->setFlash('error', implode('<br>', $data['errors']));
            redirect('/f/' . $slug);
        }

        // Criar o visitante
        $visitorData = [
            'name' => $data['data']['name'] ?? null,
            'email' => $data['data']['email'] ?? null,
            'phone' => $data['data']['phone'] ?? null,
            'birth_date' => $data['data']['birth_date'] ?? null,
            'address' => $data['data']['address'] ?? null,
            'neighborhood' => $data['data']['neighborhood'] ?? null,
            'city' => $data['data']['city'] ?? null,
            'state' => $data['data']['state'] ?? null,
            'zip_code' => $data['data']['zip_code'] ?? null,
            'marital_status' => $data['data']['marital_status'] ?? null,
            'has_children' => isset($data['data']['has_children']) ? $data['data']['has_children'] == 'yes' : false,
            'number_of_children' => $data['data']['number_of_children'] ?? null,
            'profession' => $data['data']['profession'] ?? null,
            'church_member' => isset($data['data']['church_member']) ? $data['data']['church_member'] == 'yes' : false,
            'previous_church' => $data['data']['previous_church'] ?? null,
            'conversion_date' => $data['data']['conversion_date'] ?? null,
            'baptism_date' => $data['data']['baptism_date'] ?? null,
            'prayer_requests' => $data['data']['prayer_requests'] ?? null,
            'observations' => $data['data']['observations'] ?? null,
            'source' => 'form_' . $form['id'],
            'status' => 'pending'
        ];

        $visitorId = $this->visitorModel->create($visitorData);
        if (!$visitorId) {
            $this->setFlash('error', 'Erro ao salvar os dados. Por favor, tente novamente.');
            redirect('/f/' . $slug);
        }

        // Salvar a submissão
        $submissionData = [
            'form_id' => $form['id'],
            'visitor_id' => $visitorId,
            'data' => json_encode($data['data']),
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT']
        ];

        if ($this->createSubmission($form['id'], $submissionData)) {
            $this->setFlash('success', 'Formulário enviado com sucesso! Entraremos em contato em breve.');
        } else {
            $this->setFlash('error', 'Erro ao salvar os dados. Por favor, tente novamente.');
        }

        redirect('/f/' . $slug);
    }
    
    public function submissions($id) {
        $form = VisitorForm::find($id);
        if (!$form) {
            $this->setFlash('error', 'Formulário não encontrado.');
            redirect('/visitor-forms');
        }
        
        $page = $_GET['page'] ?? 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $submissions = VisitorForm::getSubmissions($id, $limit, $offset);
        $totalSubmissions = VisitorForm::countSubmissions($id);
        
        View::render('visitor_forms/submissions', [
            'title' => 'Submissões do Formulário',
            'form' => $form,
            'submissions' => $submissions,
            'currentPage' => $page,
            'totalPages' => ceil($totalSubmissions / $limit)
        ]);
    }
    
    public function createVisitorFromSubmission($submissionId) {
        $submission = $this->findSubmission($submissionId);
        if (!$submission) {
            $this->setFlash('error', 'Submissão não encontrada.');
            redirect('/visitor-forms');
        }
        
        $data = json_decode($submission['data'], true);
        $visitorData = [
            'name' => $data['name'] ?? '',
            'email' => $data['email'] ?? '',
            'phone' => $data['phone'] ?? '',
            'notes' => 'Criado a partir do formulário #' . $submission['form_id']
        ];
        
        $visitorId = $this->visitorModel->create($visitorData);
        if ($visitorId) {
            $this->updateSubmission($submissionId, ['visitor_id' => $visitorId]);
            $this->setFlash('success', 'Visitante criado com sucesso!');
            redirect('/visitors/' . $visitorId);
        } else {
            $this->setFlash('error', 'Erro ao criar visitante.');
            redirect('/visitor-forms/' . $submission['form_id'] . '/submissions');
        }
    }
    
    public function deleteSubmission($id) {
        $submission = $this->findSubmission($id);
        if (!$submission) {
            $this->setFlash('error', 'Submissão não encontrada.');
            redirect('/visitor-forms');
        }
        
        if ($this->submissionModel->delete($id)) {
            $this->setFlash('success', 'Submissão excluída com sucesso!');
        } else {
            $this->setFlash('error', 'Erro ao excluir submissão.');
        }
        
        redirect('/visitor-forms');
    }
    
    private function validateFormData($data) {
        $errors = [];
        
        // Validar título
        if (empty($data['title'])) {
            $errors[] = 'O título do formulário é obrigatório.';
        }
        
        // Validar URL do logo (se fornecida)
        if (!empty($data['logo_url']) && !filter_var($data['logo_url'], FILTER_VALIDATE_URL)) {
            $errors[] = 'A URL do logo é inválida.';
        }
        
        // Validar cor do tema (se fornecida)
        if (!empty($data['theme_color']) && !preg_match('/^#[a-f0-9]{6}$/i', $data['theme_color'])) {
            $errors[] = 'A cor do tema deve estar no formato hexadecimal (ex: #000000).';
        }
        
        if (!empty($errors)) {
            $this->setFlash('error', implode('<br>', $errors));
            $_SESSION['old'] = $data;
            return false;
        }
        
        // Gerar slug a partir do título se não existir
        if (empty($data['slug'])) {
            $data['slug'] = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['title'])));
        }
        
        return [
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'logo_url' => $data['logo_url'] ?? null,
            'theme_color' => $data['theme_color'] ?? '#0d6efd',
            'footer_text' => $data['footer_text'] ?? null,
            'active' => !empty($data['active']),
            'slug' => $data['slug']
        ];
    }
    
    private function validateFieldData($data) {
        $errors = [];
        
        if (empty($data['field_name'])) {
            $errors[] = 'O nome do campo é obrigatório.';
        }
        
        if (empty($data['field_label'])) {
            $errors[] = 'O rótulo do campo é obrigatório.';
        }
        
        if (empty($data['field_type'])) {
            $errors[] = 'O tipo do campo é obrigatório.';
        }

        // Validar o tipo do campo
        $validTypes = ['text', 'email', 'phone', 'date', 'select', 'radio', 'checkbox', 'textarea'];
        if (!in_array($data['field_type'], $validTypes)) {
            $errors[] = 'Tipo de campo inválido.';
        }

        // Validar opções para campos de seleção
        if (in_array($data['field_type'], ['select', 'radio', 'checkbox']) && empty($data['field_options'])) {
            $errors[] = 'Campos de seleção precisam ter opções definidas.';
        }
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        return [
            'success' => true,
            'data' => [
                'field_name' => $data['field_name'],
                'field_label' => $data['field_label'],
                'field_type' => $data['field_type'],
                'field_options' => isset($data['field_options']) ? $data['field_options'] : null,
                'placeholder' => $data['placeholder'] ?? null,
                'help_text' => $data['help_text'] ?? null,
                'is_required' => !empty($data['is_required']),
                'display_order' => $data['display_order'] ?? 0
            ]
        ];
    }
    
    private function validateSubmissionData($data, $fields) {
        $errors = [];
        $validatedData = [];
        
        foreach ($fields as $field) {
            $fieldName = $field['field_name'];
            $value = $data[$fieldName] ?? null;
            
            // Verificar campos obrigatórios
            if ($field['is_required'] && empty($value)) {
                $errors[] = "O campo '{$field['field_label']}' é obrigatório.";
                continue;
            }
            
            // Validar e-mail
            if ($field['field_type'] === 'email' && !empty($value)) {
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "O campo '{$field['field_label']}' deve ser um e-mail válido.";
                    continue;
                }
            }
            
            // Validar telefone
            if ($field['field_type'] === 'phone' && !empty($value)) {
                $phone = preg_replace('/[^0-9]/', '', $value);
                if (strlen($phone) < 10 || strlen($phone) > 11) {
                    $errors[] = "O campo '{$field['field_label']}' deve ser um telefone válido.";
                    continue;
                }
            }
            
            // Validar data
            if ($field['field_type'] === 'date' && !empty($value)) {
                $date = date_create_from_format('Y-m-d', $value);
                if (!$date) {
                    $errors[] = "O campo '{$field['field_label']}' deve ser uma data válida.";
                    continue;
                }
            }
            
            // Validar campos de seleção
            if (in_array($field['field_type'], ['select', 'radio']) && !empty($value)) {
                $options = explode("\n", $field['field_options']);
                $options = array_map('trim', $options);
                if (!in_array($value, $options)) {
                    $errors[] = "O valor selecionado para o campo '{$field['field_label']}' é inválido.";
                    continue;
                }
            }
            
            // Validar checkbox
            if ($field['field_type'] === 'checkbox' && !empty($value)) {
                $options = explode("\n", $field['field_options']);
                $options = array_map('trim', $options);
                $values = is_array($value) ? $value : [$value];
                foreach ($values as $v) {
                    if (!in_array($v, $options)) {
                        $errors[] = "Um dos valores selecionados para o campo '{$field['field_label']}' é inválido.";
                        continue 2;
                    }
                }
                $value = implode(', ', $values);
            }
            
            $validatedData[$fieldName] = $value;
        }
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        return ['success' => true, 'data' => $validatedData];
    }
    
    public function createSubmission($formId, $data)
    {
        return $this->submissionModel->create([
            'form_id' => $formId,
            'data' => json_encode($data),
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function findSubmission($id)
    {
        return $this->submissionModel->find($id);
    }

    public function updateSubmission($id, $data)
    {
        return $this->submissionModel->update($id, [
            'data' => json_encode($data),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
}
