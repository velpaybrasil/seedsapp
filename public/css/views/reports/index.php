<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Relatórios</h1>
    
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-file-alt me-1"></i>
                Relatórios Salvos
            </div>
            <div>
                <a href="/gcmanager/reports/create" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Novo Relatório
                </a>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($reports)): ?>
                <div class="alert alert-info">
                    Nenhum relatório encontrado. Clique no botão "Novo Relatório" para criar um.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
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
                                            'volunteers' => 'Voluntários'
                                        ];
                                        echo $types[$report['type']] ?? $report['type'];
                                        ?>
                                    </td>
                                    <td><?= htmlspecialchars($report['description'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($report['creator_name']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($report['created_at'])) ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="/gcmanager/reports/<?= $report['id'] ?>" class="btn btn-sm btn-primary" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="/gcmanager/reports/<?= $report['id'] ?>/edit" class="btn btn-sm btn-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" title="Excluir"
                                                onclick="confirmDelete(<?= $report['id'] ?>)">
                                                <i class="fas fa-trash"></i>
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

<!-- Modal de confirmação de exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir este relatório? Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <?= csrf_field() ?>
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

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
