<div class="container-fluid">
    <!-- Page Title -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Voluntários</h1>
        <div>
            <a href="/seedsapp/volunteers/schedule" class="btn btn-primary me-2">
                <i class="bi bi-calendar-plus"></i> Nova Escala
            </a>
            <a href="/seedsapp/volunteers/create" class="btn btn-success">
                <i class="bi bi-person-plus"></i> Novo Voluntário
            </a>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Volunteers List -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Lista de Voluntários</h6>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Filtrar por Ministério
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/seedsapp/volunteers">Todos</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/seedsapp/volunteers?ministry=música">Música</a></li>
                            <li><a class="dropdown-item" href="/seedsapp/volunteers?ministry=mídia">Mídia</a></li>
                            <li><a class="dropdown-item" href="/seedsapp/volunteers?ministry=recepção">Recepção</a></li>
                            <li><a class="dropdown-item" href="/seedsapp/volunteers?ministry=infantil">Infantil</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="volunteersTable">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Ministério</th>
                                    <th>E-mail</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($volunteers as $volunteer): ?>
                                <tr>
                                    <td><?= htmlspecialchars($volunteer['name']) ?></td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?= htmlspecialchars($volunteer['ministry']) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($volunteer['email']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $volunteer['active'] ? 'success' : 'danger' ?>">
                                            <?= $volunteer['active'] ? 'Ativo' : 'Inativo' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="/seedsapp/volunteers/edit/<?= $volunteer['id'] ?>" 
                                               class="btn btn-sm btn-primary"
                                               data-bs-toggle="tooltip"
                                               title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-sm btn-info"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#scheduleModal"
                                                    data-volunteer-id="<?= $volunteer['id'] ?>"
                                                    title="Ver Escalas">
                                                <i class="bi bi-calendar3"></i>
                                            </button>
                                            <button type="button"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="toggleVolunteerStatus(<?= $volunteer['id'] ?>)"
                                                    data-bs-toggle="tooltip"
                                                    title="<?= $volunteer['active'] ? 'Desativar' : 'Ativar' ?>">
                                                <i class="bi bi-<?= $volunteer['active'] ? 'person-x' : 'person-check' ?>"></i>
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

        <!-- Upcoming Schedules -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Próximas Escalas</h6>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <?php foreach ($upcomingSchedules as $schedule): ?>
                        <div class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1"><?= htmlspecialchars($schedule['volunteer_name']) ?></h6>
                                <small class="text-muted">
                                    <?= (new DateTime($schedule['event_date']))->format('d/m/Y') ?>
                                </small>
                            </div>
                            <p class="mb-1">
                                <?= htmlspecialchars($schedule['activity']) ?>
                                <span class="badge bg-<?= match($schedule['status']) {
                                    'scheduled' => 'warning',
                                    'confirmed' => 'success',
                                    'completed' => 'info',
                                    default => 'secondary'
                                } ?>">
                                    <?= match($schedule['status']) {
                                        'scheduled' => 'Agendado',
                                        'confirmed' => 'Confirmado',
                                        'completed' => 'Concluído',
                                        default => 'Indefinido'
                                    } ?>
                                </span>
                            </p>
                            <small class="text-muted">
                                <i class="bi bi-clock"></i> <?= (new DateTime($schedule['event_time']))->format('H:i') ?>
                                - <?= htmlspecialchars($schedule['ministry']) ?>
                            </small>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Schedule Modal -->
<div class="modal fade" id="scheduleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Escalas do Voluntário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="scheduleContent">
                    Carregando...
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DataTables -->
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
// Initialize DataTable
$(document).ready(function() {
    $('#volunteersTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json'
        },
        order: [[0, 'asc']],
        pageLength: 10
    });
});

// Toggle volunteer status
async function toggleVolunteerStatus(volunteerId) {
    if (!confirm('Deseja alterar o status deste voluntário?')) {
        return;
    }
    
    try {
        showLoading();
        const response = await fetch(`/seedsapp/volunteers/${volunteerId}/toggle-status`, {
            method: 'POST'
        });
        
        if (response.ok) {
            location.reload();
        } else {
            throw new Error('Erro ao alterar status');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Ocorreu um erro ao alterar o status do voluntário');
    } finally {
        hideLoading();
    }
}

// Load volunteer schedules
$('#scheduleModal').on('show.bs.modal', async function(event) {
    const button = $(event.relatedTarget);
    const volunteerId = button.data('volunteer-id');
    const modal = $(this);
    
    try {
        const response = await fetch(`/seedsapp/volunteers/${volunteerId}/schedules`);
        const data = await response.json();
        
        let html = `
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Horário</th>
                            <th>Atividade</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        data.forEach(schedule => {
            const date = new Date(schedule.event_date).toLocaleDateString('pt-BR');
            const time = new Date(`2000-01-01T${schedule.event_time}`).toLocaleTimeString('pt-BR', {
                hour: '2-digit',
                minute: '2-digit'
            });
            
            html += `
                <tr>
                    <td>${date}</td>
                    <td>${time}</td>
                    <td>${schedule.activity}</td>
                    <td>
                        <span class="badge bg-${
                            schedule.status === 'scheduled' ? 'warning' :
                            schedule.status === 'confirmed' ? 'success' :
                            schedule.status === 'completed' ? 'info' : 'secondary'
                        }">
                            ${
                                schedule.status === 'scheduled' ? 'Agendado' :
                                schedule.status === 'confirmed' ? 'Confirmado' :
                                schedule.status === 'completed' ? 'Concluído' : 'Indefinido'
                            }
                        </span>
                    </td>
                    <td>
                        <div class="btn-group">
                            <button type="button"
                                    class="btn btn-sm btn-success"
                                    onclick="updateScheduleStatus(${schedule.id}, 'confirmed')"
                                    ${schedule.status !== 'scheduled' ? 'disabled' : ''}>
                                <i class="bi bi-check-lg"></i>
                            </button>
                            <button type="button"
                                    class="btn btn-sm btn-info"
                                    onclick="updateScheduleStatus(${schedule.id}, 'completed')"
                                    ${schedule.status !== 'confirmed' ? 'disabled' : ''}>
                                <i class="bi bi-flag"></i>
                            </button>
                            <button type="button"
                                    class="btn btn-sm btn-danger"
                                    onclick="updateScheduleStatus(${schedule.id}, 'cancelled')"
                                    ${['completed', 'cancelled'].includes(schedule.status) ? 'disabled' : ''}>
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
        
        html += `
                    </tbody>
                </table>
            </div>
        `;
        
        modal.find('#scheduleContent').html(html);
    } catch (error) {
        console.error('Error:', error);
        modal.find('#scheduleContent').html('Erro ao carregar as escalas');
    }
});

// Update schedule status
async function updateScheduleStatus(scheduleId, status) {
    try {
        showLoading();
        const response = await fetch(`/seedsapp/volunteers/schedules/${scheduleId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ status })
        });
        
        if (response.ok) {
            location.reload();
        } else {
            throw new Error('Erro ao atualizar status');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Ocorreu um erro ao atualizar o status da escala');
    } finally {
        hideLoading();
    }
}
</script>
