<?php
$title = 'Detalhes do Visitante';
?>

<div class="container-fluid">
    <!-- Page Title -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0"><?= isset($visitor['name']) ? htmlspecialchars($visitor['name']) : 'Visitante' ?></h1>
        <div>
            <a href="<?= url('/gcmanager/visitors/' . $visitor['id'] . '/edit') ?>" class="btn btn-primary me-2">
                <i class="fas fa-edit me-2"></i>Editar
            </a>
            <a href="<?= url('/gcmanager/visitors') ?>" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Voltar
            </a>
        </div>
    </div>

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
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label text-muted">Data de Nascimento</label>
                            <p class="mb-0">
                                <?= isset($visitor['birthdate']) && $visitor['birthdate'] ? (new DateTime($visitor['birthdate']))->format('d/m/Y') : '-' ?>
                            </p>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label text-muted">Idade</label>
                            <p class="mb-0"><?= isset($visitor['age']) && $visitor['age'] ? $visitor['age'] . ' anos' : '-' ?></p>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label text-muted">Gênero</label>
                            <p class="mb-0">
                                <?= isset($visitor['gender']) ? match($visitor['gender']) {
                                    'M' => 'Masculino',
                                    'F' => 'Feminino',
                                    default => '-'
                                } : '-' ?>
                            </p>
                        </div>
                        
                        <div class="col-md-5 mb-3">
                            <label class="form-label text-muted">Email</label>
                            <p class="mb-0">
                                <?php if (isset($visitor['email']) && $visitor['email']): ?>
                                    <a href="mailto:<?= htmlspecialchars($visitor['email']) ?>" class="text-decoration-none">
                                        <i class="fas fa-envelope me-2"></i><?= htmlspecialchars($visitor['email']) ?>
                                    </a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </p>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Telefone</label>
                            <p class="mb-0">
                                <?php if (isset($visitor['phone']) && $visitor['phone']): ?>
                                    <a href="tel:<?= htmlspecialchars($visitor['phone']) ?>" class="text-decoration-none">
                                        <i class="fas fa-phone me-2"></i><?= htmlspecialchars($visitor['phone']) ?>
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
                        <div class="col-md-3 mb-3">
                            <label class="form-label text-muted">CEP</label>
                            <p class="mb-0"><?= isset($visitor['zipcode']) ? htmlspecialchars($visitor['zipcode']) : '-' ?></p>
                        </div>
                        
                        <div class="col-md-7 mb-3">
                            <label class="form-label text-muted">Logradouro</label>
                            <p class="mb-0"><?= isset($visitor['street']) ? htmlspecialchars($visitor['street']) : '-' ?></p>
                        </div>
                        
                        <div class="col-md-2 mb-3">
                            <label class="form-label text-muted">Número</label>
                            <p class="mb-0"><?= isset($visitor['number']) ? htmlspecialchars($visitor['number']) : '-' ?></p>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Complemento</label>
                            <p class="mb-0"><?= isset($visitor['complement']) ? htmlspecialchars($visitor['complement']) : '-' ?></p>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Bairro</label>
                            <p class="mb-0"><?= isset($visitor['neighborhood']) ? htmlspecialchars($visitor['neighborhood']) : '-' ?></p>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Cidade</label>
                            <p class="mb-0"><?= isset($visitor['city']) ? htmlspecialchars($visitor['city']) : '-' ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Visit History -->
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Histórico de Visitas</h5>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addVisitModal">
                        <i class="fas fa-plus me-2"></i>Nova Visita
                    </button>
                </div>
                <div class="card-body">
                    <?php if (isset($visits) && !empty($visits)): ?>
                        <div class="timeline">
                            <?php foreach ($visits as $visit): ?>
                                <div class="timeline-item">
                                    <div class="timeline-content">
                                        <h6 class="mb-1"><?= isset($visit['date']) ? (new DateTime($visit['date']))->format('d/m/Y') : '-' ?></h6>
                                        <p class="mb-0 text-muted"><?= isset($visit['notes']) ? htmlspecialchars($visit['notes']) : 'Sem observações' ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">Nenhuma visita registrada.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Profile Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <?php if (isset($visitor['photo']) && $visitor['photo']): ?>
                        <img src="<?= htmlspecialchars($visitor['photo']) ?>" alt="Foto do Visitante" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    <?php else: ?>
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 150px; height: 150px;">
                            <i class="fas fa-user fa-4x text-muted"></i>
                        </div>
                    <?php endif; ?>
                    
                    <h5 class="card-title mb-0"><?= isset($visitor['name']) ? htmlspecialchars($visitor['name']) : 'Visitante' ?></h5>
                    <?php if (isset($visitor['email']) && $visitor['email']): ?>
                        <p class="text-muted mb-3"><?= htmlspecialchars($visitor['email']) ?></p>
                    <?php endif; ?>
                    
                    <div class="d-grid gap-2">
                        <a href="<?= url('/gcmanager/visitors/' . $visitor['id'] . '/edit') ?>" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Editar Perfil
                        </a>
                    </div>
                </div>
            </div>

            <!-- Follow-ups -->
            <div class="card shadow-sm">
                <div class="card-header py-3">
                    <h5 class="card-title mb-0">Follow-ups</h5>
                </div>
                <div class="card-body p-0">
                    <?php if (isset($followups) && !empty($followups)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($followups as $followup): ?>
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?= isset($followup['date']) ? (new DateTime($followup['date']))->format('d/m/Y') : '-' ?></h6>
                                        <small class="text-muted"><?= isset($followup['type']) ? htmlspecialchars($followup['type']) : '-' ?></small>
                                    </div>
                                    <p class="mb-1"><?= isset($followup['notes']) ? htmlspecialchars($followup['notes']) : 'Sem observações' ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="p-3">
                            <p class="text-muted mb-0">Nenhum follow-up registrado.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Visit Modal -->
<div class="modal fade" id="addVisitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Nova Visita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= url('/gcmanager/visitors/' . $visitor['id'] . '/visits') ?>" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="visitDate" class="form-label">Data da Visita</label>
                        <input type="date" class="form-control" id="visitDate" name="date" required>
                    </div>
                    <div class="mb-3">
                        <label for="visitNotes" class="form-label">Observações</label>
                        <textarea class="form-control" id="visitNotes" name="notes" rows="3"></textarea>
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

<style>
.timeline {
    position: relative;
    padding-left: 1rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 2px;
    background-color: #e9ecef;
}

.timeline-item {
    position: relative;
    padding-bottom: 1.5rem;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -0.4375rem;
    top: 0.5rem;
    width: 0.875rem;
    height: 0.875rem;
    border-radius: 50%;
    background-color: #fff;
    border: 2px solid #007bff;
}

.timeline-content {
    padding-left: 1rem;
}
</style>
