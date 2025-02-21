<?php
$this->layout('layouts/main', ['title' => 'Criar Formulário de Visitante']);
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Criar Formulário de Visitante</h1>
        <a href="<?= url('/visitor-forms') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="<?= url('/visitor-forms') ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <!-- Informações Básicas -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Título do Formulário *</label>
                            <input type="text" 
                                   class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>" 
                                   id="title" 
                                   name="title" 
                                   value="<?= old('title') ?>"
                                   required>
                            <?php if (isset($errors['title'])): ?>
                                <div class="invalid-feedback"><?= $errors['title'] ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>" 
                                      id="description" 
                                      name="description"><?= old('description') ?></textarea>
                            <?php if (isset($errors['description'])): ?>
                                <div class="invalid-feedback"><?= $errors['description'] ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Campos do Formulário -->
                        <h4 class="mt-4 mb-3">Campos do Formulário</h4>
                        <div id="form-fields">
                            <!-- Campo Nome -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <input type="hidden" name="fields[0][field_name]" value="name">
                                    <input type="hidden" name="fields[0][field_type]" value="text">
                                    <input type="hidden" name="fields[0][display_order]" value="0">
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Rótulo do Campo</label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       name="fields[0][field_label]" 
                                                       value="Nome" 
                                                       required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Texto de Ajuda</label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       name="fields[0][help_text]" 
                                                       value="Digite seu nome completo">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="fields[0][is_required]" 
                                               checked>
                                        <label class="form-check-label">Campo Obrigatório</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Campo Email -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <input type="hidden" name="fields[1][field_name]" value="email">
                                    <input type="hidden" name="fields[1][field_type]" value="email">
                                    <input type="hidden" name="fields[1][display_order]" value="1">
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Rótulo do Campo</label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       name="fields[1][field_label]" 
                                                       value="Email" 
                                                       required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Texto de Ajuda</label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       name="fields[1][help_text]" 
                                                       value="Digite seu email">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="fields[1][is_required]" 
                                               checked>
                                        <label class="form-check-label">Campo Obrigatório</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Campo Telefone -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <input type="hidden" name="fields[2][field_name]" value="phone">
                                    <input type="hidden" name="fields[2][field_type]" value="phone">
                                    <input type="hidden" name="fields[2][display_order]" value="2">
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Rótulo do Campo</label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       name="fields[2][field_label]" 
                                                       value="Telefone" 
                                                       required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Texto de Ajuda</label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       name="fields[2][help_text]" 
                                                       value="Digite seu telefone com DDD">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="fields[2][is_required]" 
                                               checked>
                                        <label class="form-check-label">Campo Obrigatório</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="theme_color" class="form-label">Cor do Tema</label>
                                    <input type="color" 
                                           class="form-control form-control-color w-100 <?= isset($errors['theme_color']) ? 'is-invalid' : '' ?>" 
                                           id="theme_color" 
                                           name="theme_color" 
                                           value="<?= old('theme_color', '#007bff') ?>">
                                    <?php if (isset($errors['theme_color'])): ?>
                                        <div class="invalid-feedback"><?= $errors['theme_color'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="active" 
                                           name="active" 
                                           value="1" 
                                           <?= old('active', '1') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="active">
                                        Formulário Ativo
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Criar Formulário
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Adicionar máscara ao campo de telefone
    const phones = document.querySelectorAll('input[name="phone"]');
    phones.forEach(phone => {
        phone.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 0) {
                value = '(' + value;
                if (value.length > 3) {
                    value = value.slice(0, 3) + ') ' + value.slice(3);
                }
                if (value.length > 10) {
                    value = value.slice(0, 10) + '-' + value.slice(10, 14);
                }
            }
            e.target.value = value;
        });
    });
});
