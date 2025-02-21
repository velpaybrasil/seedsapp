<?php

namespace App\Controllers;

use App\Models\PrayerRequest;
use App\Core\Controller;
use App\Core\View;

class PrayerRequestController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->checkAuth();
        
        try {
            // Inicializar a tabela se necessário
            error_log("[PrayerRequestController] Initializing prayer requests...");
            PrayerRequest::initialize();
            error_log("[PrayerRequestController] Initialization complete");
        } catch (\Exception $e) {
            error_log("[PrayerRequestController] Error initializing: " . $e->getMessage());
            error_log("[PrayerRequestController] Stack trace: " . $e->getTraceAsString());
        }
    }

    public function index()
    {
        try {
            error_log("[PrayerRequestController] Loading prayer requests index...");

            // Buscar pedidos por status
            $pendingRequests = PrayerRequest::getAllWithStatus('pending');
            $prayingRequests = PrayerRequest::getAllWithStatus('praying');
            $completedRequests = PrayerRequest::getAllWithStatus('completed');

            error_log("[PrayerRequestController] Found requests - Pending: " . count($pendingRequests) . 
                     ", Praying: " . count($prayingRequests) . 
                     ", Completed: " . count($completedRequests));

            // Renderizar a view
            View::render('prayer_requests/index', [
                'pageTitle' => 'Pedidos de Oração',
                'pendingRequests' => $pendingRequests,
                'prayingRequests' => $prayingRequests,
                'completedRequests' => $completedRequests
            ]);

        } catch (\Exception $e) {
            error_log("[PrayerRequestController] Error: " . $e->getMessage());
            error_log("[PrayerRequestController] Stack trace: " . $e->getTraceAsString());
            $this->redirect('/dashboard', ['error' => 'Erro ao carregar pedidos de oração']);
        }
    }

    public function create()
    {
        View::render('prayer_requests/create', [
            'pageTitle' => 'Novo Pedido de Oração'
        ]);
    }

    public function store()
    {
        try {
            error_log("[PrayerRequestController] Storing new prayer request...");
            error_log("[PrayerRequestController] POST data: " . print_r($_POST, true));

            if (empty($_POST['visitor_name']) || empty($_POST['request'])) {
                throw new \Exception('Todos os campos são obrigatórios');
            }

            $data = [
                'visitor_name' => $_POST['visitor_name'],
                'request' => $_POST['request'],
                'status' => 'pending'
            ];

            error_log("[PrayerRequestController] Creating prayer request with data: " . print_r($data, true));
            $result = PrayerRequest::create($data);
            error_log("[PrayerRequestController] Creation result: " . ($result ? 'success' : 'failure'));

            if ($result) {
                $this->redirect('/prayer-requests', ['success' => 'Pedido de oração registrado com sucesso']);
            } else {
                throw new \Exception('Erro ao criar pedido de oração');
            }

        } catch (\Exception $e) {
            error_log("[PrayerRequestController] Error creating prayer request: " . $e->getMessage());
            error_log("[PrayerRequestController] Stack trace: " . $e->getTraceAsString());
            $this->redirect('/prayer-requests', ['error' => $e->getMessage()]);
        }
    }

    public function updateStatus()
    {
        try {
            error_log("[PrayerRequestController] Updating prayer request status...");
            error_log("[PrayerRequestController] POST data: " . print_r($_POST, true));

            $id = $_POST['id'] ?? null;
            $status = $_POST['status'] ?? null;

            if (!$id || !$status) {
                throw new \Exception('ID e status são obrigatórios');
            }

            $result = PrayerRequest::updateStatus($id, $status);
            error_log("[PrayerRequestController] Update result: " . ($result ? 'success' : 'failure'));

            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                throw new \Exception('Erro ao atualizar status');
            }

        } catch (\Exception $e) {
            error_log("[PrayerRequestController] Error updating prayer request status: " . $e->getMessage());
            error_log("[PrayerRequestController] Stack trace: " . $e->getTraceAsString());
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
