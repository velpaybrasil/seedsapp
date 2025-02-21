<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\ExportService;
use App\Models\Message;
use App\Models\Notification;
use App\Models\FinancialTransaction;
use App\Models\User;

class ExportController extends Controller {
    private ExportService $exportService;
    private Message $messageModel;
    private Notification $notificationModel;
    private FinancialTransaction $transactionModel;
    private User $userModel;
    
    public function __construct() {
        $this->exportService = new ExportService();
        $this->messageModel = new Message();
        $this->notificationModel = new Notification();
        $this->transactionModel = new FinancialTransaction();
        $this->userModel = new User();
    }
    
    public function exportMessages(): void {
        $this->requireAuth();
        
        $format = $_GET['format'] ?? 'excel';
        $userId = $this->getCurrentUserId();
        $type = $_GET['type'] ?? 'all'; // all, sent, received
        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date'] ?? null;
        
        // Get messages
        $messages = $this->messageModel->getMessagesForExport($userId, $type, $startDate, $endDate);
        
        // Prepare data for export
        $data = [];
        foreach ($messages as $message) {
            $sender = $this->userModel->findById($message['sender_id']);
            $recipient = $this->userModel->findById($message['recipient_id']);
            
            $data[] = [
                'date' => new \DateTime($message['created_at']),
                'sender' => $sender['name'],
                'recipient' => $recipient['name'],
                'subject' => $message['subject'],
                'content' => strip_tags($message['content']),
                'read_at' => $message['read_at'] ? new \DateTime($message['read_at']) : 'Não lida'
            ];
        }
        
        // Define columns
        $columns = [
            'date' => 'Data',
            'sender' => 'Remetente',
            'recipient' => 'Destinatário',
            'subject' => 'Assunto',
            'content' => 'Conteúdo',
            'read_at' => 'Lida em'
        ];
        
        // Generate filename
        $filename = 'mensagens_' . date('Y-m-d_H-i-s');
        
        try {
            $exportPath = match($format) {
                'excel' => $this->exportService->exportToExcel($data, $columns, $filename),
                'pdf' => $this->exportService->exportToPdf($data, $columns, $filename, [
                    'title' => 'Relatório de Mensagens',
                    'orientation' => 'L'
                ]),
                'csv' => $this->exportService->exportToCsv($data, $columns, $filename),
                default => throw new \InvalidArgumentException('Formato inválido')
            };
            
            $this->json([
                'success' => true,
                'file' => $exportPath
            ]);
        } catch (\Exception $e) {
            $this->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function exportNotifications(): void {
        $this->requireAuth();
        
        $format = $_GET['format'] ?? 'excel';
        $userId = $this->getCurrentUserId();
        $type = $_GET['type'] ?? 'all'; // all, read, unread
        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date'] ?? null;
        
        // Get notifications
        $notifications = $this->notificationModel->getNotificationsForExport($userId, $type, $startDate, $endDate);
        
        // Prepare data for export
        $data = [];
        foreach ($notifications as $notification) {
            $data[] = [
                'date' => new \DateTime($notification['created_at']),
                'type' => $notification['type'],
                'message' => $notification['message'],
                'read_at' => $notification['read_at'] ? new \DateTime($notification['read_at']) : 'Não lida'
            ];
        }
        
        // Define columns
        $columns = [
            'date' => 'Data',
            'type' => 'Tipo',
            'message' => 'Mensagem',
            'read_at' => 'Lida em'
        ];
        
        // Generate filename
        $filename = 'notificacoes_' . date('Y-m-d_H-i-s');
        
        try {
            $exportPath = match($format) {
                'excel' => $this->exportService->exportToExcel($data, $columns, $filename),
                'pdf' => $this->exportService->exportToPdf($data, $columns, $filename, [
                    'title' => 'Relatório de Notificações'
                ]),
                'csv' => $this->exportService->exportToCsv($data, $columns, $filename),
                default => throw new \InvalidArgumentException('Formato inválido')
            };
            
            $this->json([
                'success' => true,
                'file' => $exportPath
            ]);
        } catch (\Exception $e) {
            $this->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function exportTransactions(): void {
        $this->requireAuth();
        
        $format = $_GET['format'] ?? 'excel';
        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date'] ?? null;
        $type = $_GET['type'] ?? 'all'; // all, income, expense
        $category = $_GET['category'] ?? null;
        
        // Get transactions
        $transactions = $this->transactionModel->getTransactionsForExport($startDate, $endDate, $type, $category);
        
        // Prepare data for export
        $data = [];
        foreach ($transactions as $transaction) {
            $creator = $this->userModel->findById($transaction['created_by']);
            
            $data[] = [
                'date' => new \DateTime($transaction['created_at']),
                'type' => $transaction['type'],
                'category' => $transaction['category'],
                'description' => $transaction['description'],
                'amount' => $transaction['amount'],
                'payment_method' => $transaction['payment_method'],
                'created_by' => $creator['name']
            ];
        }
        
        // Define columns
        $columns = [
            'date' => 'Data',
            'type' => 'Tipo',
            'category' => 'Categoria',
            'description' => 'Descrição',
            'amount' => 'Valor',
            'payment_method' => 'Forma de Pagamento',
            'created_by' => 'Criado por'
        ];
        
        // Generate filename
        $filename = 'transacoes_' . date('Y-m-d_H-i-s');
        
        try {
            $exportPath = match($format) {
                'excel' => $this->exportService->exportToExcel($data, $columns, $filename),
                'pdf' => $this->exportService->exportToPdf($data, $columns, $filename, [
                    'title' => 'Relatório de Transações Financeiras',
                    'orientation' => 'L'
                ]),
                'csv' => $this->exportService->exportToCsv($data, $columns, $filename),
                default => throw new \InvalidArgumentException('Formato inválido')
            };
            
            $this->json([
                'success' => true,
                'file' => $exportPath
            ]);
        } catch (\Exception $e) {
            $this->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function cleanup(): void {
        $this->requireAuth(['admin']);
        
        try {
            $this->exportService->cleanupExports();
            $this->json(['success' => true]);
        } catch (\Exception $e) {
            $this->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
