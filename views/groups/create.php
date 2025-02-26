<?php
use App\Core\View;

$title = 'Criar Grupo';
View::extends('layouts/app');
?>

<?php View::section('content') ?>
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="fas fa-users"></i> Criar Novo Grupo</h2>
                <a href="<?= View::url('/groups') ?>" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="<?= View::url('/groups') ?>" method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="_token" value="<?= View::csrf_token() ?>">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nome do Grupo *</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                                <div class="invalid-feedback">Por favor, informe o nome do grupo.</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="ministry_id" class="form-label">Ministério *</label>
                                <select class="form-select select2" id="ministry_id" name="ministry_id" required>
                                    <option value="">Selecione...</option>
                                    <?php foreach ($ministries as $ministry): ?>
                                        <option value="<?= $ministry['id'] ?>"><?= $ministry['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Por favor, selecione o ministério.</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="leaders" class="form-label">Líderes</label>
                                <select class="form-select select2" id="leaders" name="leaders[]" multiple>
                                    <?php foreach ($leaders as $leader): ?>
                                        <option value="<?= $leader['id'] ?>"><?= $leader['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Selecione um ou mais líderes para o grupo</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="meeting_day" class="form-label">Dia da Reunião *</label>
                                <select class="form-select" id="meeting_day" name="meeting_day" required>
                                    <option value="">Selecione...</option>
                                    <option value="Segunda-feira">Segunda-feira</option>
                                    <option value="Terça-feira">Terça-feira</option>
                                    <option value="Quarta-feira">Quarta-feira</option>
                                    <option value="Quinta-feira">Quinta-feira</option>
                                    <option value="Sexta-feira">Sexta-feira</option>
                                    <option value="Sábado">Sábado</option>
                                    <option value="Domingo">Domingo</option>
                                </select>
                                <div class="invalid-feedback">Por favor, selecione o dia da reunião.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="meeting_time" class="form-label">Horário da Reunião *</label>
                                <input type="text" class="form-control timepicker" id="meeting_time" name="meeting_time" required>
                                <div class="invalid-feedback">Por favor, informe o horário da reunião.</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="meeting_address" class="form-label">Endereço *</label>
                                <input type="text" class="form-control" id="meeting_address" name="meeting_address" required>
                                <div class="invalid-feedback">Por favor, informe o endereço.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="neighborhood" class="form-label">Bairro *</label>
                                <input type="text" class="form-control" id="neighborhood" name="neighborhood" required>
                                <div class="invalid-feedback">Por favor, informe o bairro.</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="max_participants" class="form-label">Número Máximo de Participantes *</label>
                                <input type="number" class="form-control" id="max_participants" name="max_participants" min="1" value="12" required>
                                <div class="invalid-feedback">Por favor, informe o número máximo de participantes.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="status" class="form-label">Status *</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="active">Ativo</option>
                                    <option value="inactive">Inativo</option>
                                </select>
                                <div class="invalid-feedback">Por favor, selecione o status.</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php View::endSection() ?>

<?php View::section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validação do formulário
    const form = document.querySelector('.needs-validation');
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});
</script>
<?php View::endSection() ?>

<?php View::section('styles') ?>
<style>
.select2-container--bootstrap-5 .select2-selection {
    min-height: 38px;
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
}
</style>
<?php View::endSection() ?>
