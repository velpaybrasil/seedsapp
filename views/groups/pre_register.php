<?php
$title = 'Pré-inscrição em Grupo';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Pré-inscrição em Grupo</h1>
    
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-users me-1"></i>
                Pré-inscrição para: <?= htmlspecialchars($group['name']) ?>
            </div>
            <div>
                <a href="<?= url("groups/{$group['id']}") ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Voltar
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Informações do Grupo -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Detalhes do Grupo</h5>
                    <p><strong>Dia:</strong> <?= htmlspecialchars($group['meeting_day']) ?></p>
                    <p><strong>Horário:</strong> <?= htmlspecialchars($group['meeting_time']) ?></p>
                    <p><strong>Endereço:</strong> <?= htmlspecialchars($group['meeting_address']) ?></p>
                    <p><strong>Bairro:</strong> <?= htmlspecialchars($group['neighborhood']) ?></p>
                    <?php if (!empty($group['extra_neighborhoods'])): ?>
                        <p><strong>Outros Bairros:</strong> <?= htmlspecialchars($group['extra_neighborhoods']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <h5>Descrição</h5>
                    <p><?= nl2br(htmlspecialchars($group['description'] ?? 'Nenhuma descrição disponível')) ?></p>
                </div>
            </div>

            <!-- Formulário de Pré-inscrição -->
            <form action="<?= url("groups/{$group['id']}/pre-register") ?>" method="POST" class="needs-validation" novalidate>
                <?= csrf_field() ?>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="visitor_id" class="form-label">Visitante *</label>
                        <select class="form-select" id="visitor_id" name="visitor_id" required>
                            <option value="">Selecione um visitante...</option>
                            <?php foreach ($visitors as $visitor): ?>
                                <option value="<?= $visitor['id'] ?>" <?= old('visitor_id') == $visitor['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($visitor['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Por favor, selecione um visitante.</div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Observações</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"
                        placeholder="Informações adicionais sobre o visitante ou motivo do interesse no grupo"><?= htmlspecialchars(old('notes')) ?></textarea>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Após a pré-inscrição, o líder do grupo será notificado e poderá aprovar ou rejeitar a solicitação.
                </div>

                <div class="text-end">
                    <a href="<?= url("groups/{$group['id']}") ?>" class="btn btn-secondary me-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-1"></i> Enviar Pré-inscrição
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar Select2 para o campo de visitante
    $('#visitor_id').select2({
        theme: 'bootstrap-5',
        placeholder: 'Selecione um visitante...',
        allowClear: true
    });

    // Validação do formulário
    const form = document.querySelector('form');
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});
</script>
