<?php
$title = 'Relatório de Ministérios';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Relatório de Ministérios</h1>
        <div>
            <a href="/reports/export/ministries?format=pdf" class="btn btn-danger btn-sm">
                <i class="bi bi-file-pdf"></i> Exportar PDF
            </a>
            <a href="/reports/export/ministries?format=excel" class="btn btn-success btn-sm">
                <i class="bi bi-file-excel"></i> Exportar Excel
            </a>
            <a href="/reports/export/ministries?format=csv" class="btn btn-primary btn-sm">
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
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Ativos</option>
                        <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inativos</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                    <a href="/reports/ministries" class="btn btn-secondary">Limpar</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Cards de Resumo -->
    <div class="row">
        <!-- Total de Ministérios -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total de Ministérios</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total'] ?? 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ministérios Ativos -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Ministérios Ativos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['active'] ?? 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total de Membros -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total de Membros</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total_members'] ?? 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total de Eventos -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total de Eventos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total_events'] ?? 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar-event fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Ministérios -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Ministérios</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Líderes</th>
                            <th>Grupos</th>
                            <th>Participantes</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($ministries)): ?>
                            <tr>
                                <td colspan="5" class="text-center">Nenhum ministério encontrado.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($ministries as $ministry): ?>
                                <tr>
                                    <td><?= htmlspecialchars($ministry['name']) ?></td>
                                    <td><?= htmlspecialchars($ministry['leaders'] ?? '-') ?></td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?= $ministry['total_groups'] ?> grupo<?= $ministry['total_groups'] != 1 ? 's' : '' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">
                                            <?= $ministry['total_participants'] ?> pessoa<?= $ministry['total_participants'] != 1 ? 's' : '' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $ministry['status'] === 'active' ? 'success' : 'danger' ?>">
                                            <?= $ministry['status'] === 'active' ? 'Ativo' : 'Inativo' ?>
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
