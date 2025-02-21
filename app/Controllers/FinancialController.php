<?php

namespace App\Controllers;

use App\Models\Financial;
use App\Core\Controller;
use App\Core\View;

class FinancialController extends Controller {
    private $financialModel;
    
    public function __construct() {
        parent::__construct();
        $this->financialModel = new Financial();
    }
    
    public function index() {
        $this->checkAuth();
        
        $filters = [
            'type' => $_GET['type'] ?? null,
            'status' => $_GET['status'] ?? null,
            'start_date' => $_GET['start_date'] ?? date('Y-m-01'),
            'end_date' => $_GET['end_date'] ?? date('Y-m-t')
        ];
        
        $data = [
            'transactions' => $this->financialModel->getTransactions($filters),
            'filters' => $filters,
            'stats' => $this->financialModel->getFinancialStats('month')
        ];
        
        View::render('financial/index', $data);
    }
    
    public function categories() {
        $this->checkAuth();
        
        $filters = [
            'type' => $_GET['type'] ?? null,
            'active' => $_GET['active'] ?? true
        ];
        
        $data = [
            'categories' => $this->financialModel->getCategories($filters),
            'filters' => $filters
        ];
        
        View::render('financial/categories', $data);
    }
    
    public function createCategory() {
        $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'],
                'type' => $_POST['type'],
                'description' => $_POST['description'] ?? null,
                'active' => $_POST['active'] ?? true
            ];
            
            $id = $this->financialModel->createCategory($data);
            
            if ($id) {
                $this->setFlash('success', 'Categoria criada com sucesso!');
                redirect('/financial/categories');
            } else {
                $this->setFlash('error', 'Erro ao criar categoria.');
                View::render('financial/create-category', ['data' => $data]);
            }
        } else {
            View::render('financial/create-category');
        }
    }
    
    public function createTransaction() {
        $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'category_id' => $_POST['category_id'],
                'type' => $_POST['type'],
                'amount' => $_POST['amount'],
                'description' => $_POST['description'] ?? null,
                'date' => $_POST['date'],
                'payment_method' => $_POST['payment_method'],
                'status' => $_POST['status'] ?? 'pending',
                'document_number' => $_POST['document_number'] ?? null,
                'user_id' => $_SESSION['user_id']
            ];
            
            $id = $this->financialModel->createTransaction($data);
            
            if ($id) {
                $this->setFlash('success', 'Transação registrada com sucesso!');
                redirect('/financial');
            } else {
                $this->setFlash('error', 'Erro ao registrar transação.');
                View::render('financial/create-transaction', ['data' => $data]);
            }
        } else {
            $categories = $this->financialModel->getCategories(['active' => true]);
            View::render('financial/create-transaction', ['categories' => $categories]);
        }
    }
    
    public function updateTransaction($id) {
        $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'category_id' => $_POST['category_id'],
                'type' => $_POST['type'],
                'amount' => $_POST['amount'],
                'description' => $_POST['description'] ?? null,
                'date' => $_POST['date'],
                'payment_method' => $_POST['payment_method'],
                'status' => $_POST['status'],
                'document_number' => $_POST['document_number'] ?? null
            ];
            
            if ($this->financialModel->updateTransaction($id, $data)) {
                $this->setFlash('success', 'Transação atualizada com sucesso!');
                redirect('/financial');
            } else {
                $this->setFlash('error', 'Erro ao atualizar transação.');
                redirect("/financial/transaction/{$id}");
            }
        }
    }
    
    public function tithesOfferings() {
        $this->checkAuth();
        
        $filters = [
            'type' => $_GET['type'] ?? null,
            'start_date' => $_GET['start_date'] ?? date('Y-m-01'),
            'end_date' => $_GET['end_date'] ?? date('Y-m-t'),
            'user_id' => $_GET['user_id'] ?? null
        ];
        
        $data = [
            'tithesOfferings' => $this->financialModel->getTithesOfferings($filters),
            'filters' => $filters,
            'stats' => $this->financialModel->getTithesOfferingsStats('month')
        ];
        
        View::render('financial/tithes-offerings', $data);
    }
    
    public function createTitheOffering() {
        $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'user_id' => $_POST['anonymous'] ? null : $_POST['user_id'],
                'type' => $_POST['type'],
                'amount' => $_POST['amount'],
                'date' => $_POST['date'],
                'payment_method' => $_POST['payment_method'],
                'anonymous' => $_POST['anonymous'] ?? false,
                'notes' => $_POST['notes'] ?? null
            ];
            
            $id = $this->financialModel->createTitheOffering($data);
            
            if ($id) {
                $this->setFlash('success', 'Dízimo/Oferta registrado com sucesso!');
                redirect('/financial/tithes-offerings');
            } else {
                $this->setFlash('error', 'Erro ao registrar dízimo/oferta.');
                View::render('financial/create-tithe-offering', ['data' => $data]);
            }
        } else {
            View::render('financial/create-tithe-offering');
        }
    }
    
    public function reports() {
        $this->checkAuth();
        
        $period = $_GET['period'] ?? 'month';
        
        $data = [
            'financialStats' => $this->financialModel->getFinancialStats($period),
            'tithesOfferingsStats' => $this->financialModel->getTithesOfferingsStats($period),
            'period' => $period
        ];
        
        View::render('financial/reports', $data);
    }
    
    public function exportReport() {
        $this->checkAuth();
        
        $period = $_POST['period'] ?? 'month';
        $type = $_POST['type'] ?? 'all';
        
        // TODO: Implementar exportação de relatórios
        $this->setFlash('error', 'Funcionalidade em desenvolvimento.');
        redirect('/financial/reports');
    }
}
