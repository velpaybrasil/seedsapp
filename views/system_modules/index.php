<?php $this->layout('layouts/default', ['title' => 'Módulos do Sistema']) ?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Módulos do Sistema</h1>
    
    <?php $this->insert('partials/flash_messages') ?>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-cubes me-1"></i>
                Módulos
            </div>
            <?php if ($this->userHasPermission('settings', 'create')): ?>
            <a href="/system-modules/create" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Novo Módulo
            </a>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <?php if (empty($modules)): ?>
                <p class="text-center">Nenhum módulo cadastrado.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Slug</th>
                                <th>Descrição</th>
                                <th>Ordem</th>
                                <th>Status</th>
                                <th width="120">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($modules as $module): ?>
                                <tr>
                                    <td>
                                        <?php if ($module['icon']): ?>
                                            <i class="fas fa-<?= $this->escape($module['icon']) ?> me-1"></i>
                                        <?php endif; ?>
                                        <?= $this->escape($module['name']) ?>
                                    </td>
                                    <td><?= $this->escape($module['slug']) ?></td>
                                    <td><?= $this->escape($module['description']) ?></td>
                                    <td><?= $this->escape($module['order_index']) ?></td>
                                    <td>
                                        <?php if ($module['active']): ?>
                                            <span class="badge bg-success">Ativo</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inativo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($this->userHasPermission('settings', 'edit')): ?>
                                            <a href="/system-modules/<?= $module['id'] ?>/edit" class="btn btn-primary btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if ($this->userHasPermission('settings', 'delete')): ?>
                                            <button type="button" class="btn btn-danger btn-sm" title="Excluir"
                                                    onclick="confirmDelete(<?= $module['id'] ?>, '<?= $this->escape($module['name']) ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php if (!empty($module['children'])): ?>
                                    <?php foreach ($module['children'] as $child): ?>
                                        <tr>
                                            <td class="ps-4">
                                                <?php if ($child['icon']): ?>
                                                    <i class="fas fa-<?= $this->escape($child['icon']) ?> me-1"></i>
                                                <?php endif; ?>
                                                <?= $this->escape($child['name']) ?>
                                            </td>
                                            <td><?= $this->escape($child['slug']) ?></td>
                                            <td><?= $this->escape($child['description']) ?></td>
                                            <td><?= $this->escape($child['order_index']) ?></td>
                                            <td>
                                                <?php if ($child['active']): ?>
                                                    <span class="badge bg-success">Ativo</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Inativo</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($this->userHasPermission('settings', 'edit')): ?>
                                                    <a href="/system-modules/<?= $child['id'] ?>/edit" class="btn btn-primary btn-sm" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <?php if ($this->userHasPermission('settings', 'delete')): ?>
                                                    <button type="button" class="btn btn-danger btn-sm" title="Excluir"
                                                            onclick="confirmDelete(<?= $child['id'] ?>, '<?= $this->escape($child['name']) ?>')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" style="display: none;">
    <input type="hidden" name="_method" value="DELETE">
</form>

<script>
function confirmDelete(id, name) {
    if (confirm(`Deseja realmente excluir o módulo "${name}"?`)) {
        const form = document.getElementById('deleteForm');
        form.action = `/system-modules/${id}`;
        form.submit();
    }
}
</script>
