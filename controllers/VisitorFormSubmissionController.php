<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\VisitorForm;
use App\Models\VisitorFormField;
use App\Models\VisitorFormSubmission;
use App\Models\Visitor;

class VisitorFormSubmissionController extends Controller
{
    public function submit($slug)
    {
        try {
            $form = VisitorForm::findBySlug($slug);
            if (!$form || !$form['active']) {
                throw new \Exception('Formulário não encontrado ou inativo');
            }

            $fields = VisitorFormField::getByFormId($form['id']);
            if (empty($fields)) {
                throw new \Exception('O formulário não possui campos definidos');
            }

            // Validar campos obrigatórios
            $submissionData = [];
            foreach ($fields as $field) {
                $fieldName = $field['field_name'];
                
                if ($field['is_required'] && (!isset($_POST[$fieldName]) || trim($_POST[$fieldName]) === '')) {
                    throw new \Exception("O campo {$field['field_label']} é obrigatório");
                }
                
                $submissionData[$fieldName] = isset($_POST[$fieldName]) ? trim($_POST[$fieldName]) : '';
            }

            // Criar ou atualizar visitante
            $visitorData = [
                'name' => $_POST['name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'created_at' => date('Y-m-d H:i:s')
            ];

            $visitor = Visitor::findByEmail($visitorData['email']);
            $visitorId = $visitor ? $visitor['id'] : Visitor::create($visitorData);

            if (!$visitorId) {
                throw new \Exception('Erro ao salvar dados do visitante');
            }

            // Criar submissão
            $submission = [
                'form_id' => $form['id'],
                'visitor_id' => $visitorId,
                'data' => json_encode($submissionData),
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'created_at' => date('Y-m-d H:i:s')
            ];

            $submissionId = VisitorFormSubmission::create($submission);
            if (!$submissionId) {
                throw new \Exception('Erro ao salvar submissão do formulário');
            }

            // Redirecionar para página de sucesso
            return $this->redirect("/visitor-forms/{$slug}/success");

        } catch (\Exception $e) {
            $this->flash->error($e->getMessage());
            return $this->redirect("/visitor-forms/{$slug}");
        }
    }

    public function success($slug)
    {
        $form = VisitorForm::findBySlug($slug);
        if (!$form) {
            $this->flash->error('Formulário não encontrado');
            return $this->redirect('/');
        }

        return $this->view('visitor_forms/success', ['form' => $form]);
    }

    public function delete($formId, $submissionId)
    {
        $this->checkPermission('visitor_forms', 'delete');

        try {
            $submission = (object)VisitorFormSubmission::find($submissionId);
            if (!$submission || $submission->form_id != $formId) {
                throw new \Exception('Submissão não encontrada');
            }

            if (!VisitorFormSubmission::delete($submissionId)) {
                throw new \Exception('Erro ao excluir submissão');
            }

            $this->flash->success('Submissão excluída com sucesso!');
            return $this->redirect("/visitor-forms/{$formId}/submissions");

        } catch (\Exception $e) {
            $this->flash->error($e->getMessage());
            return $this->redirect("/visitor-forms/{$formId}/submissions");
        }
    }
}
