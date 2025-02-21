<?php
$title = 'Dashboard';
$styles = [
    'https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css'
];
?>

<div class="dashboard-container">
    <!-- Welcome Section -->
    <div class="welcome-section">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1>Bem-vindo, <?= $user['name'] ?? 'Usuário' ?></h1>
                <p>Aqui está o resumo das atividades dos visitantes</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#filterModal">
                    <i class="bi bi-funnel me-2"></i> Filtros
                </button>
                <a href="/visitors/create" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-2"></i> Novo Visitante
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <!-- Total Visitors Card -->
        <div class="stat-card animate__animated animate__fadeIn">
            <div class="stat-card-body">
                <div class="stat-card-icon bg-primary-subtle">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-card-info">
                    <h6 class="stat-card-title">Total de Visitantes</h6>
                    <div class="stat-card-value" data-value="<?= $stats['total'] ?? 0 ?>">0</div>
                    <div class="stat-card-change">
                        <span class="text-success">
                            <i class="bi bi-arrow-up"></i>
                            <?= $stats['growth_rate'] ?? 0 ?>%
                        </span>
                        <span class="text-muted">vs mês anterior</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Converted Card -->
        <div class="stat-card animate__animated animate__fadeIn" style="animation-delay: 0.1s">
            <div class="stat-card-body">
                <div class="stat-card-icon bg-success-subtle">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stat-card-info">
                    <h6 class="stat-card-title">Convertidos</h6>
                    <div class="stat-card-value" data-value="<?= $stats['converted'] ?? 0 ?>">0</div>
                    <div class="stat-card-change">
                        <span class="text-success">
                            <i class="bi bi-arrow-up"></i>
                            <?= $stats['conversion_rate'] ?? 0 ?>%
                        </span>
                        <span class="text-muted">taxa de conversão</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- In Progress Card -->
        <div class="stat-card animate__animated animate__fadeIn" style="animation-delay: 0.2s">
            <div class="stat-card-body">
                <div class="stat-card-icon bg-info-subtle">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="stat-card-info">
                    <h6 class="stat-card-title">Em Acompanhamento</h6>
                    <div class="stat-card-value" data-value="<?= $stats['in_progress'] ?? 0 ?>">0</div>
                    <div class="stat-card-change">
                        <span class="text-info">
                            <i class="bi bi-arrow-right"></i>
                            <?= $stats['progress_rate'] ?? 0 ?>%
                        </span>
                        <span class="text-muted">em processo</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Waiting Contact Card -->
        <div class="stat-card animate__animated animate__fadeIn" style="animation-delay: 0.3s">
            <div class="stat-card-body">
                <div class="stat-card-icon bg-warning-subtle">
                    <i class="bi bi-telephone"></i>
                </div>
                <div class="stat-card-info">
                    <h6 class="stat-card-title">Aguardando Contato</h6>
                    <div class="stat-card-value" data-value="<?= $stats['waiting'] ?? 0 ?>">0</div>
                    <div class="stat-card-change">
                        <span class="text-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            <?= $stats['waiting_rate'] ?? 0 ?>%
                        </span>
                        <span class="text-muted">sem contato</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="row g-4">
        <!-- Visitors Chart -->
        <div class="col-xl-8">
            <div class="content-card h-100 animate__animated animate__fadeIn" style="animation-delay: 0.4s">
                <div class="content-card-header">
                    <h5 class="content-card-title">Visitantes por Período</h5>
                    <div class="btn-group">
                        <button type="button" class="btn btn-light btn-sm active" data-period="week">7 dias</button>
                        <button type="button" class="btn btn-light btn-sm" data-period="month">30 dias</button>
                        <button type="button" class="btn btn-light btn-sm" data-period="year">12 meses</button>
                    </div>
                </div>
                <div class="content-card-body">
                    <canvas id="visitorsChart" style="width: 100%; height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Visitors -->
        <div class="col-xl-4">
            <div class="content-card h-100 animate__animated animate__fadeIn" style="animation-delay: 0.5s">
                <div class="content-card-header">
                    <h5 class="content-card-title">Visitantes Recentes</h5>
                    <a href="/visitors" class="btn btn-light btn-sm">Ver Todos</a>
                </div>
                <div class="content-card-body p-0">
                    <div class="visitor-list">
                        <?php foreach (($recent_visitors ?? []) as $visitor): ?>
                            <div class="visitor-item">
                                <div class="visitor-info">
                                    <div class="visitor-avatar">
                                        <?= strtoupper(substr($visitor['name'] ?? '', 0, 1)) ?>
                                    </div>
                                    <div class="visitor-details">
                                        <h6 class="visitor-name"><?= $visitor['name'] ?? 'Visitante' ?></h6>
                                        <span class="visitor-date"><?= $visitor['visit_date'] ?? '' ?></span>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="/visitors/view/<?= $visitor['id'] ?>" class="btn btn-light btn-sm">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="/visitors/edit/<?= $visitor['id'] ?>" class="btn btn-light btn-sm">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($recent_visitors)): ?>
                            <div class="text-center py-4 text-muted">
                                Nenhum visitante recente
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filtros</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="filterForm">
                    <div class="mb-3">
                        <label class="form-label">Período</label>
                        <select class="form-select" name="period">
                            <option value="7">Últimos 7 dias</option>
                            <option value="30">Últimos 30 dias</option>
                            <option value="90">Últimos 3 meses</option>
                            <option value="180">Últimos 6 meses</option>
                            <option value="365">Último ano</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="status[]" value="new" checked>
                            <label class="form-check-label">Novos Visitantes</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="status[]" value="in_progress" checked>
                            <label class="form-check-label">Em Acompanhamento</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="status[]" value="converted" checked>
                            <label class="form-check-label">Convertidos</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Grupos</label>
                        <select class="form-select" name="group" multiple>
                            <?php foreach (($groups ?? []) as $group): ?>
                                <option value="<?= $group['id'] ?>"><?= $group['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="applyFilters">Aplicar Filtros</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate stat values
    document.querySelectorAll('.stat-card-value').forEach(el => {
        const value = parseInt(el.dataset.value);
        let current = 0;
        const increment = value / 20;
        const timer = setInterval(() => {
            current += increment;
            if (current >= value) {
                current = value;
                clearInterval(timer);
            }
            el.textContent = Math.round(current).toLocaleString();
        }, 50);
    });

    // Initialize chart
    const ctx = document.getElementById('visitorsChart').getContext('2d');
    const visitorsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($chart_data['labels'] ?? []) ?>,
            datasets: [{
                label: 'Visitantes',
                data: <?= json_encode($chart_data['values'] ?? []) ?>,
                borderColor: `rgb(${getComputedStyle(document.documentElement).getPropertyValue('--primary-rgb')})`,
                backgroundColor: `rgba(${getComputedStyle(document.documentElement).getPropertyValue('--primary-rgb')}, 0.1)`,
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        display: true,
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Handle period buttons
    document.querySelectorAll('[data-period]').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelectorAll('[data-period]').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            // TODO: Update chart data based on period
        });
    });

    // Handle filter form
    document.getElementById('applyFilters').addEventListener('click', function() {
        const formData = new FormData(document.getElementById('filterForm'));
        // TODO: Apply filters and update dashboard
        const modal = bootstrap.Modal.getInstance(document.getElementById('filterModal'));
        modal.hide();
    });
});
</script>
