<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Detalhes do Grupo</h1>
    
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-users me-1"></i>
                <?= isset($group['name']) ? htmlspecialchars($group['name']) : '' ?>
            </div>
            <div>
                <a href="/gcmanager/groups/<?= $group['id'] ?>/edit" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit me-1"></i> Editar
                </a>
                <a href="/gcmanager/groups" class="btn btn-secondary btn-sm ms-2">
                    <i class="fas fa-arrow-left me-1"></i> Voltar
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="card-title mb-4">Informações Gerais</h5>
                    <table class="table table-borderless">
                        <tr>
                            <th style="width: 150px;">Status:</th>
                            <td>
                                <span class="badge <?= isset($group['active']) && $group['active'] ? 'bg-success' : 'bg-danger' ?>">
                                    <?= isset($group['active']) && $group['active'] ? 'Ativo' : 'Inativo' ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Líder:</th>
                            <td><?= isset($group['leader_name']) ? htmlspecialchars($group['leader_name']) : 'Não definido' ?></td>
                        </tr>
                        <?php if (isset($group['co_leader_name']) && $group['co_leader_name']): ?>
                        <tr>
                            <th>Co-líder:</th>
                            <td><?= htmlspecialchars($group['co_leader_name']) ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <th>Dia/Horário:</th>
                            <td>
                                <?= isset($group['meeting_day']) ? htmlspecialchars($group['meeting_day']) : '' ?>
                                às
                                <?= isset($group['meeting_time']) ? htmlspecialchars($group['meeting_time']) : '' ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Local:</th>
                            <td><?= isset($group['address']) ? htmlspecialchars($group['address']) : '' ?></td>
                        </tr>
                        <tr>
                            <th>Máx. Participantes:</th>
                            <td><?= isset($group['max_participants']) && $group['max_participants'] > 0 ? htmlspecialchars($group['max_participants']) : 'Sem limite' ?></td>
                        </tr>
                    </table>
                </div>
                
                <div class="col-md-6">
                    <h5 class="card-title mb-4">Descrição</h5>
                    <div class="card bg-light">
                        <div class="card-body">
                            <?php if (isset($group['description']) && $group['description']): ?>
                                <?= nl2br(htmlspecialchars($group['description'])) ?>
                            <?php else: ?>
                                <p class="text-muted mb-0">Nenhuma descrição disponível.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
