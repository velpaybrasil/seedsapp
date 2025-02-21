<?php
/**
 * @var int $totalMinistries
 * @var int $activeMinistries
 * @var int $totalVolunteers
 * @var array $topMinistries
 */
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Dashboard de Ministérios</h1>
        <a href="<?= url('ministries') ?>" class="btn btn-secondary">
            <i class="fas fa-list"></i> Lista de Ministérios
        </a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Total de Ministérios</h5>
                    <h2 class="display-4"><?= $totalMinistries ?></h2>
                    <p class="text-muted">Ministérios cadastrados no sistema</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Ministérios Ativos</h5>
                    <h2 class="display-4"><?= $activeMinistries ?></h2>
                    <p class="text-muted">Ministérios em atividade</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Total de Voluntários</h5>
                    <h2 class="display-4"><?= $totalVolunteers ?></h2>
                    <p class="text-muted">Voluntários em ministérios</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Ministérios por Status</h5>
                </div>
                <div class="card-body">
                    <canvas id="ministriesStatusChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Ministérios Mais Ativos</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($topMinistries)): ?>
                        <ul class="list-group">
                            <?php foreach ($topMinistries as $ministry): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?= htmlspecialchars($ministry['name']) ?>
                                    <span class="badge bg-primary rounded-pill"><?= $ministry['volunteers_count'] ?> voluntários</span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted mb-0">Nenhum ministério encontrado.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de Status dos Ministérios
    var ctx = document.getElementById('ministriesStatusChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Ativos', 'Inativos'],
            datasets: [{
                label: 'Quantidade de Ministérios',
                data: [<?= $activeMinistries ?>, <?= $totalMinistries - $activeMinistries ?>],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.5)',
                    'rgba(220, 53, 69, 0.5)'
                ],
                borderColor: [
                    'rgba(40, 167, 69, 1)',
                    'rgba(220, 53, 69, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
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
});
</script>
