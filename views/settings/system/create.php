<?php $this->layout('layouts/default', ['title' => 'Nova Configuração do Sistema']) ?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Nova Configuração do Sistema</h1>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-plus me-1"></i>
            Criar Nova Configuração
        </div>
        <div class="card-body">
            <form action="/settings/system" method="POST">
                <?= csrf_field() ?>
                
                <div class="mb-3">
                    <label for="category" class="form-label">Categoria</label>
                    <input type="text" class="form-control" id="category" name="category" 
                           value="<?= old('category') ?>" required>
                    <div class="form-text">
                        Exemplo: visitor_rules, user_settings, email_config
                    </div>
                    <?php if ($error = error('category')): ?>
                        <div class="invalid-feedback d-block"><?= $error ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="mb-3">
                    <label for="key_name" class="form-label">Chave</label>
                    <input type="text" class="form-control" id="key_name" name="key_name" 
                           value="<?= old('key_name') ?>" required>
                    <div class="form-text">
                        Exemplo: auto_approve_visitors, default_role, smtp_host
                    </div>
                    <?php if ($error = error('key_name')): ?>
                        <div class="invalid-feedback d-block"><?= $error ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="mb-3">
                    <label for="value" class="form-label">Valor</label>
                    <textarea class="form-control" id="value" name="value" rows="3" required><?= old('value') ?></textarea>
                    <div class="form-text">
                        Para arrays ou objetos JSON, use o formato adequado. Exemplo: ["valor1", "valor2"] ou {"chave": "valor"}
                    </div>
                    <?php if ($error = error('value')): ?>
                        <div class="invalid-feedback d-block"><?= $error ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Descrição</label>
                    <textarea class="form-control" id="description" name="description" rows="2" required><?= old('description') ?></textarea>
                    <?php if ($error = error('description')): ?>
                        <div class="invalid-feedback d-block"><?= $error ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="is_public" name="is_public" value="1"
                               <?= old('is_public') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_public">
                            Configuração Pública
                        </label>
                        <div class="form-text">
                            Se marcado, esta configuração poderá ser acessada por qualquer usuário do sistema.
                        </div>
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    <a href="/settings/system" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Salvar Configuração</button>
                </div>
            </form>
        </div>
    </div>
</div>
