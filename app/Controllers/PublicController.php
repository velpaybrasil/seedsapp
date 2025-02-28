<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Models\Visitor;

class PublicController extends Controller {
    private $visitorModel;
    
    public function __construct() {
        parent::__construct();
        $this->visitorModel = new Visitor();
    }
    
    public function visitorForm() {
        View::render('public/visitor-form');
    }
    
    public function storeVisitor() {
        try {
            // Validar campos obrigatórios
            if (empty($_POST['name'])) {
                $this->setFlash('error', 'O nome é obrigatório.');
                View::render('public/visitor-form', ['data' => $_POST]);
                return;
            }
            
            // Tratar campos do formulário
            $data = [
                'name' => $_POST['name'],
                'birth_date' => !empty($_POST['birth_date']) ? $_POST['birth_date'] : null,
                'marital_status' => !empty($_POST['marital_status']) ? $_POST['marital_status'] : null,
                'phone' => !empty($_POST['phone']) ? $_POST['phone'] : null,
                'whatsapp' => !empty($_POST['whatsapp']) ? $_POST['whatsapp'] : null,
                'email' => !empty($_POST['email']) ? $_POST['email'] : null,
                'address' => !empty($_POST['address']) ? $_POST['address'] : null,
                'number' => !empty($_POST['number']) ? $_POST['number'] : null,
                'complement' => !empty($_POST['complement']) ? $_POST['complement'] : null,
                'neighborhood' => !empty($_POST['neighborhood']) ? $_POST['neighborhood'] : null,
                'city' => !empty($_POST['city']) ? $_POST['city'] : null,
                'zipcode' => !empty($_POST['zipcode']) ? $_POST['zipcode'] : null,
                'gender' => !empty($_POST['gender']) ? $_POST['gender'] : null,
                'first_visit_date' => !empty($_POST['first_visit_date']) ? $_POST['first_visit_date'] : null,
                'how_knew_church' => !empty($_POST['how_knew_church']) ? $_POST['how_knew_church'] : null,
                'prayer_requests' => !empty($_POST['prayer_requests']) ? $_POST['prayer_requests'] : null,
                'observations' => !empty($_POST['observations']) ? $_POST['observations'] : null,
                'status' => 'not_contacted'
            ];
            
            $id = $this->visitorModel->create($data);
            
            if ($id) {
                $this->setFlash('success', 'Obrigado por se cadastrar! Em breve entraremos em contato.');
                redirect('/public/visitor-form/success');
            }
            
        } catch (\Exception $e) {
            error_log("Erro ao cadastrar visitante: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $this->setFlash('error', 'Erro ao cadastrar visitante. Por favor, tente novamente.');
            View::render('public/visitor-form', ['data' => $_POST]);
        }
    }
    
    public function success() {
        View::render('public/success');
    }
    
    public function testLog() {
        try {
            // Forçar um erro para testar o log
            $undefinedVariable = $nonExistentVariable;
        } catch (\Throwable $e) {
            error_log("Teste de log: " . $e->getMessage());
            echo "Erro gerado com sucesso. Verifique o arquivo de log.";
        }
    }
}
