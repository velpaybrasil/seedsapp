<?php

use App\Core\View;

View::extends('app');

View::section('styles'); ?>
<style>
    .table th { 
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }
    .table td {
        vertical-align: middle;
    }
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
    }
    .btn-group .btn i {
        width: 16px;
        text-align: center;
    }
    .list-group-item-action:hover {
        background-color: #f8f9fa;
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
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Grupos de Crescimento</h1>
                    <p class="text-muted mb-0">Gerencie os grupos de crescimento da igreja</p>
                </div>
                <div>
                    <a href="<?= View::url('groups/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Novo Grupo
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <!-- Total de Grupos -->
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
                    <h3 class="display-5 fw-bold text-white mb-1"><?= count($groups) ?></h3>
                    <p class="text-white text-opacity-75 mb-0">Grupos cadastrados</p>
                </div>
            </div>
        </div>

        <!-- Grupos Ativos -->
        <div class="col-sm-6 col-md-6 col-xl-3">
            <div class="card h-100 border-0 bg-gradient animate-card" 
                 style="background-color: #0093E9;background-image: linear-gradient(160deg, #0093E9 0%, #80D0C7 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="rounded-circle bg-white bg-opacity-25 p-2">
                            <i class="fas fa-check-circle fa-lg text-white"></i>
                        </div>
                        <span class="badge bg-white bg-opacity-25 text-white">Ativos</span>
                    </div>
                    <h3 class="display-5 fw-bold text-white mb-1"><?= count(array_filter($groups, fn($g) => $g['status'] === 'active')) ?></h3>
                    <p class="text-white text-opacity-75 mb-0">Grupos em atividade</p>
                </div>
            </div>
        </div>

        <!-- Total de Líderes -->
        <div class="col-sm-6 col-md-6 col-xl-3">
            <div class="card h-100 border-0 bg-gradient animate-card" 
                 style="background-color: #8EC5FC;background-image: linear-gradient(62deg, #8EC5FC 0%, #E0C3FC 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="rounded-circle bg-white bg-opacity-25 p-2">
                            <i class="fas fa-user-tie fa-lg text-white"></i>
                        </div>
                        <span class="badge bg-white bg-opacity-25 text-white">Líderes</span>
                    </div>
                    <h3 class="display-5 fw-bold text-white mb-1" id="totalLeaders">-</h3>
                    <p class="text-white text-opacity-75 mb-0">Líderes ativos</p>
                </div>
            </div>
        </div>

        <!-- Bairros Alcançados -->
        <div class="col-sm-6 col-md-6 col-xl-3">
            <div class="card h-100 border-0 bg-gradient animate-card" 
                 style="background-color: #85FFBD;background-image: linear-gradient(45deg, #85FFBD 0%, #FFFB7D 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="rounded-circle bg-white bg-opacity-25 p-2">
                            <i class="fas fa-map-marker-alt fa-lg text-white"></i>
                        </div>
                        <span class="badge bg-white bg-opacity-25 text-white">Bairros</span>
                    </div>
                    <h3 class="display-5 fw-bold text-white mb-1"><?= count(array_unique(array_column($groups, 'neighborhood'))) ?></h3>
                    <p class="text-white text-opacity-75 mb-0">Áreas alcançadas</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Groups Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Lista de Grupos</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Ministério</th>
                                    <th>Líder</th>
                                    <th>Bairro</th>
                                    <th>Dia/Horário</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($groups as $group): ?>
                                    <tr>
                                        <td>
                                            <a href="#" 
                                               class="text-decoration-none"
                                               data-group-id="<?= $group['id'] ?>"
                                               onclick="showGroupDetails(<?= View::escape(json_encode($group)) ?>)">
                                                <?= View::escape($group['name']) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <?php if ($group['ministry_id']): ?>
                                                <?php 
                                                $ministry = array_filter($ministries, fn($m) => $m['id'] == $group['ministry_id']);
                                                $ministry = reset($ministry);
                                                ?>
                                                <span class="badge-ministry">
                                                    <?= View::escape($ministry['name']) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $group['leaders'] ?: 'Sem líder' ?></td>
                                        <td><?= View::escape($group['neighborhood']) ?></td>
                                        <td>
                                            <?= View::escape($group['meeting_day']) ?> às <?= View::escape($group['meeting_time']) ?>
                                        </td>
                                        <td>
                                            <?php if ($group['status'] === 'active'): ?>
                                                <span class="badge bg-success">Ativo</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Inativo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?= View::url('groups/' . $group['id'] . '/edit') ?>" 
                                                   class="btn btn-outline-primary"
                                                   title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-outline-danger"
                                                        onclick="deleteGroup(<?= $group['id'] ?>)"
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
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Group Details Modal -->
<?php View::renderPartial('groups/partials/group_details_modal') ?>

<?php View::endSection(); ?>

<?php View::section('scripts'); ?>
<script>
function showGroupDetails(group) {
    const modal = new bootstrap.Modal(document.getElementById('groupDetailsModal'));
    
    // Update modal content
    document.getElementById('modalGroupName').textContent = group.name;
    document.getElementById('modalGroupDescription').textContent = group.description || 'Nenhuma descrição disponível';
    document.getElementById('modalGroupLeader').textContent = group.leader_name;
    document.getElementById('modalGroupWeekday').textContent = `${group.meeting_day} às ${group.meeting_time}`;
    document.getElementById('modalGroupAddress').textContent = group.meeting_address;
    document.getElementById('modalGroupNeighborhood').textContent = group.neighborhood;
    document.getElementById('modalGroupCity').textContent = group.city;
    document.getElementById('modalGroupCapacity').textContent = group.max_participants;
    
    // Show modal
    modal.show();
}

function deleteGroup(groupId) {
    const token = document.querySelector('input[name="_token"]').value;
    if (!token) {
        Swal.fire('Erro!', 'Token de segurança não encontrado. Recarregue a página.', 'error');
        return;
    }

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
            // Send delete request
            fetch(`/groups/${groupId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire(
                        'Excluído!',
                        'O grupo foi excluído com sucesso.',
                        'success'
                    ).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire(
                        'Erro!',
                        data.message || 'Ocorreu um erro ao excluir o grupo.',
                        'error'
                    );
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire(
                    'Erro!',
                    'Ocorreu um erro ao excluir o grupo.',
                    'error'
                );
            });
        }
    });
}

// Calculate total leaders
document.addEventListener('DOMContentLoaded', function() {
    const uniqueLeaders = new Set();
    <?php foreach ($groups as $group): ?>
        if (<?= json_encode($group['leader_name'] !== 'Sem líder') ?>) {
            uniqueLeaders.add('<?= $group['leader_name'] ?>');
        }
    <?php endforeach; ?>
    document.getElementById('totalLeaders').textContent = uniqueLeaders.size;
});
</script>
<?php View::endSection(); ?>
