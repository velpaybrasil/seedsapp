<?php

use App\Core\View;

View::extends('layouts/app');
View::section('content'); ?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Configurações do Sistema</h1>
    
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-cogs me-1"></i>
                Gerenciar Configurações
            </div>
            <a href="<?= url('settings/system/create') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Nova Configuração
            </a>
        </div>
        <div class="card-body">
            <?php if (empty($categories)): ?>
                <div class="alert alert-info">
                    Nenhuma configuração encontrada. Clique no botão "Nova Configuração" para começar.
                </div>
            <?php else: ?>
                <div class="accordion" id="settingsAccordion">
                    <?php foreach ($categories as $category): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#category-<?= htmlspecialchars($category) ?>">
                                    <?= htmlspecialchars(ucfirst($category)) ?>
                                </button>
                            </h2>
                            <div id="category-<?= htmlspecialchars($category) ?>" class="accordion-collapse collapse" 
                                 data-bs-parent="#settingsAccordion">
                                <div class="accordion-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Chave</th>
                                                    <th>Valor</th>
                                                    <th>Tipo</th>
                                                    <th>Descrição</th>
                                                    <th>Público</th>
                                                    <th>Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($settings[$category] as $setting): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($setting['key_name']) ?></td>
                                                        <td><?= htmlspecialchars($setting['value']) ?></td>
                                                        <td><?= htmlspecialchars($setting['type']) ?></td>
                                                        <td><?= htmlspecialchars($setting['description']) ?></td>
                                                        <td>
                                                            <?php if ($setting['is_public']): ?>
                                                                <span class="badge bg-success">Sim</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-danger">Não</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group">
                                                                <a href="<?= url("settings/system/{$category}/{$setting['key_name']}/edit") ?>" 
                                                                   class="btn btn-sm btn-primary">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <button type="button" class="btn btn-sm btn-danger" 
                                                                        data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                                        data-category="<?= htmlspecialchars($category) ?>"
                                                                        data-key="<?= htmlspecialchars($setting['key_name']) ?>">
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
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir esta configuração?</p>
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
document.addEventListener('DOMContentLoaded', function() {
    const deleteModal = document.getElementById('deleteModal');
    const deleteForm = document.getElementById('deleteForm');
    
    deleteModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const category = button.getAttribute('data-category');
        const key = button.getAttribute('data-key');
        
        deleteForm.action = `<?= url('settings/system/') ?>${category}/${key}/delete`;
    });
});
</script>

<?php View::endSection(); ?>
