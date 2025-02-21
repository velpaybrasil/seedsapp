<?php
use App\Core\View;

View::extends('app');

View::section('content'); ?>
<div class="content-spacing">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Papéis de Usuário</h1>
                <a href="<?= View::url('users/roles/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Novo Papel
                </a>
            </div>
        </div>
    </div>

    <!-- Roles List -->
    <div class="row">
        <?php foreach ($roles as $role): ?>
        <div class="col-md-6 col-xl-4 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <?= htmlspecialchars($role['name']) ?>
                        <?php if ($role['is_system']): ?>
                            <span class="badge bg-info ms-2">Sistema</span>
                        <?php endif; ?>
                    </h5>
                    <?php if (!$role['is_system']): ?>
                    <div class="dropdown">
                        <button class="btn btn-link text-muted" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="<?= View::url("users/roles/{$role['id']}/edit") ?>">
                                    <i class="fas fa-edit me-2"></i>Editar
                                </a>
                            </li>
                            <li>
                                <button class="dropdown-item text-danger"
                                        onclick="deleteRole(<?= $role['id'] ?>, '<?= htmlspecialchars($role['name']) ?>')">
                                    <i class="fas fa-trash me-2"></i>Excluir
                                </button>
                            </li>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if ($role['description']): ?>
                        <p class="text-muted mb-3"><?= htmlspecialchars($role['description']) ?></p>
                    <?php endif; ?>

                    <h6 class="fw-bold mb-3">Permissões</h6>
                    <?php 
                    $permissions = $role['permissions'] ?? [];
                    $modulePermissions = [];
                    foreach ($permissions as $perm) {
                        $modulePermissions[$perm['module_name']][] = $perm;
                    }
                    ?>

                    <?php if (empty($modulePermissions)): ?>
                        <p class="text-muted">Nenhuma permissão atribuída</p>
                    <?php else: ?>
                        <div class="accordion" id="accordion-<?= $role['id'] ?>">
                            <?php foreach ($modulePermissions as $moduleName => $perms): ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" 
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#module-<?= $role['id'] ?>-<?= md5($moduleName) ?>">
                                        <?= htmlspecialchars($moduleName) ?>
                                    </button>
                                </h2>
                                <div id="module-<?= $role['id'] ?>-<?= md5($moduleName) ?>" 
                                     class="accordion-collapse collapse">
                                    <div class="accordion-body">
                                        <ul class="list-unstyled mb-0">
                                            <?php foreach ($perms as $perm): ?>
                                            <li class="mb-2">
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                <?= htmlspecialchars($perm['name']) ?>
                                            </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-light">
                    <small class="text-muted">
                        <i class="fas fa-users me-1"></i>
                        <?= count($role['users'] ?? []) ?> usuários com este papel
                    </small>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php View::endSection(); ?>

<?php View::section('scripts'); ?>
<script>
function deleteRole(roleId, roleName) {
    Swal.fire({
        title: 'Confirmar exclusão',
        text: `Deseja realmente excluir o papel "${roleName}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sim, excluir',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/users/roles/${roleId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="csrf_token"]').value
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire(
                        'Excluído!',
                        'O papel foi excluído com sucesso.',
                        'success'
                    ).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire(
                        'Erro!',
                        data.message || 'Ocorreu um erro ao excluir o papel.',
                        'error'
                    );
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire(
                    'Erro!',
                    'Ocorreu um erro ao excluir o papel.',
                    'error'
                );
            });
        }
    });
}
</script>
<?php View::endSection(); ?>
