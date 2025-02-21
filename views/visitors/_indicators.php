<?php
// Indicadores de visitantes
?>
<!-- Indicadores -->
<div class="row g-4 mb-4">
    <!-- Visitantes esta semana -->
    <div class="col-sm-6 col-md-4 col-xl">
        <div class="card h-100 border-0 bg-gradient" style="background-color: #4158D0;background-image: linear-gradient(43deg, #4158D0 0%, #C850C0 46%, #FFCC70 100%);">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="rounded-circle bg-white bg-opacity-25 p-2">
                        <i class="fas fa-clock fa-lg text-white"></i>
                    </div>
                    <span class="badge bg-white bg-opacity-25 text-white">Esta semana</span>
                </div>
                <h3 class="display-5 fw-bold text-white mb-1"><?= $stats['current_week'] ?></h3>
                <p class="text-white text-opacity-75 mb-0">Visitantes</p>
            </div>
        </div>
    </div>

    <!-- Visitantes este mês -->
    <div class="col-sm-6 col-md-4 col-xl">
        <div class="card h-100 border-0 bg-gradient" style="background-color: #0093E9;background-image: linear-gradient(160deg, #0093E9 0%, #80D0C7 100%);">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="rounded-circle bg-white bg-opacity-25 p-2">
                        <i class="fas fa-users fa-lg text-white"></i>
                    </div>
                    <span class="badge bg-white bg-opacity-25 text-white">Este mês</span>
                </div>
                <h3 class="display-5 fw-bold text-white mb-1"><?= $stats['current_month'] ?></h3>
                <p class="text-white text-opacity-75 mb-0">Visitantes</p>
            </div>
        </div>
    </div>

    <!-- Visitantes mês passado -->
    <div class="col-sm-6 col-md-4 col-xl">
        <div class="card h-100 border-0 bg-gradient" style="background-color: #8EC5FC;background-image: linear-gradient(62deg, #8EC5FC 0%, #E0C3FC 100%);">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="rounded-circle bg-white bg-opacity-25 p-2">
                        <i class="fas fa-calendar-alt fa-lg text-white"></i>
                    </div>
                    <span class="badge bg-white bg-opacity-25 text-white">Mês passado</span>
                </div>
                <h3 class="display-5 fw-bold text-white mb-1"><?= $stats['last_month'] ?></h3>
                <p class="text-white text-opacity-75 mb-0">Visitantes</p>
            </div>
        </div>
    </div>

    <!-- Visitantes este ano -->
    <div class="col-sm-6 col-md-4 col-xl">
        <div class="card h-100 border-0 bg-gradient" style="background-color: #85FFBD;background-image: linear-gradient(45deg, #85FFBD 0%, #FFFB7D 100%);">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="rounded-circle bg-white bg-opacity-25 p-2">
                        <i class="fas fa-chart-line fa-lg text-white"></i>
                    </div>
                    <span class="badge bg-white bg-opacity-25 text-white">Este ano</span>
                </div>
                <h3 class="display-5 fw-bold text-white mb-1"><?= $stats['current_year'] ?></h3>
                <p class="text-white text-opacity-75 mb-0">Visitantes</p>
            </div>
        </div>
    </div>

    <!-- Encaminhados para Grupo -->
    <div class="col-sm-6 col-md-4 col-xl">
        <div class="card h-100 border-0 bg-gradient" style="background-color: #FF9A8B;background-image: linear-gradient(90deg, #FF9A8B 0%, #FF6A88 55%, #FF99AC 100%);">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="rounded-circle bg-white bg-opacity-25 p-2">
                        <i class="fas fa-user-plus fa-lg text-white"></i>
                    </div>
                    <span class="badge bg-white bg-opacity-25 text-white">Grupos</span>
                </div>
                <h3 class="display-5 fw-bold text-white mb-1"><?= $stats['forwarded_to_group'] ?></h3>
                <p class="text-white text-opacity-75 mb-0">Encaminhados</p>
            </div>
        </div>
    </div>
</div>
