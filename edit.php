<?php
$title = 'Editar Grupo';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Editar Grupo</h1>
    
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-users me-1"></i>
                Editar Grupo: <?= isset($group['name']) ? htmlspecialchars($group['name']) : '' ?>
            </div>
            <div>
                <a href="/gcmanager/groups" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Voltar
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="/gcmanager/groups/<?= $group['id'] ?>/update" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nome do Grupo *</label>
                        <input type="text" class="form-control" id="name" name="name" required
                               value="<?= isset($group['name']) ? htmlspecialchars($group['name']) : '' ?>">
                    </div>
                    
                    <div class="col-md-6">
                        <label for="leaders" class="form-label">Líderes *</label>
                        <select class="form-select" id="leaders" name="leaders[]" multiple required>
                            <?php 
                            $currentLeaders = [];
                            if (!empty($group['leaders'])) {
                                $leadersList = explode(',', $group['leaders']);
                                foreach ($leadersList as $leader) {
                                    list($name, $id) = explode('|', $leader);
                                    $currentLeaders[] = $id;
                                }
                            }
                            foreach ($leaders as $leader): 
                            ?>
                                <option value="<?= $leader['id'] ?>" <?= in_array($leader['id'], $currentLeaders) ? 'selected' : '' ?>>
                                    <?= isset($leader['name']) ? htmlspecialchars($leader['name']) : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">Pressione Ctrl (Cmd no Mac) para selecionar múltiplos líderes</small>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="photo" class="form-label">Foto/Marca do Grupo</label>
                        <div class="input-group">
                            <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                            <?php if (!empty($group['photo'])): ?>
                                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#currentPhotoModal">
                                    Ver Atual
                                </button>
                            <?php endif; ?>
                        </div>
                        <small class="form-text text-muted">Formatos aceitos: JPG, PNG. Tamanho máximo: 2MB</small>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="co_leaders" class="form-label">Co-líderes</label>
                        <select class="form-select" id="co_leaders" name="co_leaders[]" multiple>
                            <?php 
                            $currentCoLeaders = [];
                            if (!empty($group['co_leaders'])) {
                                $coLeadersList = explode(',', $group['co_leaders']);
                                foreach ($coLeadersList as $coLeader) {
                                    list($name, $id) = explode('|', $coLeader);
                                    $currentCoLeaders[] = $id;
                                }
                            }
                            foreach ($leaders as $leader): 
                            ?>
                                <option value="<?= $leader['id'] ?>" <?= in_array($leader['id'], $currentCoLeaders) ? 'selected' : '' ?>>
                                    <?= isset($leader['name']) ? htmlspecialchars($leader['name']) : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">Pressione Ctrl (Cmd no Mac) para selecionar múltiplos co-líderes</small>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="meeting_day" class="form-label">Dia da Reunião *</label>
                        <select class="form-select" id="meeting_day" name="meeting_day" required>
                            <option value="">Selecione</option>
                            <option value="Segunda-feira" <?= $group['meeting_day'] === 'Segunda-feira' ? 'selected' : '' ?>>Segunda-feira</option>
                            <option value="Terça-feira" <?= $group['meeting_day'] === 'Terça-feira' ? 'selected' : '' ?>>Terça-feira</option>
                            <option value="Quarta-feira" <?= $group['meeting_day'] === 'Quarta-feira' ? 'selected' : '' ?>>Quarta-feira</option>
                            <option value="Quinta-feira" <?= $group['meeting_day'] === 'Quinta-feira' ? 'selected' : '' ?>>Quinta-feira</option>
                            <option value="Sexta-feira" <?= $group['meeting_day'] === 'Sexta-feira' ? 'selected' : '' ?>>Sexta-feira</option>
                            <option value="Sábado" <?= $group['meeting_day'] === 'Sábado' ? 'selected' : '' ?>>Sábado</option>
                            <option value="Domingo" <?= $group['meeting_day'] === 'Domingo' ? 'selected' : '' ?>>Domingo</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="meeting_time" class="form-label">Horário *</label>
                        <input type="time" class="form-control" id="meeting_time" name="meeting_time" required
                               value="<?= isset($group['meeting_time']) ? htmlspecialchars($group['meeting_time']) : '' ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-8">
                        <label for="address" class="form-label">Endereço *</label>
                        <input type="text" class="form-control" id="address" name="address" required
                               value="<?= isset($group['address']) ? htmlspecialchars($group['address']) : '' ?>">
                    </div>
                    
                    <div class="col-md-4">
                        <label for="max_participants" class="form-label">Número Máximo de Participantes</label>
                        <input type="number" class="form-control" id="max_participants" name="max_participants" min="1"
                               value="<?= isset($group['max_participants']) ? htmlspecialchars($group['max_participants']) : '12' ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?= isset($group['description']) ? htmlspecialchars($group['description']) : '' ?></textarea>
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

    <?php require '_participants.php'; ?>
</div>
