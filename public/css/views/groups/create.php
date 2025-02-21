<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Novo Grupo</h1>
    
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-users me-1"></i>
                Criar Novo Grupo
            </div>
            <div>
                <a href="/gcmanager/groups" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Voltar
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="/gcmanager/groups/store" method="POST">
                <?= csrf_field() ?>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nome do Grupo *</label>
                        <input type="text" class="form-control" id="name" name="name" required
                            value="<?= htmlspecialchars(old('name')) ?>">
                    </div>
                    
                    <div class="col-md-6">
                        <label for="leader_id" class="form-label">Líder *</label>
                        <select class="form-select" id="leader_id" name="leader_id" required>
                            <option value="">Selecione um líder</option>
                            <?php foreach ($leaders as $leader): ?>
                                <option value="<?= $leader['id'] ?>" <?= old('leader_id') == $leader['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($leader['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="co_leader_id" class="form-label">Co-líder</label>
                        <select class="form-select" id="co_leader_id" name="co_leader_id">
                            <option value="">Selecione um co-líder</option>
                            <?php foreach ($leaders as $leader): ?>
                                <option value="<?= $leader['id'] ?>" <?= old('co_leader_id') == $leader['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($leader['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="meeting_day" class="form-label">Dia da Reunião *</label>
                        <select class="form-select" id="meeting_day" name="meeting_day" required>
                            <option value="">Selecione</option>
                            <option value="Segunda-feira" <?= old('meeting_day') === 'Segunda-feira' ? 'selected' : '' ?>>Segunda-feira</option>
                            <option value="Terça-feira" <?= old('meeting_day') === 'Terça-feira' ? 'selected' : '' ?>>Terça-feira</option>
                            <option value="Quarta-feira" <?= old('meeting_day') === 'Quarta-feira' ? 'selected' : '' ?>>Quarta-feira</option>
                            <option value="Quinta-feira" <?= old('meeting_day') === 'Quinta-feira' ? 'selected' : '' ?>>Quinta-feira</option>
                            <option value="Sexta-feira" <?= old('meeting_day') === 'Sexta-feira' ? 'selected' : '' ?>>Sexta-feira</option>
                            <option value="Sábado" <?= old('meeting_day') === 'Sábado' ? 'selected' : '' ?>>Sábado</option>
                            <option value="Domingo" <?= old('meeting_day') === 'Domingo' ? 'selected' : '' ?>>Domingo</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="meeting_time" class="form-label">Horário *</label>
                        <input type="time" class="form-control" id="meeting_time" name="meeting_time" required
                            value="<?= htmlspecialchars(old('meeting_time')) ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-8">
                        <label for="address" class="form-label">Endereço *</label>
                        <input type="text" class="form-control" id="address" name="address" required
                            value="<?= htmlspecialchars(old('address')) ?>">
                    </div>
                    
                    <div class="col-md-4">
                        <label for="max_participants" class="form-label">Número Máximo de Participantes</label>
                        <input type="number" class="form-control" id="max_participants" name="max_participants" min="1"
                            value="<?= htmlspecialchars(old('max_participants', 12)) ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars(old('description')) ?></textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="/gcmanager/groups" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
