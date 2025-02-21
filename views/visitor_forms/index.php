<?php
use App\Core\View;

require_once VIEWS_PATH . '/partials/header.php'; ?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Formulários de Visitantes</h1>
        <a href="<?= url('/visitor-forms/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Novo Formulário
        </a>
    </div>

    <?php if (!empty($forms)): ?>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>URL</th>
                                <th>Submissões</th>
                                <th>Status</th>
                                <th>Criado em</th>
                                <th width="200">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($forms as $form): ?>
                                <tr>
                                    <td><?= htmlspecialchars($form['title']) ?></td>
                                    <td>
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm" 
                                                   value="<?= url('/f/' . $form['slug']) ?>" 
                                                   readonly>
                                            <button class="btn btn-outline-secondary btn-sm copy-url" 
                                                    type="button" 
                                                    data-url="<?= url('/f/' . $form['slug']) ?>">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="<?= url('/visitor-forms/' . $form['id'] . '/submissions') ?>" 
                                           class="text-decoration-none">
                                            <?= $form['submissions_count'] ?> submissões
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge <?= $form['active'] ? 'bg-success' : 'bg-danger' ?>">
                                            <?= $form['active'] ? 'Ativo' : 'Inativo' ?>
                                        </span>
                                    </td>
                                    <td><?= View::formatDate($form['created_at']) ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?= url('/f/' . $form['slug']) ?>" 
                                               class="btn btn-sm btn-outline-primary" 
                                               target="_blank">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= url('/visitor-forms/' . $form['id'] . '/edit') ?>" 
                                               class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    onclick="confirmDelete(<?= $form['id'] ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="d-flex justify-content-center mt-4">
                <nav>
                    <ul class="pagination">
                        <?php if ($currentPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $currentPage - 1 ?>">Anterior</a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($currentPage < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $currentPage + 1 ?>">Próxima</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="alert alert-info">
            Nenhum formulário encontrado. 
            <a href="/visitor-forms/create" class="alert-link">Criar novo formulário</a>
        </div>
    <?php endif; ?>
</div>

<!-- Modal de confirmação de exclusão -->
<div class="modal fade" id="deleteFormModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir o formulário "<span id="formTitle"></span>"?</p>
                <p class="text-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Esta ação não pode ser desfeita e todas as submissões serão excluídas.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Copiar URL do formulário
    document.querySelectorAll('.copy-url').forEach(button => {
        button.addEventListener('click', function() {
            const url = this.dataset.url;
            navigator.clipboard.writeText(url).then(() => {
                const icon = this.querySelector('i');
                icon.classList.remove('fa-copy');
                icon.classList.add('fa-check');
                setTimeout(() => {
                    icon.classList.remove('fa-check');
                    icon.classList.add('fa-copy');
                }, 2000);
            });
        });
    });

    // Modal de confirmação de exclusão
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteFormModal'));
    
    document.querySelectorAll('.delete-form').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const title = this.dataset.title;
            
            document.getElementById('formTitle').textContent = title;
            document.getElementById('deleteForm').action = `/visitor-forms/${id}`;
            
            deleteModal.show();
        });
    });
});
</script>

<?php require_once VIEWS_PATH . '/partials/footer.php'; ?>
