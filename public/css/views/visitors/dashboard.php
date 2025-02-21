<?php
$title = 'Dashboard de Visitantes';
$styles = '<link href="/gcmanager/assets/css/visitors.css" rel="stylesheet">';
?>

<div class="container-fluid">
    <!-- Page Title -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Dashboard de Visitantes</h1>
        <div>
            <a href="/gcmanager/visitors/create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Novo Visitante
            </a>
        </div>
    </div>
    
    <!-- Period Filter -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-3">
                    <label for="period" class="form-label">Período</label>
                    <select class="form-select" id="period" name="period" onchange="this.form.submit()">
                        <option value="week" <?= $period === 'week' ? 'selected' : '' ?>>Última Semana</option>
                        <option value="month" <?= $period === 'month' ? 'selected' : '' ?>>Último Mês</option>
                        <option value="year" <?= $period === 'year' ? 'selected' : '' ?>>Último Ano</option>
                    </select>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-left-primary shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total de Visitantes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $stats['total'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card border-left-success shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Convertidos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $stats['converted'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-heart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card border-left-info shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Em Acompanhamento
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $stats['in_progress'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-friends fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card border-left-warning shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Follow-ups Pendentes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count($pendingFollowUps) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts Row -->
    <div class="row">
        <!-- Visitors Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Visitantes por Período</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="visitorsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Status Pie Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Distribuição por Status</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie">
                        <canvas id="statusPieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Pending Follow-ups -->
    <?php if (!empty($pendingFollowUps)): ?>
    <div class="card shadow-sm mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Follow-ups Pendentes</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Visitante</th>
                            <th>Último Contato</th>
                            <th>Próximo Contato</th>
                            <th>Responsável</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingFollowUps as $followUp): ?>
                        <tr>
                            <td>
                                <a href="/gcmanager/visitors/<?= $followUp['visitor_id'] ?>" class="text-decoration-none">
                                    <?= htmlspecialchars($followUp['visitor_name']) ?>
                                </a>
                            </td>
                            <td><?= date('d/m/Y', strtotime($followUp['last_contact'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($followUp['next_contact'])) ?></td>
                            <td><?= htmlspecialchars($followUp['responsible_name']) ?></td>
                            <td>
                                <a href="/gcmanager/visitors/<?= $followUp['visitor_id'] ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> Ver Detalhes
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Charts Initialization -->
<script>
// Visitors Chart
const visitorsCtx = document.getElementById('visitorsChart').getContext('2d');
new Chart(visitorsCtx, {
    type: 'line',
    data: {
        labels: <?= json_encode(array_column($stats['daily'], 'date')) ?>,
        datasets: [{
            label: 'Total de Visitantes',
            data: <?= json_encode(array_column($stats['daily'], 'total')) ?>,
            borderColor: '#4e73df',
            tension: 0.3,
            fill: false
        }]
    },
    options: {
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Status Pie Chart
const statusCtx = document.getElementById('statusPieChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Novo', 'Em Acompanhamento', 'Convertido', 'Membro', 'Inativo'],
        datasets: [{
            data: [
                <?= $stats['status']['new'] ?? 0 ?>,
                <?= $stats['status']['in_progress'] ?? 0 ?>,
                <?= $stats['status']['converted'] ?? 0 ?>,
                <?= $stats['status']['member'] ?? 0 ?>,
                <?= $stats['status']['inactive'] ?? 0 ?>
            ],
            backgroundColor: ['#4e73df', '#36b9cc', '#1cc88a', '#1cc88a', '#858796']
        }]
    },
    options: {
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
