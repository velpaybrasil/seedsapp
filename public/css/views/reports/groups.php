<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid px-4">
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-chart-bar me-1"></i>
            Relatório de Grupos
        </div>
        <div class="card-body">
            <!-- Filtros -->
            <form method="GET" class="mb-4">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label class="col-form-label">Período:</label>
                    </div>
                    <div class="col-md-2">
                        <select name="month" class="form-select" onchange="this.form.submit()">
                            <?php
                            $months = [
                                '01' => 'Janeiro',
                                '02' => 'Fevereiro',
                                '03' => 'Março',
                                '04' => 'Abril',
                                '05' => 'Maio',
                                '06' => 'Junho',
                                '07' => 'Julho',
                                '08' => 'Agosto',
                                '09' => 'Setembro',
                                '10' => 'Outubro',
                                '11' => 'Novembro',
                                '12' => 'Dezembro'
                            ];
                            foreach ($months as $value => $label):
                            ?>
                                <option value="<?= $value ?>" <?= $month === $value ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="year" class="form-select" onchange="this.form.submit()">
                            <?php
                            $currentYear = date('Y');
                            for ($y = $currentYear; $y >= $currentYear - 2; $y--):
                            ?>
                                <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?>>
                                    <?= $y ?>
                                </option>
                            <?php endfor; ?>
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
                                        Total de Grupos
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
                                        Grupos Ativos
                                    </div>
                                    <div class="h3 mb-0 fw-bold">
                                        <?= $stats['active'] ?? 0 ?>
                                    </div>
                                </div>
                                <div class="text-success">
                                    <i class="fas fa-check-circle fa-2x"></i>
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
                                        Média de Participantes
                                    </div>
                                    <div class="h3 mb-0 fw-bold">
                                        <?= number_format($stats['attendance'] ?? 0, 1) ?>
                                    </div>
                                </div>
                                <div class="text-info">
                                    <i class="fas fa-users fa-2x"></i>
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
