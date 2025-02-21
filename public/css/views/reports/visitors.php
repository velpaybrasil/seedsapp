<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid px-4">
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-chart-line me-1"></i>
            Relatório de Visitantes
        </div>
        <div class="card-body">
            <!-- Filtros -->
            <form method="GET" class="mb-4">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label class="col-form-label">Período:</label>
                    </div>
                    <div class="col-md-2">
                        <select name="period" class="form-select" onchange="this.form.submit()">
                            <option value="week" <?= $period === 'week' ? 'selected' : '' ?>>Última Semana</option>
                            <option value="month" <?= $period === 'month' ? 'selected' : '' ?>>Último Mês</option>
                            <option value="year" <?= $period === 'year' ? 'selected' : '' ?>>Último Ano</option>
                        </select>
                    </div>
                </div>
            </form>

            <!-- Estatísticas -->
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-xs text-primary text-uppercase fw-bold mb-1">
                                        Total de Visitantes
                                    </div>
                                    <div class="h3 mb-0 fw-bold">
                                        <?= $stats['total'] ?? 0 ?>
                                    </div>
                                </div>
                                <div class="text-primary">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-xs text-success text-uppercase fw-bold mb-1">
                                        Retornaram
                                    </div>
                                    <div class="h3 mb-0 fw-bold">
                                        <?= $stats['returned'] ?? 0 ?>
                                    </div>
                                </div>
                                <div class="text-success">
                                    <i class="fas fa-redo fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-xs text-info text-uppercase fw-bold mb-1">
                                        Taxa de Retorno
                                    </div>
                                    <div class="h3 mb-0 fw-bold">
                                        <?= number_format(($stats['returned'] / max($stats['total'], 1)) * 100, 1) ?>%
                                    </div>
                                </div>
                                <div class="text-info">
                                    <i class="fas fa-percent fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
