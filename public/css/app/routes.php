<?php

use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\UserController;
use App\Controllers\GroupController;
use App\Controllers\VisitorController;
use App\Controllers\ProfileController;

// Debug
error_log('Loading routes...');

// Rotas públicas
Router::get('/', [AuthController::class, 'loginForm']);
Router::get('/login', [AuthController::class, 'loginForm']);
Router::post('/login', [AuthController::class, 'login']);
Router::get('/logout', [AuthController::class, 'logout']);

// Rotas protegidas
Router::group(['middleware' => 'auth'], function() {
    error_log('Setting up protected routes...');
    
    // Dashboard
    Router::get('/dashboard', [DashboardController::class, 'index']);
    
    // Igreja Modelo
    Router::get('/igrejamodelo', [DashboardController::class, 'index']);
    
    // Usuários
    Router::get('/users', [UserController::class, 'index']);
    Router::get('/users/create', [UserController::class, 'create']);
    Router::post('/users', [UserController::class, 'store']);
    Router::get('/users/{id}/edit', [UserController::class, 'edit']);
    Router::post('/users/{id}', [UserController::class, 'update']);
    Router::get('/users/{id}/delete', [UserController::class, 'delete']);
    
    // Grupos
    Router::get('/groups', [GroupController::class, 'index']);
    Router::get('/groups/create', [GroupController::class, 'create']);
    Router::post('/groups', [GroupController::class, 'store']);
    Router::get('/groups/{id}/edit', [GroupController::class, 'edit']);
    Router::post('/groups/{id}', [GroupController::class, 'update']);
    Router::get('/groups/{id}/delete', [GroupController::class, 'delete']);
    
    // Visitantes
    Router::get('/visitors', [VisitorController::class, 'index']);
    Router::get('/visitors/create', [VisitorController::class, 'create']);
    Router::post('/visitors', [VisitorController::class, 'store']);
    Router::get('/visitors/{id}/edit', [VisitorController::class, 'edit']);
    Router::post('/visitors/{id}', [VisitorController::class, 'update']);
    Router::get('/visitors/{id}/delete', [VisitorController::class, 'delete']);
    
    // Perfil
    Router::get('/profile', [ProfileController::class, 'edit']);
    Router::post('/profile', [ProfileController::class, 'update']);
});

error_log('Routes loaded successfully');
