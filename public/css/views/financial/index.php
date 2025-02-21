<div class="container-fluid">
    <!-- Page Title -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Financeiro</h1>
        <div>
            <a href="/financial/report" class="btn btn-info me-2">
                <i class="bi bi-file-earmark-text"></i> Relatórios
            </a>
            <a href="/financial/create" class="btn btn-success">
                <i class="bi bi-plus-lg"></i> Nova Transação
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <!-- Monthly Total -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total do Mês
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                R$ <?= number_format($stats['monthly_total'], 2, ',', '.') ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-cash-stack fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Tithes -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Dízimos do Mês
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                R$ <?= number_format($stats['monthly_tithes'], 2, ',', '.') ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-wallet2 fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Offerings -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Ofertas do Mês
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                R$ <?= number_format($stats['monthly_offerings'], 2, ',', '.') ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-gift fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Transactions Table -->
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Transações</h6>
                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                        <i class="bi bi-funnel"></i> Filtros
                    </button>
                </div>
                <div class="collapse" id="filterCollapse">
                    <div class="card-body border-bottom">
                        <form action="/financial" method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label for="start_date" class="form-label">Data Inicial</label>
                                <input type="date" 
                                       class="form-control" 
                                       id="start_date" 
                                       name="start_date"
                                       value="<?= $filters['start_date'] ?? '' ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="end_date" class="form-label">Data Final</label>
                                <input type="date" 
                                       class="form-control" 
                                       id="end_date" 
                                       name="end_date"
                                       value="<?= $filters['end_date'] ?? '' ?>">
                            </div>
                            <div class="col-md-2">
                                <label for="type" class="form-label">Tipo</label>
                                <select class="form-select" id="type" name="type">
                                    <option value="">Todos</option>
                                    <option value="tithe" <?= ($filters['type'] ?? '') === 'tithe' ? 'selected' : '' ?>>
                                        Dízimo
                                    </option>
                                    <option value="offering" <?= ($filters['type'] ?? '') === 'offering' ? 'selected' : '' ?>>
                                        Oferta
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="contributor" class="form-label">Contribuinte</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="contributor" 
                                       name="contributor"
                                       value="<?= $filters['contributor'] ?? '' ?>">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i> Filtrar
                                </button>
                                <a href="/financial" class="btn btn-secondary">
                                    <i class="bi bi-x-lg"></i> Limpar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="transactionsTable">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Contribuinte</th>
                                    <th>Tipo</th>
                                    <th>Valor</th>
                                    <th>Método</th>
                                    <th>Registrado por</th>
                                    <th>Observações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $transaction): ?>
                                <tr>
                                    <td><?= (new DateTime($transaction['transaction_date']))->format('d/m/Y') ?></td>
                                    <td><?= htmlspecialchars($transaction['contributor_name']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $transaction['type'] === 'tithe' ? 'success' : 'info' ?>">
                                            <?= $transaction['type'] === 'tithe' ? 'Dízimo' : 'Oferta' ?>
                                        </span>
                                    </td>
                                    <td>R$ <?= number_format($transaction['amount'], 2, ',', '.') ?></td>
                                    <td><?= htmlspecialchars($transaction['payment_method']) ?></td>
                                    <td><?= htmlspecialchars($transaction['created_by_name']) ?></td>
                                    <td><?= htmlspecialchars($transaction['notes'] ?? '') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Monthly Trends -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tendências Mensais</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="monthlyTrendsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Distribution -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Distribuição</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie">
                        <canvas id="distributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DataTables -->
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#transactionsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json'
        },
        order: [[0, 'desc']],
        pageLength: 25
    });
    
    // Load Charts
    loadMonthlyTrends();
    loadDistribution();
});

async function loadMonthlyTrends() {
    try {
        const response = await fetch('/financial/stats?period=year');
        const data = await response.json();
        
        const ctx = document.getElementById('monthlyTrendsChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.map(item => item.date),
                datasets: [
                    {
                        label: 'Dízimos',
                        data: data.map(item => item.tithes),
                        borderColor: '#1cc88a',
                        backgroundColor: 'rgba(28, 200, 138, 0.1)',
                        fill: true
                    },
                    {
                        label: 'Ofertas',
                        data: data.map(item => item.offerings),
                        borderColor: '#36b9cc',
                        backgroundColor: 'rgba(54, 185, 204, 0.1)',
                        fill: true
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => `R$ ${value.toLocaleString('pt-BR')}`
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: context => {
                                const label = context.dataset.label;
                                const value = context.raw;
                                return `${label}: R$ ${value.toLocaleString('pt-BR')}`;
                            }
                        }
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error loading monthly trends:', error);
    }
}

async function loadDistribution() {
    try {
        const response = await fetch('/financial/stats?period=month');
        const data = await response.json();
        
        const totalTithes = data.reduce((sum, item) => sum + parseFloat(item.tithes), 0);
        const totalOfferings = data.reduce((sum, item) => sum + parseFloat(item.offerings), 0);
        
        const ctx = document.getElementById('distributionChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Dízimos', 'Ofertas'],
                datasets: [{
                    data: [totalTithes, totalOfferings],
                    backgroundColor: ['#1cc88a', '#36b9cc'],
                    hoverBackgroundColor: ['#17a673', '#2c9faf']
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: context => {
                                const label = context.label;
                                const value = context.raw;
                                const total = totalTithes + totalOfferings;
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: R$ ${value.toLocaleString('pt-BR')} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error loading distribution:', error);
    }
}
</script>
