<?php
$title = 'Visualizar Grupo';
?>

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
                        <h6 class="text-muted">Bairro Principal</h6>
                        <p><?= htmlspecialchars($group['neighborhood'] ?? 'Não informado') ?></p>
                    </div>

                    <?php if (!empty($group['extra_neighborhoods'])): ?>
                    <div class="mb-3">
                        <h6 class="text-muted">Bairros Extras</h6>
                        <p><?= htmlspecialchars($group['extra_neighborhoods']) ?></p>
                    </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <h6 class="text-muted">Endereço Principal</h6>
                        <p><?= htmlspecialchars($group['meeting_address'] ?? 'Não informado') ?></p>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted">Líder</h6>
                            <p><?= htmlspecialchars($group['leader_name'] ?? 'Não definido') ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Co-líder</h6>
                            <p><?= htmlspecialchars($group['co_leader_name'] ?? 'Não definido') ?></p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Participantes</h6>
                            <p><?= count($members ?? []) ?>/<?= $group['capacity'] ?? 12 ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Status</h6>
                            <span class="badge bg-<?= ($group['active'] ?? false) ? 'success' : 'secondary' ?>">
                                <?= ($group['active'] ?? false) ? 'Ativo' : 'Inativo' ?>
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
                    <?php if (empty($members)): ?>
                        <p class="text-muted text-center py-4">Nenhum participante cadastrado.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Telefone</th>
                                        <th class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($members as $member): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($member['name']) ?></td>
                                            <td><?= htmlspecialchars($member['email'] ?? 'Não informado') ?></td>
                                            <td><?= htmlspecialchars($member['phone'] ?? 'Não informado') ?></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-sm" 
                                                        onclick="confirmRemoveMember(<?= $member['id'] ?>, '<?= htmlspecialchars($member['name']) ?>')">
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
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addMeetingModal">
                <i class="bi bi-plus-circle"></i> Nova Reunião
            </button>
        </div>
        <div class="card-body">
            <?php if (empty($meetings)): ?>
                <p class="text-muted">Nenhuma reunião registrada.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Tema</th>
                                <th>Presentes</th>
                                <th>Taxa de Presença</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($meetings as $meeting): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($meeting['meeting_date'])) ?></td>
                                    <td><?= htmlspecialchars($meeting['topic'] ?? 'Não informado') ?></td>
                                    <td><?= $meeting['total_attendees'] ?>/<?= $meeting['total_participants'] ?></td>
                                    <td>
                                        <?php 
                                        $attendanceRate = $meeting['total_participants'] > 0 
                                            ? ($meeting['total_attendees'] / $meeting['total_participants']) * 100 
                                            : 0;
                                        ?>
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" 
                                                style="width: <?= $attendanceRate ?>%"
                                                aria-valuenow="<?= $attendanceRate ?>" 
                                                aria-valuemin="0" 
                                                aria-valuemax="100">
                                                <?= number_format($attendanceRate, 1) ?>%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info" 
                                                onclick="showAttendanceModal(<?= $meeting['id'] ?>)">
                                            <i class="bi bi-list-check"></i> Presenças
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

    <!-- Modal Adicionar Participante -->
    <div class="modal fade" id="addParticipantModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Adicionar Participante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addParticipantForm" action="<?= url("/groups/{$group['id']}/participants") ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <div class="mb-3">
                            <label for="participant_type" class="form-label">Tipo de Participante</label>
                            <select class="form-select" id="participant_type" name="participant_type" required>
                                <option value="">Selecione...</option>
                                <option value="visitor">Visitante</option>
                                <option value="member">Membro</option>
                            </select>
                        </div>

                        <div id="visitor_select_div" class="mb-3" style="display: none;">
                            <label for="visitor_id" class="form-label">Selecione o Visitante</label>
                            <select class="form-select" id="visitor_id" name="visitor_id">
                                <option value="">Selecione um visitante...</option>
                                <?php if (!empty($visitors)): ?>
                                    <?php foreach ($visitors as $visitor): ?>
                                        <option value="<?= $visitor['id'] ?>">
                                            <?= htmlspecialchars($visitor['name']) ?> 
                                            <?php if (!empty($visitor['phone'])): ?>
                                                - <?= htmlspecialchars($visitor['phone']) ?>
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div id="member_select_div" class="mb-3" style="display: none;">
                            <label for="member_id" class="form-label">Selecione o Membro</label>
                            <select class="form-select" id="member_id" name="member_id">
                                <option value="">Selecione um membro...</option>
                                <?php if (!empty($members)): ?>
                                    <?php foreach ($members as $member): ?>
                                        <option value="<?= $member['id'] ?>">
                                            <?= htmlspecialchars($member['name']) ?>
                                            <?php if (!empty($member['phone'])): ?>
                                                - <?= htmlspecialchars($member['phone']) ?>
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Adicionar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const participantType = document.getElementById('participant_type');
        const visitorDiv = document.getElementById('visitor_select_div');
        const memberDiv = document.getElementById('member_select_div');
        const visitorSelect = document.getElementById('visitor_id');
        const memberSelect = document.getElementById('member_id');

        participantType.addEventListener('change', function() {
            visitorDiv.style.display = 'none';
            memberDiv.style.display = 'none';
            visitorSelect.required = false;
            memberSelect.required = false;

            if (this.value === 'visitor') {
                visitorDiv.style.display = 'block';
                visitorSelect.required = true;
            } else if (this.value === 'member') {
                memberDiv.style.display = 'block';
                memberSelect.required = true;
            }
        });

        // Inicializar Select2 para melhor usabilidade
        if (typeof $.fn.select2 !== 'undefined') {
            $('#visitor_id, #member_id').select2({
                dropdownParent: $('#addParticipantModal'),
                width: '100%',
                placeholder: 'Digite para buscar...',
                allowClear: true
            });
        }
    });
    </script>

    <!-- Modal Nova Reunião -->
    <div class="modal fade" id="addMeetingModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nova Reunião</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="/groups/<?= $group['id'] ?>/meetings" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="meeting_date" class="form-label">Data da Reunião</label>
                            <input type="date" class="form-control" id="meeting_date" name="meeting_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="topic" class="form-label">Tema</label>
                            <input type="text" class="form-control" id="topic" name="topic">
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Observações</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Registro de Presença -->
    <div class="modal fade" id="attendanceModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registro de Presença</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="attendanceForm">
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Participante</th>
                                        <th>Presente</th>
                                        <th>Observações</th>
                                    </tr>
                                </thead>
                                <tbody id="attendanceList">
                                    <!-- Preenchido via JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
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
    function confirmRemoveMember(participantId, participantName) {
        const modal = new bootstrap.Modal(document.getElementById('removeParticipantModal'));
        const form = document.getElementById('removeParticipantForm');
        form.action = `/groups/<?= $group['id'] ?>/participants/${participantId}/remove`;
        modal.show();
    }

    async function showAttendanceModal(meetingId) {
        try {
            const response = await fetch(`/groups/meetings/${meetingId}/attendance`);
            const attendance = await response.json();
            
            if (!attendance.success) {
                throw new Error(attendance.message);
            }

            const tbody = document.getElementById('attendanceList');
            tbody.innerHTML = '';

            attendance.data.forEach(participant => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${participant.visitor_name}</td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" 
                                   name="attendance[${participant.visitor_id}][present]"
                                   ${participant.present ? 'checked' : ''}>
                        </div>
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm" 
                               name="attendance[${participant.visitor_id}][notes]"
                               value="${participant.notes || ''}">
                    </td>
                `;
                tbody.appendChild(tr);
            });

            const modal = new bootstrap.Modal(document.getElementById('attendanceModal'));
            modal.show();

            // Configurar o formulário
            const form = document.getElementById('attendanceForm');
            form.onsubmit = async (e) => {
                e.preventDefault();
                
                const formData = new FormData(form);
                const data = {};
                
                for (const [key, value] of formData.entries()) {
                    const matches = key.match(/attendance\[(\d+)\]\[(\w+)\]/);
                    if (matches) {
                        const [, id, field] = matches;
                        if (!data[id]) data[id] = {};
                        data[id][field] = value;
                    }
                }

                try {
                    const response = await fetch(`/groups/meetings/${meetingId}/attendance`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ attendance: Object.values(data) })
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        location.reload();
                    } else {
                        throw new Error(result.message);
                    }
                } catch (error) {
                    alert('Erro ao salvar presenças: ' + error.message);
                }
            };
        } catch (error) {
            alert('Erro ao carregar presenças: ' + error.message);
        }
    }
    </script>
