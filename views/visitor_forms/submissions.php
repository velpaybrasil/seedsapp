<?php
$title = 'Submissões do Formulário';
?>

<div class="container-fluid">
    <!-- Page Title -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0"><?= htmlspecialchars($form['title']) ?></h1>
            <p class="text-muted mb-0">Submissões do Formulário</p>
        </div>
        <div>
            <a href="<?= url('/visitor-forms/' . $form['id'] . '/edit') ?>" class="btn btn-outline-primary me-2">
                <i class="fas fa-edit me-2"></i>Editar Formulário
            </a>
            <a href="<?= url('/visitor-forms') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Voltar
            </a>
        </div>
    </div>

    <!-- Lista de Submissões -->
    <div class="card shadow-sm">
        <div class="card-body">
            <?php if (empty($submissions)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Nenhuma submissão recebida ainda.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Visitante</th>
                                <th>Dados Enviados</th>
                                <th>IP</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($submissions as $submission): ?>
                                <?php 
                                $submissionData = json_decode($submission['submission_data'], true);
                                ?>
                                <tr>
                                    <td><?= (new DateTime($submission['created_at']))->format('d/m/Y H:i') ?></td>
                                    <td>
                                        <?php if ($submission['visitor_id']): ?>
                                            <a href="<?= url('/visitors/' . $submission['visitor_id']) ?>" class="text-decoration-none">
                                                <?= htmlspecialchars($submission['visitor_name']) ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">Não vinculado</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-primary"
                                                onclick='showSubmissionData(<?= json_encode($submissionData) ?>)'>
                                            <i class="fas fa-eye me-2"></i>Ver Dados
                                        </button>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?= htmlspecialchars($submission['ip_address']) ?></small>
                                    </td>
                                    <td>
                                        <div class="btn-group float-end">
                                            <?php if (!$submission['visitor_id'] && 
                                                    (!empty($submissionData['email']) || !empty($submissionData['phone']))): ?>
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-success"
                                                        onclick="createVisitor(<?= $submission['id'] ?>, <?= json_encode($submissionData) ?>)"
                                                        title="Criar Visitante">
                                                    <i class="fas fa-user-plus"></i>
                                                </button>
                                            <?php endif; ?>
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="deleteSubmission(<?= $submission['id'] ?>)"
                                                    title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <?php if ($pagination['total'] > $pagination['perPage']): ?>
                    <div class="d-flex justify-content-center mt-4">
                        <nav>
                            <ul class="pagination">
                                <?php
                                $totalPages = ceil($pagination['total'] / $pagination['perPage']);
                                $currentPage = $pagination['page'];
                                ?>
                                
                                <!-- Primeira página -->
                                <li class="page-item <?= $currentPage == 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= url('/visitor-forms/' . $form['id'] . '/submissions?page=1') ?>">
                                        <i class="fas fa-angle-double-left"></i>
                                    </a>
                                </li>
                                
                                <!-- Página anterior -->
                                <li class="page-item <?= $currentPage == 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= url('/visitor-forms/' . $form['id'] . '/submissions?page=' . ($currentPage - 1)) ?>">
                                        <i class="fas fa-angle-left"></i>
                                    </a>
                                </li>
                                
                                <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                                    <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                        <a class="page-link" href="<?= url('/visitor-forms/' . $form['id'] . '/submissions?page=' . $i) ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                
                                <!-- Próxima página -->
                                <li class="page-item <?= $currentPage == $totalPages ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= url('/visitor-forms/' . $form['id'] . '/submissions?page=' . ($currentPage + 1)) ?>">
                                        <i class="fas fa-angle-right"></i>
                                    </a>
                                </li>
                                
                                <!-- Última página -->
                                <li class="page-item <?= $currentPage == $totalPages ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= url('/visitor-forms/' . $form['id'] . '/submissions?page=' . $totalPages) ?>">
                                        <i class="fas fa-angle-double-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal de Dados da Submissão -->
<div class="modal fade" id="submissionDataModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Dados da Submissão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table">
                        <tbody id="submissionDataTable"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let submissionModal;

function showSubmissionData(data) {
    if (!submissionModal) {
        submissionModal = new bootstrap.Modal(document.getElementById('submissionDataModal'));
    }
    
    const table = document.getElementById('submissionDataTable');
    table.innerHTML = '';
    
    for (const [key, value] of Object.entries(data)) {
        const row = document.createElement('tr');
        
        const keyCell = document.createElement('th');
        keyCell.textContent = key;
        keyCell.style.width = '40%';
        
        const valueCell = document.createElement('td');
        valueCell.textContent = Array.isArray(value) ? value.join(', ') : value;
        
        row.appendChild(keyCell);
        row.appendChild(valueCell);
        table.appendChild(row);
    }
    
    submissionModal.show();
}

function createVisitor(submissionId, data) {
    if (!confirm('Deseja criar um novo visitante com estes dados?')) {
        return;
    }
    
    fetch(`/visitor-forms/submissions/${submissionId}/create-visitor`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            throw new Error(data.error);
        }
        location.reload();
    })
    .catch(error => {
        alert('Erro ao criar visitante: ' + error.message);
    });
}

function deleteSubmission(submissionId) {
    if (!confirm('Tem certeza que deseja excluir esta submissão?')) {
        return;
    }
    
    fetch(`/visitor-forms/submissions/${submissionId}`, {
        method: 'DELETE'
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            throw new Error(data.error);
        }
        location.reload();
    })
    .catch(error => {
        alert('Erro ao excluir submissão: ' + error.message);
    });
}
</script>
