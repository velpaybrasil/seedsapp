<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Message;
use App\Models\Notification;
use App\Models\User;

class CommunicationController extends Controller {
    private Message $messageModel;
    private Notification $notificationModel;
    private User $userModel;
    
    public function __construct() {
        $this->messageModel = new Message();
        $this->notificationModel = new Notification();
        $this->userModel = new User();
    }
    
    public function inbox(): void {
        $this->requireAuth();
        
        $page = (int)($_GET['page'] ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $userId = $this->getCurrentUserId();
        $messages = $this->messageModel->getInbox($userId, $limit, $offset);
        $stats = $this->messageModel->getMessageStats($userId);
        
        $this->render('communication/inbox', [
            'messages' => $messages,
            'stats' => $stats,
            'page' => $page,
            'limit' => $limit
        ]);
    }
    
    public function sent(): void {
        $this->requireAuth();
        
        $page = (int)($_GET['page'] ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $userId = $this->getCurrentUserId();
        $messages = $this->messageModel->getSent($userId, $limit, $offset);
        
        $this->render('communication/sent', [
            'messages' => $messages,
            'page' => $page,
            'limit' => $limit
        ]);
    }
    
    public function compose(): void {
        $this->requireAuth();
        
        if ($this->isPost()) {
            $data = $this->getPostData();
            $errors = $this->validateMessageData($data);
            
            if (empty($errors)) {
                try {
                    $data['sender_id'] = $this->getCurrentUserId();
                    
                    // Handle multiple recipients
                    $recipients = array_map('trim', explode(',', $data['recipients']));
                    foreach ($recipients as $recipient) {
                        $recipientUser = $this->userModel->findByEmail($recipient);
                        if ($recipientUser) {
                            $messageData = array_merge($data, ['recipient_id' => $recipientUser['id']]);
                            $messageId = $this->messageModel->create($messageData);
                            
                            // Create notification for recipient
                            $this->notificationModel->createSystemNotification(
                                [$recipientUser['id']],
                                'new_message',
                                "Nova mensagem de {$this->getCurrentUser()['name']}",
                                ['message_id' => $messageId]
                            );
                        }
                    }
                    
                    $this->setFlash('success', 'Mensagem enviada com sucesso!');
                    $this->redirect('/communication/inbox');
                    return;
                } catch (\Exception $e) {
                    $errors[] = 'Erro ao enviar mensagem: ' . $e->getMessage();
                }
            }
            
            $this->setFlash('errors', $errors);
        }
        
        // Get users for recipient autocomplete
        $users = $this->userModel->findAll(['select' => ['id', 'name', 'email']]);
        
        $this->render('communication/compose', [
            'users' => $users,
            'reply_to' => $_GET['reply_to'] ?? null
        ]);
    }
    
    public function view(int $id): void {
        $this->requireAuth();
        
        $userId = $this->getCurrentUserId();
        $thread = $this->messageModel->getThread($id, $userId);
        
        if (empty($thread)) {
            $this->setFlash('error', 'Mensagem não encontrada');
            $this->redirect('/communication/inbox');
            return;
        }
        
        // Mark message as read if recipient
        if ($thread[0]['recipient_id'] === $userId) {
            $this->messageModel->markAsRead($id, $userId);
        }
        
        $this->render('communication/view', [
            'thread' => $thread
        ]);
    }
    
    public function search(): void {
        $this->requireAuth();
        
        $query = $_GET['q'] ?? '';
        if (empty($query)) {
            $this->redirect('/communication/inbox');
            return;
        }
        
        $userId = $this->getCurrentUserId();
        $messages = $this->messageModel->searchMessages($userId, $query);
        
        $this->render('communication/search', [
            'messages' => $messages,
            'query' => $query
        ]);
    }
    
    public function notifications(): void {
        $this->requireAuth();
        
        $userId = $this->getCurrentUserId();
        $notifications = $this->notificationModel->getUnread($userId);
        $preferences = $this->notificationModel->getNotificationPreferences($userId);
        
        $this->render('communication/notifications', [
            'notifications' => $notifications,
            'preferences' => $preferences
        ]);
    }
    
    public function updatePreferences(): void {
        $this->requireAuth();
        
        if ($this->isPost()) {
            $preferences = $this->getPostData();
            $userId = $this->getCurrentUserId();
            
            try {
                $this->notificationModel->updateNotificationPreferences($userId, $preferences);
                $this->json(['success' => true]);
            } catch (\Exception $e) {
                $this->json(['error' => $e->getMessage()], 500);
            }
        }
    }
    
    public function markNotificationRead(int $id): void {
        $this->requireAuth();
        
        if ($this->isPost()) {
            $userId = $this->getCurrentUserId();
            
            try {
                $this->notificationModel->markAsRead($id, $userId);
                $this->json(['success' => true]);
            } catch (\Exception $e) {
                $this->json(['error' => $e->getMessage()], 500);
            }
        }
    }
    
    public function markAllNotificationsRead(): void {
        $this->requireAuth();
        
        if ($this->isPost()) {
            $userId = $this->getCurrentUserId();
            
            try {
                $this->notificationModel->markAllAsRead($userId);
                $this->json(['success' => true]);
            } catch (\Exception $e) {
                $this->json(['error' => $e->getMessage()], 500);
            }
        }
    }
    
    private function validateMessageData(array $data): array {
        $errors = [];
        
        if (empty($data['recipients'])) {
            $errors[] = 'O destinatário é obrigatório';
        }
        
        if (empty($data['subject'])) {
            $errors[] = 'O assunto é obrigatório';
        }
        
        if (empty($data['content'])) {
            $errors[] = 'O conteúdo da mensagem é obrigatório';
        }
        
        // Validate recipients
        $recipients = array_map('trim', explode(',', $data['recipients']));
        foreach ($recipients as $recipient) {
            if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "E-mail inválido: {$recipient}";
            } elseif (!$this->userModel->findByEmail($recipient)) {
                $errors[] = "Usuário não encontrado: {$recipient}";
            }
        }
        
        return $errors;
    }
}
