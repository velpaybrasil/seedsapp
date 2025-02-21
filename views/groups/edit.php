<?php

use App\Core\View;

$title = 'Editar Grupo';
View::extends('app');
?>

<?php View::section('styles') ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .required-field::after {
        content: " *";
        color: #dc3545;
    }
    .map-container {
        position: relative;
    }
    .map-overlay {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 1000;
        background: white;
        padding: 10px;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .select2-container {
        width: 100% !important;
    }
    .form-section {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 4px;
        margin-bottom: 20px;
    }
    .form-section-title {
        font-size: 1.1rem;
        font-weight: 500;
        margin-bottom: 15px;
        color: #0d6efd;
    }
</style>
<?php View::endSection() ?>

<?php View::section('content') ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-users me-2"></i>
            Editar Grupo: <?= isset($group['name']) ? htmlspecialchars($group['name']) : '' ?>
        </h1>
        <a href="/groups" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>
            Voltar
        </a>
    </div>
    
    <form action="/groups/<?= $group['id'] ?>" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
        <?= csrf_field() ?>
        
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="name" class="form-label">Nome do Grupo *</label>
                <input type="text" class="form-control" id="name" name="name" required
                    value="<?= htmlspecialchars($group['name']) ?>">
            </div>
            
            <div class="col-md-6">
                <label for="description" class="form-label">Descrição</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($group['description']) ?></textarea>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="meeting_address" class="form-label">Endereço Principal *</label>
                <input type="text" class="form-control" id="meeting_address" name="meeting_address" required
                    value="<?= htmlspecialchars($group['meeting_address']) ?>"
                    placeholder="Ex: Rua das Flores, 123">
                <small class="form-text text-muted">Endereço onde acontece a maior parte dos encontros</small>
            </div>
            
            <div class="col-md-6">
                <label for="neighborhood" class="form-label">Bairro Principal *</label>
                <input type="text" class="form-control" id="neighborhood" name="neighborhood" required
                    value="<?= htmlspecialchars($group['neighborhood']) ?>"
                    placeholder="Ex: Centro">
                <small class="form-text text-muted">Bairro onde acontece a maior parte dos encontros</small>
            </div>
        </div>

        <div class="mb-3">
            <label for="extra_neighborhoods" class="form-label">Bairros Extras</label>
            <textarea class="form-control" id="extra_neighborhoods" name="extra_neighborhoods" rows="2"
                placeholder="Ex: Jardim América, Vila Nova, Centro"><?= htmlspecialchars($group['extra_neighborhoods'] ?? '') ?></textarea>
            <small class="form-text text-muted">Outros bairros onde o grupo também se reúne (separe por vírgula)</small>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="meeting_day" class="form-label">Dia da Reunião *</label>
                <select class="form-select" id="meeting_day" name="meeting_day" required>
                    <option value="">Selecione...</option>
                    <option value="sunday" <?= $group['meeting_day'] === 'sunday' ? 'selected' : '' ?>>Domingo</option>
                    <option value="monday" <?= $group['meeting_day'] === 'monday' ? 'selected' : '' ?>>Segunda-feira</option>
                    <option value="tuesday" <?= $group['meeting_day'] === 'tuesday' ? 'selected' : '' ?>>Terça-feira</option>
                    <option value="wednesday" <?= $group['meeting_day'] === 'wednesday' ? 'selected' : '' ?>>Quarta-feira</option>
                    <option value="thursday" <?= $group['meeting_day'] === 'thursday' ? 'selected' : '' ?>>Quinta-feira</option>
                    <option value="friday" <?= $group['meeting_day'] === 'friday' ? 'selected' : '' ?>>Sexta-feira</option>
                    <option value="saturday" <?= $group['meeting_day'] === 'saturday' ? 'selected' : '' ?>>Sábado</option>
                </select>
            </div>
            
            <div class="col-md-6">
                <label for="meeting_time" class="form-label">Horário *</label>
                <input type="time" class="form-control" id="meeting_time" name="meeting_time" required
                    value="<?= htmlspecialchars($group['meeting_time']) ?>">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="max_participants" class="form-label">Número Máximo de Participantes</label>
                <input type="number" class="form-control" id="max_participants" name="max_participants"
                    value="<?= htmlspecialchars($group['max_participants'] ?? '12') ?>"
                    min="1" max="100">
                <small class="form-text text-muted">Deixe em branco para usar o padrão (12)</small>
            </div>

            <div class="col-md-6">
                <label for="ministry_id" class="form-label">Ministério</label>
                <select class="form-select" id="ministry_id" name="ministry_id">
                    <option value="">Selecione...</option>
                    <?php foreach ($ministries as $ministry): ?>
                        <option value="<?= $ministry['id'] ?>" <?= $group['ministry_id'] == $ministry['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ministry['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="text-end">
            <a href="/groups" class="btn btn-secondary me-2">Cancelar</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Salvar
            </button>
        </div>
    </form>

    <?php require '_participants.php'; ?>
</div>

<?php View::endSection() ?>

<?php View::section('scripts') ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar Select2
    function initializeSelect2(element) {
        $(element).select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    }

    // Inicializar todos os Select2 existentes
    $('.select2').each(function() {
        initializeSelect2(this);
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

    // Inicializar mapa se necessário
    if (typeof L !== 'undefined' && document.getElementById('map')) {
        initializeMap();
    }
});
</script>
<?php View::endSection() ?>
