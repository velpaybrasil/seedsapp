<?php
/**
 * @var array $data
 */
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Novo Ministério</h1>
        <a href="<?= url('ministries') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="<?= url('ministries') ?>" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Nome *</label>
                            <input type="text" class="form-control" id="name" name="name" required value="<?= $data['name'] ?? '' ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="1" <?= isset($data['active']) && $data['active'] ? 'selected' : '' ?>>Ativo</option>
                                <option value="0" <?= isset($data['active']) && !$data['active'] ? 'selected' : '' ?>>Inativo</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="description" class="form-label">Descrição</label>
                    <textarea class="form-control" id="description" name="description" rows="3"><?= $data['description'] ?? '' ?></textarea>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label">Líderes</label>
                    <div class="leaders-container">
                        <div class="leader-row mb-2">
                            <div class="row">
                                <div class="col-md-8">
                                    <select class="form-control" name="leaders[0][user_id]">
                                        <option value="">Selecione um líder</option>
                                        <?php foreach ($users as $user): ?>
                                            <option value="<?= $user['id'] ?>">
                                                <?= $user['name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-control" name="leaders[0][role]">
                                        <option value="leader">Líder</option>
                                        <option value="co_leader">Co-líder</option>
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-danger remove-leader" style="display: none;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary mt-2" id="add-leader">
                        <i class="fas fa-plus"></i> Adicionar Líder
                    </button>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.querySelector('.leaders-container');
    const addButton = document.querySelector('#add-leader');
    let leaderCount = 1;

    addButton.addEventListener('click', function() {
        const template = `
            <div class="leader-row mb-2">
                <div class="row">
                    <div class="col-md-8">
                        <select class="form-control" name="leaders[${leaderCount}][user_id]">
                            <option value="">Selecione um líder</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['id'] ?>"><?= $user['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-control" name="leaders[${leaderCount}][role]">
                            <option value="leader">Líder</option>
                            <option value="co_leader">Co-líder</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger remove-leader">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', template);
        leaderCount++;
        
        if (container.querySelectorAll('.leader-row').length > 1) {
            container.querySelectorAll('.remove-leader').forEach(btn => btn.style.display = 'block');
        }
    });

    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-leader')) {
            e.target.closest('.leader-row').remove();
            
            if (container.querySelectorAll('.leader-row').length <= 1) {
                container.querySelectorAll('.remove-leader').forEach(btn => btn.style.display = 'none');
            }
        }
    });
});
</script>
