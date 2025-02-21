<?php
/**
 * @var array $ministry
 */
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Editar Ministério</h1>
        <a href="<?= url('ministries') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="<?= url("ministries/{$ministry['id']}") ?>" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Nome *</label>
                            <input type="text" class="form-control" id="name" name="name" required value="<?= htmlspecialchars($ministry['name']) ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="active" <?= $ministry['status'] === 'active' ? 'selected' : '' ?>>Ativo</option>
                                <option value="inactive" <?= $ministry['status'] === 'inactive' ? 'selected' : '' ?>>Inativo</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="description" class="form-label">Descrição</label>
                    <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($ministry['description']) ?></textarea>
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
