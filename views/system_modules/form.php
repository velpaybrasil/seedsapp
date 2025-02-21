<?php
$isEdit = isset($module);
$title = $isEdit ? 'Editar Módulo' : 'Novo Módulo';
$this->layout('layouts/default', ['title' => $title]);
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $title ?></h1>
    
    <?php $this->insert('partials/flash_messages') ?>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-cube me-1"></i>
            <?= $title ?>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= $isEdit ? "/system-modules/{$module['id']}" : '/system-modules' ?>">
                <?php if ($isEdit): ?>
                    <input type="hidden" name="_method" value="PUT">
                <?php endif; ?>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nome *</label>
                        <input type="text" class="form-control" id="name" name="name" required
                               value="<?= $isEdit ? $this->escape($module['name']) : '' ?>">
                    </div>
                    
                    <div class="col-md-6">
                        <label for="slug" class="form-label">Slug *</label>
                        <input type="text" class="form-control" id="slug" name="slug" required
                               value="<?= $isEdit ? $this->escape($module['slug']) : '' ?>">
                        <div class="form-text">Identificador único do módulo (apenas letras, números e hífens)</div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?= $isEdit ? $this->escape($module['description']) : '' ?></textarea>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="icon" class="form-label">Ícone</label>
                        <input type="text" class="form-control" id="icon" name="icon"
                               value="<?= $isEdit ? $this->escape($module['icon']) : '' ?>">
                        <div class="form-text">Nome do ícone do Font Awesome (sem o prefixo fa-)</div>
                    </div>

                    <div class="col-md-4">
                        <label for="parent_id" class="form-label">Módulo Pai</label>
                        <select class="form-select" id="parent_id" name="parent_id">
                            <option value="">Nenhum</option>
                            <?php foreach ($modules as $m): ?>
                                <?php if (!$isEdit || $m['id'] != $module['id']): ?>
                                    <option value="<?= $m['id'] ?>" <?= $isEdit && $module['parent_id'] == $m['id'] ? 'selected' : '' ?>>
                                        <?= $this->escape($m['name']) ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="order_index" class="form-label">Ordem</label>
                        <input type="number" class="form-control" id="order_index" name="order_index"
                               value="<?= $isEdit ? $this->escape($module['order_index']) : '0' ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="active" name="active" value="1"
                               <?= (!$isEdit || $module['active']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="active">
                            Ativo
                        </label>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="/system-modules" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('name').addEventListener('input', function(e) {
    if (!document.getElementById('slug').value) {
        // Converte o nome para slug
        let slug = e.target.value.toLowerCase()
            .replace(/[^\w\s-]/g, '') // Remove caracteres especiais
            .replace(/\s+/g, '-')     // Substitui espaços por hífens
            .replace(/-+/g, '-');     // Remove hífens duplicados
        
        document.getElementById('slug').value = slug;
    }
});
</script>
