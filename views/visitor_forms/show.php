<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($form['title']) ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="<?= url('https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css') ?>" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="<?= url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css') ?>" rel="stylesheet">
    
    <style>
        :root {
            --theme-color: <?= $form['theme_color'] ?>;
        }
        
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .form-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            margin: 2rem auto;
            max-width: 800px;
            padding: 2rem;
        }
        
        .form-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .form-logo {
            max-width: 200px;
            max-height: 100px;
            margin-bottom: 1rem;
        }
        
        .form-title {
            color: var(--theme-color);
            margin-bottom: 1rem;
        }
        
        .form-description {
            color: #6c757d;
            margin-bottom: 1rem;
        }
        
        .form-control:focus {
            border-color: var(--theme-color);
            box-shadow: 0 0 0 0.2rem rgba(var(--theme-color), 0.25);
        }
        
        .btn-primary {
            background-color: var(--theme-color);
            border-color: var(--theme-color);
        }
        
        .btn-primary:hover {
            background-color: var(--theme-color);
            border-color: var(--theme-color);
            opacity: 0.9;
        }
        
        .required-field::after {
            content: "*";
            color: #dc3545;
            margin-left: 4px;
        }
        
        .form-footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <!-- Cabeçalho do Formulário -->
            <div class="form-header">
                <?php if ($form['logo_url']): ?>
                    <img src="<?= htmlspecialchars($form['logo_url']) ?>" 
                         alt="Logo" 
                         class="form-logo">
                <?php endif; ?>
                
                <h1 class="form-title"><?= htmlspecialchars($form['title']) ?></h1>
                
                <?php if ($form['description']): ?>
                    <p class="form-description"><?= nl2br(htmlspecialchars($form['description'])) ?></p>
                <?php endif; ?>
                
                <?php if ($form['header_text']): ?>
                    <div class="form-header-text">
                        <?= nl2br(htmlspecialchars($form['header_text'])) ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Mensagens Flash -->
            <?php if (isset($_SESSION['flash'])): ?>
                <?php foreach ($_SESSION['flash'] as $type => $message): ?>
                    <div class="alert alert-<?= $type ?> alert-dismissible fade show" role="alert">
                        <?= $message ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endforeach; ?>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>

            <!-- Formulário -->
            <form action="<?= url('/f/' . $form['slug'] . '/submit') ?>" method="POST" id="visitorForm">
                <?php foreach ($fields as $field): ?>
                    <div class="mb-3">
                        <label for="<?= $field['field_name'] ?>" 
                               class="form-label <?= $field['is_required'] ? 'required-field' : '' ?>">
                            <?= htmlspecialchars($field['field_label']) ?>
                            <?php if ($field['is_required']): ?>
                                <span class="text-danger">*</span>
                            <?php endif; ?>
                        </label>

                        <?php switch ($field['field_type']): 
                            case 'textarea': ?>
                                <textarea class="form-control" 
                                          id="<?= $field['field_name'] ?>" 
                                          name="<?= $field['field_name'] ?>"
                                          placeholder="<?= htmlspecialchars($field['placeholder'] ?? '') ?>"
                                          <?= $field['is_required'] ? 'required' : '' ?>
                                          rows="4"></textarea>
                                <?php break; ?>
                                
                            <?php case 'select': ?>
                                <select class="form-select" 
                                        id="<?= $field['field_name'] ?>" 
                                        name="<?= $field['field_name'] ?>"
                                        <?= $field['is_required'] ? 'required' : '' ?>>
                                    <option value="">Selecione...</option>
                                    <?php foreach (explode("\n", $field['field_options']) as $option): ?>
                                        <option value="<?= htmlspecialchars(trim($option)) ?>">
                                            <?= htmlspecialchars(trim($option)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php break; ?>
                                
                            <?php case 'radio': ?>
                                <?php foreach (explode("\n", $field['field_options']) as $option): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="radio" 
                                               name="<?= $field['field_name'] ?>" 
                                               id="<?= $field['field_name'] . '_' . md5($option) ?>"
                                               value="<?= htmlspecialchars(trim($option)) ?>"
                                               <?= $field['is_required'] ? 'required' : '' ?>>
                                        <label class="form-check-label" 
                                               for="<?= $field['field_name'] . '_' . md5($option) ?>">
                                            <?= htmlspecialchars(trim($option)) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                                <?php break; ?>
                                
                            <?php case 'checkbox': ?>
                                <?php foreach (explode("\n", $field['field_options']) as $option): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="<?= $field['field_name'] ?>[]" 
                                               id="<?= $field['field_name'] . '_' . md5($option) ?>"
                                               value="<?= htmlspecialchars(trim($option)) ?>">
                                        <label class="form-check-label" 
                                               for="<?= $field['field_name'] . '_' . md5($option) ?>">
                                            <?= htmlspecialchars(trim($option)) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                                <?php break; ?>
                                
                            <?php default: ?>
                                <input type="<?= $field['field_type'] ?>" 
                                       class="form-control" 
                                       id="<?= $field['field_name'] ?>" 
                                       name="<?= $field['field_name'] ?>"
                                       placeholder="<?= htmlspecialchars($field['placeholder'] ?? '') ?>"
                                       <?= $field['is_required'] ? 'required' : '' ?>>
                        <?php endswitch; ?>

                        <?php if ($field['help_text']): ?>
                            <div class="form-text"><?= htmlspecialchars($field['help_text']) ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-paper-plane"></i> Enviar
                    </button>
                </div>
            </form>

            <?php if ($form['footer_text']): ?>
                <div class="form-footer">
                    <?= nl2br(htmlspecialchars($form['footer_text'])) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="<?= url('https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js') ?>"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Validação personalizada do formulário
        const form = document.getElementById('visitorForm');
        
        form.addEventListener('submit', function(event) {
            let hasError = false;
            
            // Validar campos obrigatórios
            form.querySelectorAll('[required]').forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    hasError = true;
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            // Validar e-mail
            const emailField = form.querySelector('input[type="email"]');
            if (emailField && emailField.value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailField.value)) {
                    emailField.classList.add('is-invalid');
                    hasError = true;
                }
            }
            
            // Validar telefone
            const phoneField = form.querySelector('input[type="tel"]');
            if (phoneField && phoneField.value) {
                const phoneRegex = /^\(\d{2}\) \d{4,5}-\d{4}$/;
                if (!phoneRegex.test(phoneField.value)) {
                    phoneField.classList.add('is-invalid');
                    hasError = true;
                }
            }
            
            if (hasError) {
                event.preventDefault();
                alert('Por favor, corrija os erros no formulário antes de enviar.');
            }
        });
        
        // Máscara para telefone
        const phoneField = form.querySelector('input[type="tel"]');
        if (phoneField) {
            phoneField.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length <= 11) {
                    value = value.replace(/^(\d{2})(\d)/g, '($1) $2');
                    value = value.replace(/(\d)(\d{4})$/, '$1-$2');
                    e.target.value = value;
                }
            });
        }
    });
    </script>
</body>
</html>
