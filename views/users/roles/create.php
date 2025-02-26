<?php
use App\Core\View;

View::extends('layouts/app');

View::section('content'); ?>
<div class="content-spacing">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Novo Papel</h1>
                <a href="<?= View::url('users/roles') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Voltar
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="<?= View::url('users/roles') ?>" method="POST">
                        <?= View::csrf() ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nome do Papel</label>
                                    <input type="text" 
                                           class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                                           id="name" 
                                           name="name" 
                                           value="<?= old('name') ?>"
                                           required>
                                    <?php if (isset($errors['name'])): ?>
                                        <div class="invalid-feedback"><?= $errors['name'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Descrição</label>
                                    <input type="text" 
                                           class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>" 
                                           id="description" 
                                           name="description"
                                           value="<?= old('description') ?>">
                                    <?php if (isset($errors['description'])): ?>
                                        <div class="invalid-feedback"><?= $errors['description'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="active" 
                                       name="active" 
                                       value="1"
                                       <?= old('active', true) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="active">Ativo</label>
                            </div>
                        </div>

                        <h5 class="mb-3">Permissões</h5>

                        <?php foreach ($modules as $module): ?>
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="<?= htmlspecialchars($module['icon']) ?> me-2"></i>
                                    <?= htmlspecialchars($module['name']) ?>
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <?php 
                                    $modulePermissions = array_filter($permissions, function($p) use ($module) {
                                        return $p['module_id'] == $module['id'];
                                    });
                                    ?>
                                    <?php foreach ($modulePermissions as $permission): ?>
                                    <div class="col-md-6 col-lg-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   name="permissions[]" 
                                                   value="<?= $permission['id'] ?>" 
                                                   id="perm-<?= $permission['id'] ?>"
                                                   <?= in_array($permission['id'], old('permissions', [])) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="perm-<?= $permission['id'] ?>">
                                                <?= htmlspecialchars($permission['name']) ?>
                                                <?php if ($permission['description']): ?>
                                                <i class="fas fa-info-circle text-muted" 
                                                   data-bs-toggle="tooltip" 
                                                   title="<?= htmlspecialchars($permission['description']) ?>">
                                                </i>
                                                <?php endif; ?>
                                            </label>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Salvar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php View::endSection(); ?>

<?php View::section('scripts'); ?>
<script>
$(document).ready(function() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
<?php View::endSection(); ?>
