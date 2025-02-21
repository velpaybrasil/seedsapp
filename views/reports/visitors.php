<?php
$title = 'Relatório de Visitantes';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Relatório de Visitantes</h1>
        <div>
            <a href="/reports/export/visitors?format=pdf" class="btn btn-danger btn-sm">
                <i class="bi bi-file-pdf"></i> Exportar PDF
            </a>
            <a href="/reports/export/visitors?format=excel" class="btn btn-success btn-sm">
                <i class="bi bi-file-excel"></i> Exportar Excel
            </a>
            <a href="/reports/export/visitors?format=csv" class="btn btn-primary btn-sm">
                <i class="bi bi-file-text"></i> Exportar CSV
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtros</h6>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Período</label>
                    <select name="period" class="form-select">
                        <option value="week" <?= $period === 'week' ? 'selected' : '' ?>>Última Semana</option>
                        <option value="month" <?= $period === 'month' ? 'selected' : '' ?>>Último Mês</option>
                        <option value="quarter" <?= $period === 'quarter' ? 'selected' : '' ?>>Último Trimestre</option>
                        <option value="year" <?= $period === 'year' ? 'selected' : '' ?>>Último Ano</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="contacted" <?= $status === 'contacted' ? 'selected' : '' ?>>Contatados</option>
                        <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pendentes</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                    <a href="/reports/visitors" class="btn btn-secondary">Limpar</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Cards de Resumo -->
    <div class="row">
        <!-- Total de Visitantes -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total de Visitantes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total'] ?? 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-plus fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Visitantes Contatados -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Contatados</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['contacted'] ?? 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Taxa de Retorno -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Taxa de Retorno</div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?= $stats['return_rate'] ?? 0 ?>%</div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: <?= $stats['return_rate'] ?? 0 ?>%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-graph-up fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Média por Culto -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Média por Culto</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($stats['average_per_service'] ?? 0, 1) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar-event fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Visitantes -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Visitantes</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Telefone</th>
                            <th>Primeira Visita</th>
                            <th>Última Visita</th>
                            <th>Total de Visitas</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($visitors)): ?>
                            <tr>
                                <td colspan="7" class="text-center">Nenhum visitante encontrado.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($visitors as $visitor): ?>
                                <tr>
                                    <td><?= htmlspecialchars($visitor['name']) ?></td>
                                    <td><?= htmlspecialchars($visitor['email']) ?></td>
                                    <td><?= htmlspecialchars($visitor['phone']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($visitor['first_visit'])) ?></td>
                                    <td><?= date('d/m/Y', strtotime($visitor['last_visit'])) ?></td>
                                    <td><?= $visitor['total_visits'] ?></td>
                                    <td>
                                        <span class="badge bg-<?= $visitor['status'] === 'contacted' ? 'success' : 'warning' ?>">
                                            <?= $visitor['status'] === 'contacted' ? 'Contatado' : 'Pendente' ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
