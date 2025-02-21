<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Models\Report;

class ReportController extends Controller {
    private $reportModel;
    
    public function __construct() {
        parent::__construct();
        error_log("ReportController::__construct - Iniciando...");
        try {
            $this->reportModel = new Report();
            error_log("ReportController::__construct - Report model criado com sucesso");
        } catch (\Exception $e) {
            error_log("ReportController::__construct - Erro ao criar Report model: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
        }
    }
    
    public function index() {
        error_log("ReportController::index - Iniciando...");
        $this->checkAuth();
        
        try {
            error_log("ReportController::index - Buscando relatórios...");
            $reports = $this->reportModel->findAll();
            error_log("ReportController::index - Relatórios encontrados: " . json_encode($reports));
            View::render('reports/index', ['reports' => $reports]);
        } catch (\Exception $e) {
            error_log("ReportController::index - Erro: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $this->setFlash('error', 'Erro ao carregar relatórios: ' . $e->getMessage());
            redirect('/gcmanager/dashboard');
        }
    }
    
    public function create() {
        error_log("ReportController::create - Iniciando...");
        $this->checkAuth();
        
        try {
            error_log("ReportController::create - Renderizando formulário de criação...");
            View::render('reports/create');
        } catch (\Exception $e) {
            error_log("ReportController::create - Erro: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $this->setFlash('error', 'Erro ao criar relatório: ' . $e->getMessage());
            redirect('/gcmanager/reports');
        }
    }
    
    public function store() {
        error_log("ReportController::store - Iniciando...");
        $this->checkAuth();
        
        try {
            error_log("ReportController::store - Validando dados...");
            if (empty($_POST['name'])) {
                throw new \Exception('O nome do relatório é obrigatório.');
            }
            
            if (empty($_POST['type'])) {
                throw new \Exception('O tipo do relatório é obrigatório.');
            }
            
            if (empty($_POST['fields'])) {
                throw new \Exception('Selecione pelo menos um campo para o relatório.');
            }
            
            $data = [
                'name' => $_POST['name'],
                'description' => $_POST['description'] ?? null,
                'type' => $_POST['type'],
                'fields' => $_POST['fields'],
                'filters' => $_POST['filters'] ?? [],
                'created_by' => $_SESSION['user_id']
            ];
            
            error_log("ReportController::store - Dados validados. Criando relatório...");
            $id = $this->reportModel->create($data);
            
            if ($id) {
                error_log("ReportController::store - Relatório criado com sucesso!");
                $this->setFlash('success', 'Relatório criado com sucesso!');
                redirect('/gcmanager/reports');
            } else {
                throw new \Exception('Erro ao criar relatório.');
            }
        } catch (\Exception $e) {
            error_log("ReportController::store - Erro: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $this->setFlash('error', 'Erro ao criar relatório: ' . $e->getMessage());
            View::render('reports/create', ['data' => $_POST]);
        }
    }
    
    public function edit($id) {
        error_log("ReportController::edit - Iniciando...");
        $this->checkAuth();
        
        try {
            error_log("ReportController::edit - Buscando relatório...");
            $report = $this->reportModel->find($id);
            
            if (!$report) {
                throw new \Exception('Relatório não encontrado.');
            }
            
            error_log("ReportController::edit - Relatório encontrado. Renderizando formulário de edição...");
            View::render('reports/edit', ['report' => $report]);
        } catch (\Exception $e) {
            error_log("ReportController::edit - Erro: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $this->setFlash('error', 'Erro ao editar relatório: ' . $e->getMessage());
            redirect('/gcmanager/reports');
        }
    }
    
    public function update($id) {
        error_log("ReportController::update - Iniciando...");
        $this->checkAuth();
        
        try {
            error_log("ReportController::update - Validando dados...");
            $report = $this->reportModel->find($id);
            
            if (!$report) {
                throw new \Exception('Relatório não encontrado.');
            }
            
            if (empty($_POST['name'])) {
                throw new \Exception('O nome do relatório é obrigatório.');
            }
            
            if (empty($_POST['type'])) {
                throw new \Exception('O tipo do relatório é obrigatório.');
            }
            
            if (empty($_POST['fields'])) {
                throw new \Exception('Selecione pelo menos um campo para o relatório.');
            }
            
            $data = [
                'name' => $_POST['name'],
                'description' => $_POST['description'] ?? null,
                'type' => $_POST['type'],
                'fields' => $_POST['fields'],
                'filters' => $_POST['filters'] ?? []
            ];
            
            error_log("ReportController::update - Dados validados. Atualizando relatório...");
            if ($this->reportModel->update($id, $data)) {
                error_log("ReportController::update - Relatório atualizado com sucesso!");
                $this->setFlash('success', 'Relatório atualizado com sucesso!');
                redirect('/gcmanager/reports');
            } else {
                throw new \Exception('Erro ao atualizar relatório.');
            }
        } catch (\Exception $e) {
            error_log("ReportController::update - Erro: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $this->setFlash('error', 'Erro ao atualizar relatório: ' . $e->getMessage());
            View::render('reports/edit', [
                'report' => array_merge($report, $_POST)
            ]);
        }
    }
    
    public function delete($id) {
        error_log("ReportController::delete - Iniciando...");
        $this->checkAuth();
        
        try {
            error_log("ReportController::delete - Deletando relatório...");
            if ($this->reportModel->delete($id)) {
                error_log("ReportController::delete - Relatório deletado com sucesso!");
                $this->setFlash('success', 'Relatório excluído com sucesso!');
            } else {
                throw new \Exception('Erro ao deletar relatório.');
            }
        } catch (\Exception $e) {
            error_log("ReportController::delete - Erro: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $this->setFlash('error', 'Erro ao deletar relatório: ' . $e->getMessage());
        }
        
        redirect('/gcmanager/reports');
    }
    
    public function show($id) {
        error_log("ReportController::show - Iniciando...");
        $this->checkAuth();
        
        try {
            error_log("ReportController::show - Buscando relatório...");
            $report = $this->reportModel->find($id);
            
            if (!$report) {
                throw new \Exception('Relatório não encontrado.');
            }
            
            $filters = $_GET;
            unset($filters['page']);
            
            error_log("ReportController::show - Gerando relatório...");
            $result = $this->reportModel->generateReport($id, $filters);
            
            error_log("ReportController::show - Relatório gerado. Renderizando página de exibição...");
            View::render('reports/show', [
                'report' => $result['report'],
                'data' => $result['data']
            ]);
        } catch (\Exception $e) {
            error_log("ReportController::show - Erro: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $this->setFlash('error', 'Erro ao exibir relatório: ' . $e->getMessage());
            redirect('/gcmanager/reports');
        }
    }
    
    public function export($id) {
        error_log("ReportController::export - Iniciando...");
        $this->checkAuth();
        
        try {
            error_log("ReportController::export - Buscando relatório...");
            $report = $this->reportModel->find($id);
            
            if (!$report) {
                throw new \Exception('Relatório não encontrado.');
            }
            
            $filters = $_GET;
            unset($filters['page']);
            
            error_log("ReportController::export - Gerando relatório...");
            $result = $this->reportModel->generateReport($id, $filters);
            
            // Define o tipo de arquivo
            $format = $_GET['format'] ?? 'csv';
            
            error_log("ReportController::export - Exportando relatório para " . $format . "...");
            switch ($format) {
                case 'csv':
                    $this->exportToCsv($result);
                    break;
                case 'pdf':
                    $this->exportToPdf($result);
                    break;
                case 'excel':
                    $this->exportToExcel($result);
                    break;
                default:
                    throw new \Exception('Formato de exportação inválido.');
            }
        } catch (\Exception $e) {
            error_log("ReportController::export - Erro: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $this->setFlash('error', 'Erro ao exportar relatório: ' . $e->getMessage());
            redirect("/gcmanager/reports/{$id}");
        }
    }
    
    private function exportToCsv($result) {
        error_log("ReportController::exportToCsv - Iniciando...");
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $result['report']['name'] . '.csv');
        
        $output = fopen('php://output', 'w');
        
        // Cabeçalho
        $headers = [];
        foreach ($result['report']['fields'] as $field) {
            $headers[] = $field['alias'] ?? $field['field'];
        }
        fputcsv($output, $headers);
        
        // Dados
        foreach ($result['data'] as $row) {
            fputcsv($output, $row);
        }
        
        fclose($output);
        exit;
    }
    
    private function exportToPdf($result) {
        error_log("ReportController::exportToPdf - Iniciando...");
        // TODO: Implementar exportação para PDF
        $this->setFlash('error', 'Exportação para PDF ainda não implementada.');
        redirect("/gcmanager/reports/{$result['report']['id']}");
    }
    
    private function exportToExcel($result) {
        error_log("ReportController::exportToExcel - Iniciando...");
        // TODO: Implementar exportação para Excel
        $this->setFlash('error', 'Exportação para Excel ainda não implementada.');
        redirect("/gcmanager/reports/{$result['report']['id']}");
    }
    
    public function members() {
        error_log("ReportController::members - Iniciando...");
        $this->checkAuth();
        
        try {
            error_log("ReportController::members - Gerando relatório de membros...");
            $data = $this->reportModel->getMembersReport();
            View::render('reports/members', [
                'title' => 'Relatório de Membros',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            error_log("ReportController::members - Erro: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $this->setFlash('error', 'Erro ao gerar relatório de membros: ' . $e->getMessage());
            redirect('/gcmanager/reports');
        }
    }

    public function groups() {
        error_log("ReportController::groups - Iniciando...");
        $this->checkAuth();
        
        try {
            error_log("ReportController::groups - Gerando relatório de grupos...");
            $data = $this->reportModel->getGroupsReport();
            View::render('reports/groups', [
                'title' => 'Relatório de Grupos',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            error_log("ReportController::groups - Erro: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $this->setFlash('error', 'Erro ao gerar relatório de grupos: ' . $e->getMessage());
            redirect('/gcmanager/reports');
        }
    }

    public function visitors() {
        error_log("ReportController::visitors - Iniciando...");
        $this->checkAuth();
        
        try {
            error_log("ReportController::visitors - Gerando relatório de visitantes...");
            $data = $this->reportModel->getVisitorsReport();
            View::render('reports/visitors', [
                'title' => 'Relatório de Visitantes',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            error_log("ReportController::visitors - Erro: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $this->setFlash('error', 'Erro ao gerar relatório de visitantes: ' . $e->getMessage());
            redirect('/gcmanager/reports');
        }
    }

    public function ministries() {
        error_log("ReportController::ministries - Iniciando...");
        $this->checkAuth();
        
        try {
            error_log("ReportController::ministries - Gerando relatório de ministérios...");
            $data = $this->reportModel->getMinistriesReport();
            View::render('reports/ministries', [
                'title' => 'Relatório de Ministérios',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            error_log("ReportController::ministries - Erro: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $this->setFlash('error', 'Erro ao gerar relatório de ministérios: ' . $e->getMessage());
            redirect('/gcmanager/reports');
        }
    }
}
