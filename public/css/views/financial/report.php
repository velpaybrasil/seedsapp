<div class="container-fluid">
    <!-- Page Title -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Relatórios Financeiros</h1>
        <div>
            <div class="btn-group me-2">
                <button type="button" 
                        class="btn btn-primary dropdown-toggle" 
                        data-bs-toggle="dropdown">
                    <i class="bi bi-download"></i> Exportar
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="/financial/report?start_date=<?= $startDate ?>&end_date=<?= $endDate ?>&export=pdf">
                            <i class="bi bi-file-pdf"></i> PDF
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="/financial/report?start_date=<?= $startDate ?>&end_date=<?= $endDate ?>&export=excel">
                            <i class="bi bi-file-excel"></i> Excel
                        </a>
                    </li>
                </ul>
            </div>
            <a href="/financial" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <form action="/financial/report" method="GET" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="start_date" class="form-label">Data Inicial</label>
                            <input type="date" 
                                   class="form-control" 
                                   id="start_date" 
                                   name="start_date"
                                   value="<?= $startDate ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label">Data Final</label>
                            <input type="date" 
                                   class="form-control" 
                                   id="end_date" 
                                   name="end_date"
                                   value="<?= $endDate ?>">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Filtrar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Daily Report -->
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Relatório Diário</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="dailyReportTable">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Tipo</th>
                                    <th>Quantidade</th>
                                    <th>Total</th>
                                    <th>Média</th>
                                    <th>Menor Valor</th>
                                    <th>Maior Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($report as $entry): ?>
                                <tr>
                                    <td><?= (new DateTime($entry['date']))->format('d/m/Y') ?></td>
                                    <td>
                                        <span class="badge bg-<?= $entry['type'] === 'tithe' ? 'success' : 'info' ?>">
                                            <?= $entry['type'] === 'tithe' ? 'Dízimo' : 'Oferta' ?>
                                        </span>
                                    </td>
                                    <td><?= $entry['transaction_count'] ?></td>
                                    <td>R$ <?= number_format($entry['total_amount'], 2, ',', '.') ?></td>
                                    <td>R$ <?= number_format($entry['avg_amount'], 2, ',', '.') ?></td>
                                    <td>R$ <?= number_format($entry['min_amount'], 2, ',', '.') ?></td>
                                    <td>R$ <?= number_format($entry['max_amount'], 2, ',', '.') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Contributors -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Maiores Contribuintes</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="topContributorsTable">
                            <thead>
                                <tr>
                                    <th>Contribuinte</th>
                                    <th>Total de Transações</th>
                                    <th>Valor Total</th>
                                    <th>Maior Contribuição</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topContributors as $contributor): ?>
                                <tr>
                                    <td><?= htmlspecialchars($contributor['contributor_name']) ?></td>
                                    <td><?= $contributor['transaction_count'] ?></td>
                                    <td>R$ <?= number_format($contributor['total_amount'], 2, ',', '.') ?></td>
                                    <td>R$ <?= number_format($contributor['max_contribution'], 2, ',', '.') ?></td>
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
        <!-- Daily Trends -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tendência Diária</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="dailyTrendsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Distribution -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Distribuição por Tipo</h6>
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
    // Initialize DataTables
    $('#dailyReportTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json'
        },
        order: [[0, 'desc']],
        pageLength: 25
    });
    
    $('#topContributorsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json'
        },
        order: [[2, 'desc']],
        pageLength: 10
    });
    
    // Initialize Charts
    initializeDailyTrendsChart();
    initializeDistributionChart();
});

function initializeDailyTrendsChart() {
    const data = <?= json_encode($report) ?>;
    const dates = [...new Set(data.map(item => item.date))];
    
    const tithesData = dates.map(date => {
        const entry = data.find(item => item.date === date && item.type === 'tithe');
        return entry ? entry.total_amount : 0;
    });
    
    const offeringsData = dates.map(date => {
        const entry = data.find(item => item.date === date && item.type === 'offering');
        return entry ? entry.total_amount : 0;
    });
    
    const ctx = document.getElementById('dailyTrendsChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates.map(date => {
                return new Date(date).toLocaleDateString('pt-BR');
            }),
            datasets: [
                {
                    label: 'Dízimos',
                    data: tithesData,
                    borderColor: '#1cc88a',
                    backgroundColor: 'rgba(28, 200, 138, 0.1)',
                    fill: true
                },
                {
                    label: 'Ofertas',
                    data: offeringsData,
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
}

function initializeDistributionChart() {
    const data = <?= json_encode($report) ?>;
    
    const totalTithes = data
        .filter(item => item.type === 'tithe')
        .reduce((sum, item) => sum + parseFloat(item.total_amount), 0);
        
    const totalOfferings = data
        .filter(item => item.type === 'offering')
        .reduce((sum, item) => sum + parseFloat(item.total_amount), 0);
    
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
}
</script>
