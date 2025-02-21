<?php require_once __DIR__ . '/../includes/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= htmlspecialchars($group['name']) ?></h1>
        <div class="btn-group">
            <a href="/groups/<?= $group['id'] ?>/edit" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Editar
            </a>
            <a href="/groups" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Informações do Grupo -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informações Gerais</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-muted">Descrição</h6>
                        <p><?= htmlspecialchars($group['description'] ?? 'Não informada') ?></p>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted">Dia da Reunião</h6>
                            <p><?= ucfirst(htmlspecialchars(str_replace(['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'], 
                                ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'], 
                                $group['meeting_day']))) ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Horário</h6>
                            <p><?= date('H:i', strtotime($group['meeting_time'])) ?></p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-muted">Endereço</h6>
                        <p><?= htmlspecialchars($group['address']) ?></p>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted">Líder</h6>
                            <p><?= htmlspecialchars($group['leader_name']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Co-líder</h6>
                            <p><?= htmlspecialchars($group['co_leader_name'] ?? 'Não definido') ?></p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Participantes</h6>
                            <p><?= count($participants) ?>/<?= $group['max_participants'] ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Status</h6>
                            <span class="badge bg-<?= $group['active'] ? 'success' : 'secondary' ?>">
                                <?= $group['active'] ? 'Ativo' : 'Inativo' ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Participantes -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Participantes</h5>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addParticipantModal">
                        <i class="bi bi-plus-circle"></i> Adicionar
                    </button>
                </div>
                <div class="card-body">
                    <?php if (empty($participants)): ?>
                        <p class="text-muted text-center py-4">Nenhum participante cadastrado.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th class="text-center">Data de Entrada</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($participants as $participant): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($participant['name']) ?></td>
                                            <td class="text-center">
                                                <?= date('d/m/Y', strtotime($participant['join_date'])) ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-<?= $participant['active'] ? 'success' : 'secondary' ?>">
                                                    <?= $participant['active'] ? 'Ativo' : 'Inativo' ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-sm" 
                                                    onclick="confirmRemoveParticipant(<?= $participant['id'] ?>)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Registro de Presença -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Registro de Presença</h5>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#attendanceModal">
                <i class="bi bi-calendar-plus"></i> Nova Reunião
            </button>
        </div>
        <div class="card-body">
            <?php if (empty($attendance)): ?>
                <p class="text-muted text-center py-4">Nenhuma reunião registrada.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th class="text-center">Presentes</th>
                                <th>Tópico</th>
                                <th>Observações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($attendance as $meeting): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($meeting['meeting_date'])) ?></td>
                                    <td class="text-center">
                                        <?= $meeting['present_count'] ?>/<?= $meeting['total_participants'] ?>
                                    </td>
                                    <td><?= htmlspecialchars($meeting['topic'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($meeting['notes'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Adicionar Participante -->
<div class="modal fade" id="addParticipantModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adicionar Participante</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="/groups/<?= $group['id'] ?>/participants" method="POST">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label class="form-label" for="user_id">Usuário</label>
                        <select name="user_id" id="user_id" required class="form-select">
                            <option value="">Selecione...</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="join_date">Data de Entrada</label>
                        <input type="date" name="join_date" id="join_date" required
                            value="<?= date('Y-m-d') ?>"
                            class="form-control">
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Adicionar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Registrar Presença -->
<div class="modal fade" id="attendanceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Reunião</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="/groups/<?= $group['id'] ?>/attendance" method="POST">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label class="form-label" for="meeting_date">Data da Reunião</label>
                        <input type="date" name="meeting_date" id="meeting_date" required
                            value="<?= date('Y-m-d') ?>"
                            class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Participantes Presentes</label>
                        <?php foreach ($participants as $participant): ?>
                            <?php if ($participant['active']): ?>
                                <div class="form-check">
                                    <input type="checkbox" name="participants[<?= $participant['id'] ?>]" 
                                        value="1" class="form-check-input" id="participant_<?= $participant['id'] ?>">
                                    <label class="form-check-label" for="participant_<?= $participant['id'] ?>">
                                        <?= htmlspecialchars($participant['name']) ?>
                                    </label>
                                    <input type="text" name="notes[<?= $participant['id'] ?>]" 
                                        class="form-control form-control-sm mt-1"
                                        placeholder="Observações (opcional)">
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save"></i> Salvar Registro
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Confirmação de Remoção -->
<div class="modal fade" id="removeParticipantModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Remoção</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja remover este participante do grupo?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="removeParticipantForm" action="" method="POST" class="d-inline">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Remover
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmRemoveParticipant(participantId) {
    const modal = new bootstrap.Modal(document.getElementById('removeParticipantModal'));
    const form = document.getElementById('removeParticipantForm');
    form.action = `/groups/<?= $group['id'] ?>/participants/${participantId}/remove`;
    modal.show();
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
