<?php
use App\Core\View;

View::extends('app');

View::section('styles'); ?>
<style>
    .stats-card {
        background-color: #fff;
        border-radius: 0.5rem;
        padding: 1.5rem;
        box-shadow: 0 0 10px rgba(0,0,0,.05);
        transition: transform 0.2s;
    }
    .stats-card:hover {
        transform: translateY(-5px);
    }
    .stats-icon {
        font-size: 2rem;
        opacity: 0.1;
        position: absolute;
        right: 1rem;
        top: 1rem;
    }
    .stats-value {
        font-size: 2rem;
        font-weight: 600;
        margin: 0;
    }
    .stats-label {
        color: #6c757d;
        margin: 0;
    }
    .role-badge {
        font-size: 0.8rem;
        padding: 0.3rem 0.6rem;
        border-radius: 50rem;
    }
    .user-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        margin-right: 0.5rem;
    }
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .animate-card {
        animation: fadeInUp 0.5s ease-in-out;
    }
</style>
<?php View::endSection(); ?>

<?php View::section('content'); ?>
<div class="content-spacing">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Usuários</h1>
                <div class="d-flex gap-2">
                    <?php if (hasPermission('admin')): ?>
                    <a href="<?= url('settings/system') ?>" class="btn btn-outline-primary">
                        <i class="fas fa-cogs me-2"></i>Configurações
                    </a>
                    <?php endif; ?>
                    <a href="<?= url('users/roles') ?>" class="btn btn-outline-primary">
                        <i class="fas fa-user-tag me-2"></i>Papéis
                    </a>
                    <a href="<?= url('users/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Novo Usuário
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <!-- Total de Usuários -->
        <div class="col-sm-6 col-md-6 col-xl-3">
            <div class="card h-100 border-0 bg-gradient animate-card" 
                 style="background-color: #4158D0;background-image: linear-gradient(43deg, #4158D0 0%, #C850C0 46%, #FFCC70 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="rounded-circle bg-white bg-opacity-25 p-2">
                            <i class="fas fa-users fa-lg text-white"></i>
                        </div>
                        <span class="badge bg-white bg-opacity-25 text-white">Total</span>
                    </div>
                    <h3 class="display-5 fw-bold text-white mb-1"><?= $stats['total'] ?></h3>
                    <p class="text-white text-opacity-75 mb-0">Usuários cadastrados</p>
                </div>
            </div>
        </div>

        <!-- Usuários Ativos -->
        <div class="col-sm-6 col-md-6 col-xl-3">
            <div class="card h-100 border-0 bg-gradient animate-card" 
                 style="background-color: #0093E9;background-image: linear-gradient(160deg, #0093E9 0%, #80D0C7 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="rounded-circle bg-white bg-opacity-25 p-2">
                            <i class="fas fa-user-check fa-lg text-white"></i>
                        </div>
                        <span class="badge bg-white bg-opacity-25 text-white">Ativos</span>
                    </div>
                    <h3 class="display-5 fw-bold text-white mb-1"><?= $stats['active'] ?></h3>
                    <p class="text-white text-opacity-75 mb-0">Usuários ativos</p>
                </div>
            </div>
        </div>

        <!-- Usuários Inativos -->
        <div class="col-sm-6 col-md-6 col-xl-3">
            <div class="card h-100 border-0 bg-gradient animate-card" 
                 style="background-color: #8EC5FC;background-image: linear-gradient(62deg, #8EC5FC 0%, #E0C3FC 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="rounded-circle bg-white bg-opacity-25 p-2">
                            <i class="fas fa-user-times fa-lg text-white"></i>
                        </div>
                        <span class="badge bg-white bg-opacity-25 text-white">Inativos</span>
                    </div>
                    <h3 class="display-5 fw-bold text-white mb-1"><?= $stats['inactive'] ?></h3>
                    <p class="text-white text-opacity-75 mb-0">Usuários inativos</p>
                </div>
            </div>
        </div>

        <!-- Papéis de Acesso -->
        <div class="col-sm-6 col-md-6 col-xl-3">
            <div class="card h-100 border-0 bg-gradient animate-card" 
                 style="background-color: #85FFBD;background-image: linear-gradient(45deg, #85FFBD 0%, #FFFB7D 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="rounded-circle bg-white bg-opacity-25 p-2">
                            <i class="fas fa-user-tag fa-lg text-white"></i>
                        </div>
                        <span class="badge bg-white bg-opacity-25 text-white">Papéis</span>
                    </div>
                    <h3 class="display-5 fw-bold text-white mb-1"><?= count($stats['roles']) ?></h3>
                    <p class="text-white text-opacity-75 mb-0">Papéis de acesso</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="row mb-4">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Últimos Registros</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php foreach ($stats['last_registered'] as $user): ?>
                        <div class="list-group-item">
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name=<?= urlencode($user['name']) ?>&background=random" 
                                     class="user-avatar" alt="<?= htmlspecialchars($user['name']) ?>">
                                <div>
                                    <h6 class="mb-0"><?= htmlspecialchars($user['name']) ?></h6>
                                    <small class="text-muted">
                                        <?= date('d/m/Y', strtotime($user['created_at'])) ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Distribuição de Papéis</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($stats['roles'] as $role): ?>
                        <div class="col-sm-6 col-md-4 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-primary rounded-circle p-2">
                                        <i class="fas fa-user-tag"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0"><?= htmlspecialchars($role['name']) ?></h6>
                                    <small class="text-muted">
                                        <?= count($role['users'] ?? []) ?> usuários
                                    </small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Lista de Usuários</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="usersTable">
                    <thead>
                        <tr>
                            <th>Usuário</th>
                            <th>Email</th>
                            <th>Papéis</th>
                            <th>Status</th>
                            <th>Último Acesso</th>
                            <th width="120">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($user['name']) ?>&background=random" 
                                         class="user-avatar" alt="<?= htmlspecialchars($user['name']) ?>">
                                    <div>
                                        <?= htmlspecialchars($user['name']) ?>
                                        <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                            <span class="badge bg-info ms-1">Você</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <?php foreach ($user['roles'] as $role): ?>
                                    <span class="badge bg-primary role-badge mb-1">
                                        <?= htmlspecialchars($role['name']) ?>
                                    </span>
                                <?php endforeach; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?= $user['active'] ? 'success' : 'danger' ?>">
                                    <?= $user['active'] ? 'Ativo' : 'Inativo' ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($user['last_login']): ?>
                                    <?= date('d/m/Y H:i', strtotime($user['last_login'])) ?>
                                <?php else: ?>
                                    <span class="text-muted">Nunca acessou</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?= url('users/' . $user['id'] . '/edit') ?>" 
                                       class="btn btn-sm btn-outline-primary"
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="deleteUser(<?= $user['id'] ?>)"
                                            title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
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
<?php View::endSection(); ?>

<?php View::section('scripts'); ?>
<script>
$(document).ready(function() {
    $('#usersTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json'
        },
        order: [[0, 'asc']],
        pageLength: 25
    });
});

function deleteUser(userId) {
    Swal.fire({
        title: 'Tem certeza?',
        text: "Esta ação não poderá ser revertida!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sim, excluir!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/users/${userId}`, {
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
                        'O usuário foi excluído com sucesso.',
                        'success'
                    ).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire(
                        'Erro!',
                        data.message || 'Ocorreu um erro ao excluir o usuário.',
                        'error'
                    );
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire(
                    'Erro!',
                    'Ocorreu um erro ao excluir o usuário.',
                    'error'
                );
            });
        }
    });
}
</script>
<?php View::endSection(); ?>
