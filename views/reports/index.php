<?php
$title = 'Relatórios';
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Relatórios</h1>
    
    <div class="row">
        <!-- Relatório de Membros -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Membros</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Relatório de Membros</div>
                        </div>
                        <div class="col-auto">
                            <a href="/reports/members" class="btn btn-primary btn-sm">
                                <i class="bi bi-people"></i> Visualizar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Relatório de Grupos -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Grupos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Relatório de Grupos</div>
                        </div>
                        <div class="col-auto">
                            <a href="/reports/groups" class="btn btn-success btn-sm">
                                <i class="bi bi-people-fill"></i> Visualizar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Relatório de Visitantes -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Visitantes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Relatório de Visitantes</div>
                        </div>
                        <div class="col-auto">
                            <a href="/reports/visitors" class="btn btn-info btn-sm">
                                <i class="bi bi-person-plus"></i> Visualizar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Relatório de Ministérios -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Ministérios</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Relatório de Ministérios</div>
                        </div>
                        <div class="col-auto">
                            <a href="/reports/ministries" class="btn btn-warning btn-sm">
                                <i class="bi bi-building"></i> Visualizar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Relatórios Personalizados -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Relatórios Personalizados</h6>
            <a href="/reports/create" class="btn btn-primary btn-sm">
                <i class="bi bi-plus"></i> Novo Relatório
            </a>
        </div>
        <div class="card-body">
            <?php if (empty($reports)): ?>
                <div class="alert alert-info">
                    Nenhum relatório personalizado encontrado. Clique no botão "Novo Relatório" para criar um.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Tipo</th>
                                <th>Descrição</th>
                                <th>Criado por</th>
                                <th>Data de Criação</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reports as $report): ?>
                                <tr>
                                    <td><?= htmlspecialchars($report['name']) ?></td>
                                    <td>
                                        <?php
                                        $types = [
                                            'visitors' => 'Visitantes',
                                            'groups' => 'Grupos',
                                            'members' => 'Membros',
                                            'ministries' => 'Ministérios'
                                        ];
                                        echo $types[$report['type']] ?? $report['type'];
                                        ?>
                                    </td>
                                    <td><?= htmlspecialchars($report['description'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($report['creator_name']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($report['created_at'])) ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="/reports/<?= $report['id'] ?>" class="btn btn-primary btn-sm">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="/reports/<?= $report['id'] ?>/edit" class="btn btn-warning btn-sm">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $report['id'] ?>)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Tem certeza que deseja excluir este relatório? Esta ação não pode ser desfeita.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const form = document.getElementById('deleteForm');
    form.action = `/gcmanager/reports/${id}/delete`;
    modal.show();
}
</script>
