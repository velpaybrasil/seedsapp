<?php
use App\Core\View;
View::extends('app');
?>

<?php View::section('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0"><?= htmlspecialchars($group['name']) ?></h1>
                    <p class="text-muted mb-0">Detalhes do grupo e membros</p>
                </div>
                <div>
                    <a href="<?= url("groups/{$group['id']}/edit") ?>" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>
                        Editar Grupo
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Informações do Grupo -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informações Básicas
                    </h5>
                </div>
                <div class="card-body">
                    <p><strong>Descrição:</strong> <?= htmlspecialchars($group['description']) ?></p>
                    <p><strong>Ministério:</strong> <?= htmlspecialchars($group['ministry_name']) ?></p>
                    <p><strong>Dia:</strong> <?= htmlspecialchars($group['meeting_day']) ?></p>
                    <p><strong>Horário:</strong> <?= htmlspecialchars($group['meeting_time']) ?></p>
                    <p><strong>Endereço:</strong> <?= htmlspecialchars($group['meeting_address']) ?></p>
                    <p><strong>Bairro:</strong> <?= htmlspecialchars($group['neighborhood']) ?></p>
                    <p><strong>Capacidade:</strong> <?= htmlspecialchars($group['max_participants']) ?> participantes</p>
                    <p><strong>Status:</strong> <?= $group['status'] === 'active' ? '<span class="badge bg-success">Ativo</span>' : '<span class="badge bg-danger">Inativo</span>' ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-users me-2"></i>
                        Liderança
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($leaders)): ?>
                        <div class="list-group">
                            <?php foreach ($leaders as $leader): ?>
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1"><?= htmlspecialchars($leader['name']) ?></h6>
                                            <small class="text-muted">
                                                <?= $leader['role'] === 'leader' ? 'Líder' : 'Co-líder' ?>
                                            </small>
                                        </div>
                                        <?php if (!empty($leader['phone'])): ?>
                                            <a href="tel:<?= htmlspecialchars($leader['phone']) ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-phone"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">Nenhum líder definido</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Membros Pendentes -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-clock me-2"></i>
                        Pré-inscrições Pendentes
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($pendingMembers)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>E-mail</th>
                                        <th>Telefone</th>
                                        <th>Data da Inscrição</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendingMembers as $member): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($member['user_name']) ?></td>
                                            <td><?= htmlspecialchars($member['email']) ?></td>
                                            <td><?= htmlspecialchars($member['phone']) ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($member['created_at'])) ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" 
                                                            class="btn btn-sm btn-success approve-member" 
                                                            data-id="<?= $member['user_id'] ?>"
                                                            title="Aprovar">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger reject-member" 
                                                            data-id="<?= $member['user_id'] ?>"
                                                            title="Rejeitar">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">Nenhuma pré-inscrição pendente</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Membros Aprovados -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-users me-2"></i>
                        Membros Ativos
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($approvedMembers)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>E-mail</th>
                                        <th>Telefone</th>
                                        <th>Data de Entrada</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($approvedMembers as $member): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($member['user_name']) ?></td>
                                            <td><?= htmlspecialchars($member['email']) ?></td>
                                            <td><?= htmlspecialchars($member['phone']) ?></td>
                                            <td><?= date('d/m/Y', strtotime($member['joined_at'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">Nenhum membro ativo</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php View::endSection(); ?>

<?php View::section('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Aprovar membro
    document.querySelectorAll('.approve-member').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.id;
            const row = this.closest('tr');
            const userName = row.querySelector('td:first-child').textContent;

            Swal.fire({
                title: 'Confirmar aprovação',
                text: `Deseja aprovar ${userName} como membro do grupo?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, aprovar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `/groups/<?= $group['id'] ?>/members/${userId}/approve`;
                }
            });
        });
    });

    // Rejeitar membro
    document.querySelectorAll('.reject-member').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.id;
            const row = this.closest('tr');
            const userName = row.querySelector('td:first-child').textContent;

            Swal.fire({
                title: 'Confirmar rejeição',
                text: `Deseja rejeitar a inscrição de ${userName}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, rejeitar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `/groups/<?= $group['id'] ?>/members/${userId}/reject`;
                }
            });
        });
    });
});
</script>
<?php View::endSection(); ?>
