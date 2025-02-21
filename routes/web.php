<?php

use App\Core\Router;
use App\Controllers\VisitorFormController;
use App\Controllers\VisitorController;
use App\Controllers\GrowthGroupController;
use App\Controllers\DashboardController;
use App\Controllers\AuthController;
use App\Controllers\PublicController;
use App\Controllers\ProfileController;
use App\Controllers\MinistryController;
use App\Controllers\ReportController;
use App\Controllers\PrayerRequestController;
use App\Controllers\SystemModuleController;
use App\Controllers\SystemSettingsController;

// Rotas públicas
Router::get('/', [AuthController::class, 'loginForm']);
Router::get('/login', [AuthController::class, 'loginForm']);
Router::post('/login', [AuthController::class, 'login']);
Router::get('/logout', [AuthController::class, 'logout']);

// Rotas públicas do formulário de visitantes
Router::get('/f/{slug}', [VisitorFormController::class, 'show']);
Router::post('/f/{slug}/submit', [VisitorFormController::class, 'submit']);

// Rotas protegidas
Router::group(['middleware' => ['auth']], function() {
    // Dashboard
    Router::get('/dashboard', [DashboardController::class, 'index']);

    // Perfil e Configurações
    Router::get('/profile', [ProfileController::class, 'showProfile']);
    Router::put('/profile', [ProfileController::class, 'updateProfile']);
    Router::get('/settings', [ProfileController::class, 'showSettings']);
    Router::put('/settings', [ProfileController::class, 'updateSettings']);

    // Visitor Forms
    Router::get('/visitor-forms', [VisitorFormController::class, 'index']);
    Router::get('/visitor-forms/create', [VisitorFormController::class, 'create']);
    Router::post('/visitor-forms', [VisitorFormController::class, 'store']);
    Router::get('/visitor-forms/{id}/edit', [VisitorFormController::class, 'edit']);
    Router::post('/visitor-forms/{id}', [VisitorFormController::class, 'update']);
    Router::post('/visitor-forms/{id}/delete', [VisitorFormController::class, 'delete']);

    // Visitor Form Fields
    Router::post('/visitor-forms/{formId}/fields', [VisitorFormController::class, 'addField']);
    Router::put('/visitor-forms/{formId}/fields/{fieldId}', [VisitorFormController::class, 'updateField']);
    Router::delete('/visitor-forms/{formId}/fields/{fieldId}', [VisitorFormController::class, 'deleteField']);
    Router::post('/visitor-forms/{formId}/fields/order', [VisitorFormController::class, 'updateFieldOrder']);

    // Rotas para submissões
    Router::get('/visitor-forms/{id}/submissions', [VisitorFormController::class, 'submissions']);
    Router::post('/visitor-forms/submissions/{id}/create-visitor', [VisitorFormController::class, 'createVisitorFromSubmission']);
    Router::delete('/visitor-forms/submissions/{id}', [VisitorFormController::class, 'deleteSubmission']);

    // Rotas de Visitantes
    Router::get('/visitors', [VisitorController::class, 'index']);
    Router::get('/visitors/create', [VisitorController::class, 'create']);
    Router::post('/visitors', [VisitorController::class, 'store']);
    Router::get('/visitors/{id}', [VisitorController::class, 'show']);
    Router::get('/visitors/{id}/edit', [VisitorController::class, 'edit']);
    Router::put('/visitors/{id}', [VisitorController::class, 'update']);
    Router::delete('/visitors/{id}', [VisitorController::class, 'delete']);
    Router::get('/visitors/export', [VisitorController::class, 'export']);
    Router::get('/visitors/{id}/contact-logs', [VisitorController::class, 'getContactLogs']);
    Router::post('/visitors/{id}/contact-logs', [VisitorController::class, 'addContactLog']);
    Router::post('/visitors/{id}/contact-logs/{logId}/status', [VisitorController::class, 'updateFollowUpStatus']);
    Router::get('/visitors/search', [VisitorController::class, 'search']);
    Router::post('/visitors/add-to-group', [VisitorController::class, 'addToGroup']);
    Router::get('/api/visitors/search', [VisitorController::class, 'apiSearch']);

    // Rotas de Grupos
    Router::get('/groups', [GrowthGroupController::class, 'index']);
    Router::get('/groups/create', [GrowthGroupController::class, 'create']);
    Router::post('/groups', [GrowthGroupController::class, 'store']);
    Router::get('/groups/{id}', [GrowthGroupController::class, 'show']);
    Router::get('/groups/{id}/edit', [GrowthGroupController::class, 'edit']);
    Router::put('/groups/{id}', [GrowthGroupController::class, 'update']);
    Router::delete('/groups/{id}', [GrowthGroupController::class, 'delete']);

    // Rotas para membros dos grupos
    Router::get('/groups/{id}/members', [GrowthGroupController::class, 'members']);
    Router::post('/groups/{id}/members/add', [GrowthGroupController::class, 'addMember']);
    Router::post('/groups/{id}/members/remove', [GrowthGroupController::class, 'removeMember']);
    Router::post('/groups/{id}/members/status', [GrowthGroupController::class, 'updateMemberStatus']);

    // Outras rotas protegidas...
    // Rotas de Ministérios
    Router::get('/ministries', [MinistryController::class, 'index']);
    Router::get('/ministries/create', [MinistryController::class, 'create']);
    Router::post('/ministries', [MinistryController::class, 'store']);
    Router::get('/ministries/{id}', [MinistryController::class, 'show']);
    Router::get('/ministries/{id}/edit', [MinistryController::class, 'edit']);
    Router::put('/ministries/{id}', [MinistryController::class, 'update']);
    Router::delete('/ministries/{id}', [MinistryController::class, 'delete']);

    // Rotas de Relatórios
    Router::get('/reports', [ReportController::class, 'index']);
    Router::get('/reports/{id}', [ReportController::class, 'show']);

    // Rotas de Pedidos de Oração
    Router::get('/prayer-requests', [PrayerRequestController::class, 'index']);
    Router::get('/prayer-requests/create', [PrayerRequestController::class, 'create']);
    Router::post('/prayer-requests/store', [PrayerRequestController::class, 'store']);
    Router::post('/prayer-requests/update-status', [PrayerRequestController::class, 'updateStatus']);

    // Rotas de Módulos do Sistema
    Router::get('/system-modules', [SystemModuleController::class, 'index']);
    Router::get('/system-modules/create', [SystemModuleController::class, 'create']);
    Router::post('/system-modules', [SystemModuleController::class, 'store']);
    Router::get('/system-modules/{id}', [SystemModuleController::class, 'show']);
    Router::get('/system-modules/{id}/edit', [SystemModuleController::class, 'edit']);
    Router::put('/system-modules/{id}', [SystemModuleController::class, 'update']);
    Router::delete('/system-modules/{id}', [SystemModuleController::class, 'delete']);

    // Configurações do Sistema
    Router::get('/settings/system', [SystemSettingsController::class, 'index']);
    Router::get('/settings/system/create', [SystemSettingsController::class, 'create']);
    Router::post('/settings/system', [SystemSettingsController::class, 'store']);
    Router::get('/settings/system/{category}/{key}/edit', [SystemSettingsController::class, 'edit']);
    Router::post('/settings/system/{category}/{key}', [SystemSettingsController::class, 'update']);
    Router::post('/settings/system/{category}/{key}/delete', [SystemSettingsController::class, 'delete']);
});
