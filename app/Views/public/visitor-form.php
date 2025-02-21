<?php
$title = 'Cadastro de Visitante';
$styles = '<link href="/gcmanager/assets/css/visitors.css" rel="stylesheet">';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <?= $styles ?>
    
    <style>
        body {
            background-color: #f8f9fa;
        }
        .form-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .church-logo {
            max-width: 200px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <div class="text-center mb-4">
                <img src="/gcmanager/assets/img/logo.png" alt="Logo da Igreja" class="church-logo">
                <h1 class="h3">Cadastro de Visitante</h1>
                <p class="text-muted">Seja bem-vindo! Por favor, preencha seus dados.</p>
            </div>

            <?php if (isset($_SESSION['flash'])): ?>
                <?php foreach ($_SESSION['flash'] as $flash): ?>
                    <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
                        <?= $flash['message'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endforeach; ?>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>

            <form action="/gcmanager/public/visitor-form/store" method="post" class="needs-validation" novalidate>
                <!-- Informações Pessoais -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Informações Pessoais</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nome Completo *</label>
                                <input type="text" name="name" class="form-control" value="<?= $data['name'] ?? '' ?>" required>
                                <div class="invalid-feedback">Por favor, informe seu nome completo.</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Data de Nascimento</label>
                                <input type="date" name="birth_date" class="form-control" value="<?= $data['birth_date'] ?? '' ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Estado Civil</label>
                                <select name="marital_status" class="form-select">
                                    <option value="">Selecione...</option>
                                    <option value="single" <?= isset($data['marital_status']) && $data['marital_status'] === 'single' ? 'selected' : '' ?>>Solteiro(a)</option>
                                    <option value="married" <?= isset($data['marital_status']) && $data['marital_status'] === 'married' ? 'selected' : '' ?>>Casado(a)</option>
                                    <option value="divorced" <?= isset($data['marital_status']) && $data['marital_status'] === 'divorced' ? 'selected' : '' ?>>Divorciado(a)</option>
                                    <option value="widowed" <?= isset($data['marital_status']) && $data['marital_status'] === 'widowed' ? 'selected' : '' ?>>Viúvo(a)</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Gênero</label>
                                <select name="gender" class="form-select">
                                    <option value="">Selecione...</option>
                                    <option value="M" <?= isset($data['gender']) && $data['gender'] === 'M' ? 'selected' : '' ?>>Masculino</option>
                                    <option value="F" <?= isset($data['gender']) && $data['gender'] === 'F' ? 'selected' : '' ?>>Feminino</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contato -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Informações de Contato</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Telefone</label>
                                <input type="tel" name="phone" class="form-control phone-mask" value="<?= $data['phone'] ?? '' ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">WhatsApp</label>
                                <input type="tel" name="whatsapp" class="form-control phone-mask" value="<?= $data['whatsapp'] ?? '' ?>">
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label class="form-label">E-mail</label>
                                <input type="email" name="email" class="form-control" value="<?= $data['email'] ?? '' ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Endereço -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Endereço</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">CEP</label>
                                <input type="text" name="zipcode" class="form-control cep-mask" value="<?= $data['zipcode'] ?? '' ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cidade</label>
                                <input type="text" name="city" class="form-control" value="<?= $data['city'] ?? '' ?>">
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Endereço</label>
                                <input type="text" name="address" class="form-control" value="<?= $data['address'] ?? '' ?>">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Número</label>
                                <input type="text" name="number" class="form-control" value="<?= $data['number'] ?? '' ?>">
                            </div>
                            
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Complemento</label>
                                <input type="text" name="complement" class="form-control" value="<?= $data['complement'] ?? '' ?>">
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Bairro</label>
                                <input type="text" name="neighborhood" class="form-control" value="<?= $data['neighborhood'] ?? '' ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informações da Igreja -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Informações Adicionais</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Data da Primeira Visita</label>
                                <input type="date" name="first_visit_date" class="form-control" value="<?= $data['first_visit_date'] ?? '' ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Como conheceu a igreja?</label>
                                <input type="text" name="how_knew_church" class="form-control" value="<?= $data['how_knew_church'] ?? '' ?>">
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Pedidos de Oração</label>
                                <textarea name="prayer_requests" class="form-control" rows="3"><?= $data['prayer_requests'] ?? '' ?></textarea>
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Observações</label>
                                <textarea name="observations" class="form-control" rows="3"><?= $data['observations'] ?? '' ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-paper-plane me-2"></i>Enviar Cadastro
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery Mask Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    
    <script>
        // Form validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()

        // Input masks
        $(document).ready(function(){
            $('.phone-mask').mask('(00) 00000-0000');
            $('.cep-mask').mask('00000-000');
        });
    </script>
</body>
</html>
