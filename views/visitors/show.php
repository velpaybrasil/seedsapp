<?php
$title = 'Detalhes do Visitante';

$styles = '
<style>
.timeline {
    position: relative;
    padding: 20px 0;
}
.timeline-item {
    position: relative;
    padding-left: 40px;
    margin-bottom: 20px;
}
.timeline-marker {
    position: absolute;
    left: 0;
    top: 0;
    width: 15px;
    height: 15px;
    border-radius: 50%;
    background: #007bff;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #007bff;
}
.timeline-item:before {
    content: "";
    position: absolute;
    left: 7px;
    top: 15px;
    height: 100%;
    width: 2px;
    background: #007bff;
}
.timeline-item:last-child:before {
    display: none;
}
.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 4px;
}
</style>
';

// Validar se o visitante existe e tem os dados necessários
if (!isset($visitor) || !is_array($visitor) || empty($visitor['id'])) {
    error_log("[View/show] Dados do visitante inválidos ou ausentes: " . print_r($visitor ?? null, true));
    redirect(url('/visitors'));
    return;
}

error_log("[View/show] Iniciando renderização da view para visitante ID: " . $visitor['id']);
?>

<div class="container-fluid">
    <!-- Page Title -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0"><?= isset($visitor['name']) ? htmlspecialchars($visitor['name']) : 'Visitante' ?></h1>
        <div>
            <a href="<?= url('/visitors/' . $visitor['id'] . '/edit') ?>" class="btn btn-primary me-2">
                <i class="fas fa-edit me-2"></i>Editar
            </a>
            <a href="<?= url('/visitors') ?>" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Voltar
            </a>
        </div>
    </div>

    <?php error_log("[View/show] Renderizando informações principais..."); ?>
    
    <div class="row">
        <!-- Main Information -->
        <div class="col-lg-8">
            <!-- Personal Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3">
                    <h5 class="card-title mb-0">Informações Pessoais</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Nome Completo</label>
                            <p class="mb-0"><?= isset($visitor['name']) ? htmlspecialchars($visitor['name']) : '-' ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Data de Nascimento</label>
                            <p class="mb-0"><?= isset($visitor['birth_date']) ? date('d/m/Y', strtotime($visitor['birth_date'])) : '-' ?></p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Telefone</label>
                            <p class="mb-0">
                                <?php if (isset($visitor['phone']) && $visitor['phone']): ?>
                                    <div class="d-flex align-items-center">
                                        <a href="tel:<?= htmlspecialchars($visitor['phone']) ?>" class="text-decoration-none me-2">
                                            <i class="fas fa-phone-alt me-1"></i>
                                            <?= htmlspecialchars($visitor['phone']) ?>
                                        </a>
                                        <a href="https://wa.me/<?= formatPhoneForWhatsApp($visitor['phone']) ?>" 
                                           target="_blank" 
                                           class="btn btn-success btn-sm" 
                                           title="Abrir WhatsApp">
                                            <i class="fab fa-whatsapp"></i>
                                        </a>
                                    </div>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Email</label>
                            <p class="mb-0">
                                <?php if (isset($visitor['email']) && $visitor['email']): ?>
                                    <a href="mailto:<?= htmlspecialchars($visitor['email']) ?>" class="text-decoration-none">
                                        <i class="fas fa-envelope me-1"></i>
                                        <?= htmlspecialchars($visitor['email']) ?>
                                    </a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address -->
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3">
                    <h5 class="card-title mb-0">Endereço</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label text-muted">Endereço Completo</label>
                            <p class="mb-0"><?= isset($visitor['address']) ? htmlspecialchars($visitor['address']) : '-' ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Bairro</label>
                            <p class="mb-0"><?= isset($visitor['neighborhood']) ? htmlspecialchars($visitor['neighborhood']) : '-' ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Church Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3">
                    <h5 class="card-title mb-0">Informações da Igreja</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Como Conheceu a Igreja</label>
                            <p class="mb-0"><?= isset($visitor['how_knew_church']) ? htmlspecialchars($visitor['how_knew_church']) : '-' ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Data da Primeira Visita</label>
                            <p class="mb-0"><?= isset($visitor['first_visit_date']) ? date('d/m/Y', strtotime($visitor['first_visit_date'])) : '-' ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Interesse em Grupo</label>
                            <p class="mb-0"><?= isset($visitor['wants_group']) && $visitor['wants_group'] ? 'Sim' : 'Não' ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Dias Disponíveis</label>
                            <p class="mb-0">
                                <?php
                                if (!empty($visitor['available_days'])) {
                                    $days = explode(',', $visitor['available_days']);
                                    $dayTranslations = [
                                        'monday' => 'Segunda',
                                        'tuesday' => 'Terça',
                                        'wednesday' => 'Quarta',
                                        'thursday' => 'Quinta',
                                        'friday' => 'Sexta',
                                        'saturday' => 'Sábado',
                                        'sunday' => 'Domingo'
                                    ];
                                    foreach ($days as $day) {
                                        echo '<span class="badge bg-secondary me-1">' . ($dayTranslations[$day] ?? $day) . '</span>';
                                    }
                                } else {
                                    echo '-';
                                }
                                ?>
                            </p>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted">Pedidos de Oração</label>
                            <p class="mb-0"><?= isset($visitor['prayer_requests']) ? nl2br(htmlspecialchars($visitor['prayer_requests'])) : '-' ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Profile Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <?php if (isset($visitor['photo']) && $visitor['photo']): ?>
                        <img src="<?= url('/uploads/visitors/' . $visitor['photo']) ?>" 
                             alt="<?= htmlspecialchars($visitor['name']) ?>" 
                             class="rounded-circle mb-3"
                             style="width: 128px; height: 128px; object-fit: cover;">
                    <?php else: ?>
                        <div class="avatar bg-primary text-white mb-3 mx-auto d-flex align-items-center justify-content-center"
                             style="width: 128px; height: 128px; border-radius: 50%; font-size: 48px;">
                            <?= strtoupper(substr($visitor['name'], 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    <h5 class="mb-1"><?= htmlspecialchars($visitor['name']) ?></h5>
                    <?php if (!empty($visitor['group_name'])): ?>
                        <p class="text-muted mb-3">
                            Grupo: <span class="badge bg-info"><?= htmlspecialchars($visitor['group_name']) ?></span>
                        </p>
                    <?php endif; ?>
                    <div class="d-grid gap-2">
                        <?php if (!empty($visitor['phone'])): ?>
                            <a href="https://wa.me/<?= formatPhoneForWhatsApp($visitor['phone']) ?>" 
                               target="_blank"
                               class="btn btn-success">
                                <i class="fab fa-whatsapp me-2"></i>WhatsApp
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($visitor['email'])): ?>
                            <a href="mailto:<?= htmlspecialchars($visitor['email']) ?>" 
                               class="btn btn-primary">
                                <i class="fas fa-envelope me-2"></i>Email
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Follow-up History -->
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Histórico de Follow-ups</h5>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addContactLogModal">
                        <i class="fas fa-plus me-2"></i>Novo Follow-up
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" id="followUpList">
                        <?php if (isset($visitor['contact_logs']) && !empty($visitor['contact_logs'])): ?>
                            <?php foreach ($visitor['contact_logs'] as $log): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <?= date('d/m/Y H:i', strtotime($log['created_at'])) ?>
                                        </small>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-link text-muted" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <button class="dropdown-item text-success" onclick="updateFollowUpStatus(<?= $log['id'] ?>, 'completed')">
                                                        <i class="fas fa-check me-2"></i>Marcar como Concluído
                                                    </button>
                                                </li>
                                                <li>
                                                    <button class="dropdown-item text-danger" onclick="updateFollowUpStatus(<?= $log['id'] ?>, 'cancelled')">
                                                        <i class="fas fa-times me-2"></i>Cancelar
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <p class="mb-1"><?= nl2br(htmlspecialchars($log['notes'] ?? '')) ?></p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">Por: <?= htmlspecialchars($log['user_name'] ?? 'Sistema') ?></small>
                                        <span class="badge bg-<?= isset($log['status']) && $log['status'] === 'completed' ? 'success' : 'danger' ?>">
                                            <?= isset($log['status']) ? ucfirst($log['status']) : 'Pendente' ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-info-circle me-2"></i>Nenhum follow-up registrado
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Group History -->
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Histórico de Grupos</h5>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addToGroupModal">
                        <i class="fas fa-plus me-2"></i>Adicionar a Grupo
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php if (isset($visitor['group_history']) && !empty($visitor['group_history'])): ?>
                            <?php foreach ($visitor['group_history'] as $history): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-info"><?= htmlspecialchars($history['group_name']) ?></span>
                                        <small class="text-muted">
                                            <?= date('d/m/Y', strtotime($history['joined_at'])) ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-info-circle me-2"></i>Nenhum histórico de grupo
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Follow-up Modal -->
<div class="modal fade" id="addContactLogModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Follow-up</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addContactLogForm" action="<?= url('/visitors/' . $visitor['id'] . '/contact-logs') ?>" method="POST">
                    <div class="mb-3">
                        <label for="notes" class="form-label">Anotações</label>
                        <textarea class="form-control" id="notes" name="notes" rows="4" required></textarea>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add to Group Modal -->
<div class="modal fade" id="addToGroupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adicionar a Grupo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addToGroupForm" action="<?= url('/visitors/add-to-group') ?>" method="POST">
                    <input type="hidden" name="visitor_id" value="<?= $visitor['id'] ?>">
                    <div class="mb-3">
                        <label for="group_id" class="form-label">Grupo</label>
                        <select class="form-select" id="group_id" name="group_id" required>
                            <option value="">Selecione um grupo...</option>
                            <?php foreach ($groups as $group): ?>
                                <option value="<?= $group['id'] ?>"><?= htmlspecialchars($group['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar todos os tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Adicionar Follow-up
        const addContactLogForm = document.getElementById('addContactLogForm');
        if (addContactLogForm) {
            addContactLogForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                try {
                    const response = await fetch(this.action, {
                        method: 'POST',
                        body: new FormData(this),
                    });
                    
                    if (response.ok) {
                        window.location.reload();
                    } else {
                        alert('Erro ao adicionar follow-up. Por favor, tente novamente.');
                    }
                } catch (error) {
                    console.error('Erro:', error);
                    alert('Erro ao adicionar follow-up. Por favor, tente novamente.');
                }
            });
        }

        // Adicionar a Grupo
        const addToGroupForm = document.getElementById('addToGroupForm');
        if (addToGroupForm) {
            addToGroupForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                try {
                    const response = await fetch(this.action, {
                        method: 'POST',
                        body: new FormData(this),
                    });
                    
                    if (response.ok) {
                        window.location.reload();
                    } else {
                        alert('Erro ao adicionar ao grupo. Por favor, tente novamente.');
                    }
                } catch (error) {
                    console.error('Erro:', error);
                    alert('Erro ao adicionar ao grupo. Por favor, tente novamente.');
                }
            });
        }
    });

    // Atualizar status do Follow-up
    async function updateFollowUpStatus(logId, status) {
        try {
            const response = await fetch(`${window.location.pathname}/contact-logs/${logId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `status=${status}`,
            });
            
            if (response.ok) {
                window.location.reload();
            } else {
                alert('Erro ao atualizar status. Por favor, tente novamente.');
            }
        } catch (error) {
            console.error('Erro:', error);
            alert('Erro ao atualizar status. Por favor, tente novamente.');
        }
    }
</script>

<?= $styles ?>
