<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Volunteer;
use App\Models\User;

class VolunteerController extends Controller {
    private Volunteer $volunteerModel;
    private User $userModel;
    
    public function __construct() {
        $this->volunteerModel = new Volunteer();
        $this->userModel = new User();
    }
    
    public function index(): void {
        $this->requireAuth();
        
        $ministry = $this->getQueryParams()['ministry'] ?? null;
        $volunteers = $ministry 
            ? $this->volunteerModel->getByMinistry($ministry)
            : $this->volunteerModel->findAll();
            
        $upcomingSchedules = $this->volunteerModel->getUpcomingSchedules();
        
        $this->render('volunteers/index', [
            'volunteers' => $volunteers,
            'upcomingSchedules' => $upcomingSchedules
        ]);
    }
    
    public function create(): void {
        $this->requireAuth();
        
        if ($this->isPost()) {
            $data = $this->getPostData();
            $errors = $this->validateVolunteerData($data);
            
            if (empty($errors)) {
                try {
                    $userId = $this->userModel->create([
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'password' => $data['password'],
                        'role' => 'volunteer'
                    ]);
                    
                    $this->volunteerModel->create([
                        'user_id' => $userId,
                        'ministry' => $data['ministry'],
                        'availability' => json_encode($data['availability'])
                    ]);
                    
                    $this->setFlash('success', 'Voluntário cadastrado com sucesso!');
                    $this->redirect('/volunteers');
                    return;
                } catch (\Exception $e) {
                    $errors[] = 'Erro ao cadastrar voluntário: ' . $e->getMessage();
                }
            }
            
            $this->setFlash('errors', $errors);
        }
        
        $this->render('volunteers/create');
    }
    
    public function edit(int $id): void {
        $this->requireAuth();
        
        $volunteer = $this->volunteerModel->find($id);
        if (!$volunteer) {
            $this->setFlash('error', 'Voluntário não encontrado');
            $this->redirect('/volunteers');
            return;
        }
        
        if ($this->isPost()) {
            $data = $this->getPostData();
            $errors = $this->validateVolunteerData($data, true);
            
            if (empty($errors)) {
                try {
                    $this->userModel->update($volunteer['user_id'], [
                        'name' => $data['name'],
                        'email' => $data['email']
                    ]);
                    
                    if (!empty($data['password'])) {
                        $this->userModel->updatePassword($volunteer['user_id'], $data['password']);
                    }
                    
                    $this->volunteerModel->update($id, [
                        'ministry' => $data['ministry'],
                        'availability' => json_encode($data['availability'])
                    ]);
                    
                    $this->setFlash('success', 'Voluntário atualizado com sucesso!');
                    $this->redirect('/volunteers');
                    return;
                } catch (\Exception $e) {
                    $errors[] = 'Erro ao atualizar voluntário: ' . $e->getMessage();
                }
            }
            
            $this->setFlash('errors', $errors);
        }
        
        $user = $this->userModel->find($volunteer['user_id']);
        $volunteer['name'] = $user['name'];
        $volunteer['email'] = $user['email'];
        
        $this->render('volunteers/edit', [
            'volunteer' => $volunteer
        ]);
    }
    
    public function schedule(): void {
        $this->requireAuth();
        
        if ($this->isPost()) {
            $data = $this->getPostData();
            $errors = $this->validateScheduleData($data);
            
            if (empty($errors)) {
                try {
                    $this->volunteerModel->createSchedule([
                        'volunteer_id' => $data['volunteer_id'],
                        'event_date' => $data['date'],
                        'event_time' => $data['time'],
                        'activity' => $data['activity'],
                        'status' => 'scheduled'
                    ]);
                    
                    $this->setFlash('success', 'Escala criada com sucesso!');
                    $this->redirect('/volunteers/schedules');
                    return;
                } catch (\Exception $e) {
                    $errors[] = 'Erro ao criar escala: ' . $e->getMessage();
                }
            }
            
            $this->setFlash('errors', $errors);
        }
        
        $volunteers = $this->volunteerModel->findAll();
        $this->render('volunteers/schedule', [
            'volunteers' => $volunteers
        ]);
    }
    
    public function updateSchedule(int $id): void {
        $this->requireAuth();
        
        if ($this->isPost()) {
            $data = $this->getPostData();
            
            try {
                $this->volunteerModel->updateScheduleStatus($id, $data['status']);
                $this->json(['success' => true]);
            } catch (\Exception $e) {
                $this->json(['error' => $e->getMessage()], 500);
            }
        }
    }
    
    private function validateVolunteerData(array $data, bool $isEdit = false): array {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors[] = 'O nome é obrigatório';
        }
        
        if (empty($data['email'])) {
            $errors[] = 'O e-mail é obrigatório';
        } elseif (!$this->validateEmail($data['email'])) {
            $errors[] = 'E-mail inválido';
        }
        
        if (!$isEdit && empty($data['password'])) {
            $errors[] = 'A senha é obrigatória';
        }
        
        if (empty($data['ministry'])) {
            $errors[] = 'O ministério é obrigatório';
        }
        
        if (empty($data['availability'])) {
            $errors[] = 'A disponibilidade é obrigatória';
        }
        
        return $errors;
    }
    
    private function validateScheduleData(array $data): array {
        $errors = [];
        
        if (empty($data['volunteer_id'])) {
            $errors[] = 'O voluntário é obrigatório';
        }
        
        if (empty($data['date'])) {
            $errors[] = 'A data é obrigatória';
        }
        
        if (empty($data['time'])) {
            $errors[] = 'O horário é obrigatório';
        }
        
        if (empty($data['activity'])) {
            $errors[] = 'A atividade é obrigatória';
        }
        
        return $errors;
    }
}
