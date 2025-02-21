<?php
$title = 'Relatório de Grupos';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Relatório de Grupos</h1>
        <div>
            <a href="/reports/export/groups?format=pdf" class="btn btn-danger btn-sm">
                <i class="bi bi-file-pdf"></i> Exportar PDF
            </a>
            <a href="/reports/export/groups?format=excel" class="btn btn-success btn-sm">
                <i class="bi bi-file-excel"></i> Exportar Excel
            </a>
            <a href="/reports/export/groups?format=csv" class="btn btn-primary btn-sm">
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
                    <label class="form-label">Mês</label>
                    <select name="month" class="form-select">
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
                <div class="col-md-3">
                    <label class="form-label">Ano</label>
                    <select name="year" class="form-select">
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
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Ativos</option>
                        <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inativos</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                    <a href="/reports/groups" class="btn btn-secondary">Limpar</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Cards de Resumo -->
    <div class="row">
        <!-- Total de Grupos -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total de Grupos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total'] ?? 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grupos Ativos -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Grupos Ativos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['active'] ?? 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Média de Participantes -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Média de Participantes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($stats['attendance'] ?? 0, 1) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total de Membros -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total de Membros</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total_members'] ?? 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-graph-up fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Grupos -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Grupos</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Líder</th>
                            <th>Membros</th>
                            <th>Dia/Horário</th>
                            <th>Local</th>
                            <th>Status</th>
                            <th>Última Reunião</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($groups)): ?>
                            <tr>
                                <td colspan="7" class="text-center">Nenhum grupo encontrado.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($groups as $group): ?>
                                <tr>
                                    <td><?= htmlspecialchars($group['name']) ?></td>
                                    <td><?= htmlspecialchars($group['leader_name']) ?></td>
                                    <td><?= $group['total_members'] ?></td>
                                    <td><?= htmlspecialchars($group['meeting_day']) ?> às <?= htmlspecialchars($group['meeting_time']) ?></td>
                                    <td><?= htmlspecialchars($group['location']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $group['status'] === 'active' ? 'success' : 'danger' ?>">
                                            <?= $group['status'] === 'active' ? 'Ativo' : 'Inativo' ?>
                                        </span>
                                    </td>
                                    <td><?= $group['last_meeting'] ? date('d/m/Y', strtotime($group['last_meeting'])) : '-' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
