<?php

namespace App\Controllers;

use App\Models\Message;
use App\Core\Controller;
use App\Core\View;

class MessageController extends Controller {
    private $messageModel;
    
    public function __construct() {
        parent::__construct();
        $this->messageModel = new Message();
    }
    
    public function index() {
        $this->checkAuth();
        
        $filters = [
            'type' => $_GET['type'] ?? null,
            'status' => $_GET['status'] ?? null,
            'search' => $_GET['search'] ?? null
        ];
        
        $data = [
            'messages' => $this->messageModel->findAll($filters),
            'filters' => $filters
        ];
        
        View::render('messages/index', $data);
    }
    
    public function create() {
        $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'subject' => $_POST['subject'],
                'content' => $_POST['content'],
                'type' => $_POST['type'],
                'status' => $_POST['status'] ?? 'draft',
                'scheduled_date' => $_POST['scheduled_date'] ?? null,
                'sender_id' => $_SESSION['user_id'],
                'recipients' => $_POST['recipients'] ?? []
            ];
            
            $id = $this->messageModel->create($data);
            
            if ($id) {
                $this->setFlash('success', 'Mensagem criada com sucesso!');
                redirect('/messages');
            } else {
                $this->setFlash('error', 'Erro ao criar mensagem.');
                View::render('messages/create', ['data' => $data]);
            }
        } else {
            View::render('messages/create');
        }
    }
    
    public function edit($id) {
        $this->checkAuth();
        
        $message = $this->messageModel->find($id);
        
        if (!$message) {
            $this->setFlash('error', 'Mensagem não encontrada.');
            redirect('/messages');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'subject' => $_POST['subject'],
                'content' => $_POST['content'],
                'type' => $_POST['type'],
                'status' => $_POST['status'],
                'scheduled_date' => $_POST['scheduled_date'] ?? null,
                'recipients' => $_POST['recipients'] ?? []
            ];
            
            if ($this->messageModel->update($id, $data)) {
                $this->setFlash('success', 'Mensagem atualizada com sucesso!');
                redirect('/messages');
            } else {
                $this->setFlash('error', 'Erro ao atualizar mensagem.');
                View::render('messages/edit', ['message' => $message]);
            }
        } else {
            View::render('messages/edit', ['message' => $message]);
        }
    }
    
    public function delete($id) {
        $this->checkAuth();
        
        if ($this->messageModel->delete($id)) {
            $this->setFlash('success', 'Mensagem excluída com sucesso!');
        } else {
            $this->setFlash('error', 'Erro ao excluir mensagem.');
        }
        
        redirect('/messages');
    }
    
    public function view($id) {
        $this->checkAuth();
        
        $message = $this->messageModel->find($id);
        
        if (!$message) {
            $this->setFlash('error', 'Mensagem não encontrada.');
            redirect('/messages');
        }
        
        $recipients = $this->messageModel->getRecipients($id);
        
        View::render('messages/view', [
            'message' => $message,
            'recipients' => $recipients
        ]);
    }
    
    public function markAsRead($id) {
        $this->checkAuth();
        
        if ($this->messageModel->markAsRead($id, $_SESSION['user_id'])) {
            $this->setFlash('success', 'Mensagem marcada como lida.');
        } else {
            $this->setFlash('error', 'Erro ao marcar mensagem como lida.');
        }
        
        redirect("/messages/view/{$id}");
    }
    
    public function getUnreadCount() {
        $this->checkAuth();
        
        $count = $this->messageModel->getUnreadCount($_SESSION['user_id']);
        echo json_encode(['count' => $count]);
    }
    
    public function processScheduled() {
        $this->checkAuth();
        
        $messages = $this->messageModel->getScheduledMessages();
        
        foreach ($messages as $message) {
            // Atualiza o status da mensagem para enviada
            $this->messageModel->update($message['id'], ['status' => 'sent']);
            
            // TODO: Implementar o envio efetivo da mensagem (email, SMS, etc)
        }
        
        echo json_encode(['processed' => count($messages)]);
    }
}
