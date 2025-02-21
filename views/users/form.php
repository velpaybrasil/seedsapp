<?php
use App\Core\View;

View::extends('app');

$isEdit = isset($user);
$title = $isEdit ? 'Editar Usuário' : 'Novo Usuário';

View::section('content'); ?>
<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $title ?></h1>
    
    <?php View::renderPartial('partials/flash_messages') ?>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-user me-1"></i>
            <?= $title ?>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= View::url($isEdit ? "users/{$user['id']}/update" : 'users/store') ?>">
                <?= View::csrf() ?>
                <?php if ($isEdit): ?>
                    <input type="hidden" name="_method" value="PUT">
                <?php endif; ?>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nome *</label>
                        <input type="text" class="form-control" id="name" name="name" required
                               value="<?= $isEdit ? htmlspecialchars($user['name']) : View::old('name') ?>">
                    </div>
                    
                    <div class="col-md-6">
                        <label for="email" class="form-label">E-mail *</label>
                        <input type="email" class="form-control" id="email" name="email" required
                               value="<?= $isEdit ? htmlspecialchars($user['email']) : View::old('email') ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label"><?= $isEdit ? 'Nova Senha' : 'Senha *' ?></label>
                        <input type="password" class="form-control" id="password" name="password"
                               <?= !$isEdit ? 'required' : '' ?>>
                        <?php if ($isEdit): ?>
                            <div class="form-text">Deixe em branco para manter a senha atual</div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="roles" class="form-label">Papéis *</label>
                        <select class="form-select select2" id="roles" name="roles[]" multiple required>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= $role['id'] ?>" 
                                        <?= $isEdit && in_array($role['id'], $userRoles ?? []) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($role['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="active" name="active" value="1"
                               <?= (!$isEdit || ($isEdit && $user['active']) || View::old('active')) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="active">
                            Ativo
                        </label>
                    </div>
                </div>

                <?php if ($isEdit): ?>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="must_change_password" name="must_change_password" value="1"
                                   <?= $user['must_change_password'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="must_change_password">
                                Exigir alteração de senha no próximo login
                            </label>
                        </div>
                    </div>

                    <?php if (!empty($modules)): ?>
                        <div class="card mb-3">
                            <div class="card-header">
                                <i class="fas fa-lock me-1"></i>
                                Permissões de Acesso
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Módulo</th>
                                                <th class="text-center">Visualizar</th>
                                                <th class="text-center">Criar</th>
                                                <th class="text-center">Editar</th>
                                                <th class="text-center">Excluir</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($modules as $module): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($module['name']) ?></td>
                                                    <td class="text-center">
                                                        <div class="form-check d-flex justify-content-center">
                                                            <input class="form-check-input" type="checkbox" 
                                                                   name="permissions[]" 
                                                                   value="<?= $module['id'] ?>_view"
                                                                   <?= in_array($module['id'] . '_view', $userPermissions ?? []) ? 'checked' : '' ?>>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="form-check d-flex justify-content-center">
                                                            <input class="form-check-input" type="checkbox" 
                                                                   name="permissions[]" 
                                                                   value="<?= $module['id'] ?>_create"
                                                                   <?= in_array($module['id'] . '_create', $userPermissions ?? []) ? 'checked' : '' ?>>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="form-check d-flex justify-content-center">
                                                            <input class="form-check-input" type="checkbox" 
                                                                   name="permissions[]" 
                                                                   value="<?= $module['id'] ?>_edit"
                                                                   <?= in_array($module['id'] . '_edit', $userPermissions ?? []) ? 'checked' : '' ?>>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="form-check d-flex justify-content-center">
                                                            <input class="form-check-input" type="checkbox" 
                                                                   name="permissions[]" 
                                                                   value="<?= $module['id'] ?>_delete"
                                                                   <?= in_array($module['id'] . '_delete', $userPermissions ?? []) ? 'checked' : '' ?>>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <div class="d-flex justify-content-end">
                    <a href="<?= View::url('users') ?>" class="btn btn-secondary me-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php View::endSection(); ?>

<?php View::section('styles'); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
</style>
<?php View::endSection(); ?>

<?php View::section('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>
<?php View::endSection(); ?>
