<?php
$title = 'Relatório de Membros';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Relatório de Membros</h1>
        <div>
            <a href="/reports/export/members?format=pdf" class="btn btn-danger btn-sm">
                <i class="bi bi-file-pdf"></i> Exportar PDF
            </a>
            <a href="/reports/export/members?format=excel" class="btn btn-success btn-sm">
                <i class="bi bi-file-excel"></i> Exportar Excel
            </a>
            <a href="/reports/export/members?format=csv" class="btn btn-primary btn-sm">
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
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="active">Ativos</option>
                        <option value="inactive">Inativos</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Grupo</label>
                    <select name="group" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach ($groups ?? [] as $group): ?>
                            <option value="<?= $group['id'] ?>"><?= htmlspecialchars($group['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Ministério</label>
                    <select name="ministry" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach ($ministries ?? [] as $ministry): ?>
                            <option value="<?= $ministry['id'] ?>"><?= htmlspecialchars($ministry['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                    <a href="/reports/members" class="btn btn-secondary">Limpar</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Resultados -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Membros</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Telefone</th>
                            <th>Grupos</th>
                            <th>Ministérios</th>
                            <th>Status</th>
                            <th>Data de Cadastro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data)): ?>
                            <tr>
                                <td colspan="7" class="text-center">Nenhum membro encontrado.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($data as $member): ?>
                                <tr>
                                    <td><?= htmlspecialchars($member['name']) ?></td>
                                    <td><?= htmlspecialchars($member['email']) ?></td>
                                    <td><?= htmlspecialchars($member['phone']) ?></td>
                                    <td><?= htmlspecialchars($member['groups']) ?></td>
                                    <td><?= htmlspecialchars($member['ministries']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $member['status'] === 'active' ? 'success' : 'danger' ?>">
                                            <?= $member['status'] === 'active' ? 'Ativo' : 'Inativo' ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($member['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
