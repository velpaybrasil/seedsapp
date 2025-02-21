<?php
$title = 'Novo Grupo';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Novo Grupo</h1>
    
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-users me-1"></i>
                Criar Novo Grupo
            </div>
            <div>
                <a href="<?= url('groups') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Voltar
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="<?= url('groups/store') ?>" method="POST">
                <?= csrf_field() ?>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nome do Grupo *</label>
                        <input type="text" class="form-control" id="name" name="name" required
                            value="<?= htmlspecialchars(old('name')) ?>">
                    </div>
                    
                    <div class="col-md-6">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars(old('description')) ?></textarea>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="meeting_address" class="form-label">Endereço Principal *</label>
                        <input type="text" class="form-control" id="meeting_address" name="meeting_address" required
                            value="<?= htmlspecialchars(old('meeting_address')) ?>"
                            placeholder="Ex: Rua das Flores, 123">
                        <small class="form-text text-muted">Endereço onde acontece a maior parte dos encontros</small>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="neighborhood" class="form-label">Bairro Principal *</label>
                        <input type="text" class="form-control" id="neighborhood" name="neighborhood" required
                            value="<?= htmlspecialchars(old('neighborhood')) ?>"
                            placeholder="Ex: Centro">
                        <small class="form-text text-muted">Bairro onde acontece a maior parte dos encontros</small>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="extra_neighborhoods" class="form-label">Bairros Extras</label>
                    <textarea class="form-control" id="extra_neighborhoods" name="extra_neighborhoods" rows="2"
                        placeholder="Ex: Jardim América, Vila Nova, Centro"><?= htmlspecialchars(old('extra_neighborhoods')) ?></textarea>
                    <small class="form-text text-muted">Outros bairros onde o grupo também se reúne (separe por vírgula)</small>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="meeting_day" class="form-label">Dia da Reunião *</label>
                        <select class="form-select" id="meeting_day" name="meeting_day" required>
                            <option value="">Selecione...</option>
                            <option value="sunday" <?= old('meeting_day') === 'sunday' ? 'selected' : '' ?>>Domingo</option>
                            <option value="monday" <?= old('meeting_day') === 'monday' ? 'selected' : '' ?>>Segunda-feira</option>
                            <option value="tuesday" <?= old('meeting_day') === 'tuesday' ? 'selected' : '' ?>>Terça-feira</option>
                            <option value="wednesday" <?= old('meeting_day') === 'wednesday' ? 'selected' : '' ?>>Quarta-feira</option>
                            <option value="thursday" <?= old('meeting_day') === 'thursday' ? 'selected' : '' ?>>Quinta-feira</option>
                            <option value="friday" <?= old('meeting_day') === 'friday' ? 'selected' : '' ?>>Sexta-feira</option>
                            <option value="saturday" <?= old('meeting_day') === 'saturday' ? 'selected' : '' ?>>Sábado</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="meeting_time" class="form-label">Horário *</label>
                        <input type="time" class="form-control" id="meeting_time" name="meeting_time" required
                            value="<?= htmlspecialchars(old('meeting_time')) ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="max_participants" class="form-label">Número Máximo de Participantes</label>
                        <input type="number" class="form-control" id="max_participants" name="max_participants"
                            value="<?= htmlspecialchars(old('max_participants', '12')) ?>"
                            min="1" max="100">
                        <small class="form-text text-muted">Deixe em branco para usar o padrão (12)</small>
                    </div>

                    <div class="col-md-6">
                        <label for="ministry_id" class="form-label">Ministério</label>
                        <select class="form-select" id="ministry_id" name="ministry_id">
                            <option value="">Selecione...</option>
                            <?php foreach ($ministries as $ministry): ?>
                                <option value="<?= $ministry['id'] ?>" <?= old('ministry_id') == $ministry['id'] ? 'selected' : '' ?>>
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
        </div>
    </div>
</div>
