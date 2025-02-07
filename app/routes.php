<?php

use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\UserController;
use App\Controllers\GroupController;
use App\Controllers\VisitorController;
use App\Controllers\ProfileController;
use App\Controllers\MinistryController;
use App\Controllers\ReportController;
use App\Controllers\PublicController;
use App\Controllers\PrayerRequestController;
use App\Controllers\SystemModuleController;

// Debug
error_log('Loading routes...');

// Rotas públicas
Router::get('/', [AuthController::class, 'loginForm']);
Router::get('/login', [AuthController::class, 'loginForm']);
Router::post('/login', [AuthController::class, 'login']);
Router::get('/logout', [AuthController::class, 'logout']);

// Formulário público de visitantes (sem autenticação)
Router::get('/public/visitor-form', [PublicController::class, 'visitorForm']);
Router::post('/public/visitor-form/store', [PublicController::class, 'storeVisitor']);
Router::get('/public/visitor-form/success', [PublicController::class, 'success']);
Router::get('/visitor-form', [PublicController::class, 'visitorForm']); // Rota alternativa
Router::post('/visitor-form/store', [PublicController::class, 'storeVisitor']); // Rota alternativa
Router::get('/visitor-form/success', [PublicController::class, 'success']); // Rota alternativa

// Rotas protegidas
Router::group(['middleware' => 'auth'], function() {
    error_log('Setting up protected routes...');

    // Dashboard
    Router::get('/dashboard', [DashboardController::class, 'index']);

    // Usuários
    Router::get('/users', [UserController::class, 'index']);
    Router::get('/users/create', [UserController::class, 'create']);
    Router::post('/users', [UserController::class, 'store']);
    Router::get('/users/{id}/edit', [UserController::class, 'edit']);
    Router::post('/users/{id}', [UserController::class, 'update']);
    Router::post('/users/{id}/delete', [UserController::class, 'delete']);

    // Papéis de Usuário
    Router::get('/users/roles', [UserController::class, 'roles']);
    Router::get('/users/roles/create', [UserController::class, 'createRole']);
    Router::post('/users/roles', [UserController::class, 'storeRole']);
    Router::get('/users/roles/{id}/edit', [UserController::class, 'editRole']);
    Router::post('/users/roles/{id}', [UserController::class, 'updateRole']);
    Router::post('/users/roles/{id}/delete', [UserController::class, 'deleteRole']);

    // Grupos
    Router::get('/groups', [GroupController::class, 'index']);
    Router::get('/groups/create', [GroupController::class, 'create']);
    Router::post('/groups', [GroupController::class, 'store']);
    Router::get('/groups/{id}/edit', [GroupController::class, 'edit']);
    Router::post('/groups/{id}', [GroupController::class, 'update']);
    Router::post('/groups/{id}/delete', [GroupController::class, 'delete']);
    Router::get('/groups/{id}', [GroupController::class, 'show']);
    Router::get('/groups/heatmap', [GroupController::class, 'heatmap']);

    // Visitantes
    Router::get('/visitors', [VisitorController::class, 'index']);
    Router::get('/visitors/create', [VisitorController::class, 'create']);
    Router::post('/visitors', [VisitorController::class, 'store']);
    Router::get('/visitors/{id}', [VisitorController::class, 'show']); 
    Router::get('/visitors/{id}/edit', [VisitorController::class, 'edit']);
    Router::post('/visitors/{id}', [VisitorController::class, 'update']);
    Router::post('/visitors/{id}/delete', [VisitorController::class, 'delete']);
    Router::get('/visitors/{id}/contact-logs', [VisitorController::class, 'getContactLogs']);
    Router::post('/visitors/{id}/contact-logs', [VisitorController::class, 'addContactLog']);
    Router::post('/visitors/{id}/contact-logs/{logId}/status', [VisitorController::class, 'updateFollowUpStatus']);

    // Rotas para gerenciamento de participantes do grupo
    Router::get('/visitors/search', [VisitorController::class, 'search']);
    Router::post('/visitors/add-to-group', [VisitorController::class, 'addToGroup']);

    // Rotas da API
    Router::get('/api/visitors/search', [VisitorController::class, 'apiSearch']);
    Router::post('/api/groups/add-participant', [GroupController::class, 'addParticipant']);

    // Perfil
    Router::get('/profile', [ProfileController::class, 'edit']);
    Router::post('/profile', [ProfileController::class, 'update']);

    // Ministérios
    Router::get('/ministries', [MinistryController::class, 'index']);
    Router::get('/ministries/create', [MinistryController::class, 'create']);
    Router::post('/ministries', [MinistryController::class, 'store']);
    Router::get('/ministries/{id}/edit', [MinistryController::class, 'edit']);
    Router::post('/ministries/{id}', [MinistryController::class, 'update']);
    Router::post('/ministries/{id}/delete', [MinistryController::class, 'delete']);
    Router::get('/ministries/view/{id}', [MinistryController::class, 'view']);
    Router::get('/ministries/dashboard', [MinistryController::class, 'dashboard']);

    // Relatórios
    Router::get('/reports', [ReportController::class, 'index']);
    Router::get('/reports/members', [ReportController::class, 'members']);
    Router::get('/reports/groups', [ReportController::class, 'groups']);
    Router::get('/reports/visitors', [ReportController::class, 'visitors']);
    Router::get('/reports/ministries', [ReportController::class, 'ministries']);
    Router::get('/reports/export/{type}', [ReportController::class, 'export']);

    // Rotas de pedidos de oração
    Router::get('/prayers', [PrayerRequestController::class, 'index']);
    Router::post('/prayers', [PrayerRequestController::class, 'store']);
    Router::put('/prayers/{id}', [PrayerRequestController::class, 'update']);
    Router::delete('/prayers/{id}', [PrayerRequestController::class, 'delete']);
    Router::put('/prayers/{id}/status', [PrayerRequestController::class, 'updateStatus']);

    // Módulos do Sistema
    Router::get('/system-modules', [SystemModuleController::class, 'index']);
    Router::get('/system-modules/create', [SystemModuleController::class, 'create']);
    Router::post('/system-modules', [SystemModuleController::class, 'store']);
    Router::get('/system-modules/{id}/edit', [SystemModuleController::class, 'edit']);
    Router::post('/system-modules/{id}', [SystemModuleController::class, 'update']);
    Router::post('/system-modules/{id}/delete', [SystemModuleController::class, 'delete']);
});

error_log('Routes loaded successfully');
