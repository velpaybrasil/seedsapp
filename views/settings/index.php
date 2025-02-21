<?php
/**
 * Settings Index View
 * This view shows the main settings page with navigation to different settings sections
 */
?>

<div class="container-fluid">
    <!-- Page Title -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Configurações</h1>
    </div>

    <!-- Settings Navigation Cards -->
    <div class="row">
        <!-- Profile Settings Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Perfil</div>
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Gerencie suas informações pessoais
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="/settings/profile" class="btn btn-primary btn-sm btn-block">
                            Acessar Perfil
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Settings Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Segurança</div>
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Altere sua senha e configurações de segurança
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shield-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="/settings/profile#security" class="btn btn-success btn-sm btn-block">
                            Configurar Segurança
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notification Settings Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Notificações</div>
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Configure suas preferências de notificação
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bell fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="/settings/profile#notifications" class="btn btn-info btn-sm btn-block">
                            Configurar Notificações
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Settings Section -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Outras Configurações</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Theme Settings -->
                        <div class="col-md-6 mb-4">
                            <h5>Tema</h5>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="darkMode">
                                <label class="custom-control-label" for="darkMode">Modo Escuro</label>
                            </div>
                        </div>

                        <!-- Language Settings -->
                        <div class="col-md-6 mb-4">
                            <h5>Idioma</h5>
                            <select class="form-control">
                                <option value="pt_BR">Português (Brasil)</option>
                                <option value="en">English</option>
                                <option value="es">Español</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Settings Page Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dark Mode Toggle
    const darkModeToggle = document.getElementById('darkMode');
    const body = document.body;

    // Check if dark mode is enabled in localStorage
    if (localStorage.getItem('darkMode') === 'enabled') {
        body.classList.add('dark-mode');
        darkModeToggle.checked = true;
    }

    darkModeToggle.addEventListener('change', function() {
        if (this.checked) {
            body.classList.add('dark-mode');
            localStorage.setItem('darkMode', 'enabled');
        } else {
            body.classList.remove('dark-mode');
            localStorage.setItem('darkMode', 'disabled');
        }
    });
});
</script>
