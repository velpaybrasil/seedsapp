<?php
session_start();

// Configurações do banco de dados
$dbHost = 'localhost';
$dbName = 'u315624178_gcmanager';
$dbUser = 'u315624178_gcmanager';
$dbPass = 'Gcm@2024';

try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    error_log("Erro de conexão: " . $e->getMessage());
    die("Erro ao conectar ao banco de dados");
}

// Processa o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validação básica
        if (empty($_POST['name'])) {
            throw new Exception('O nome é obrigatório');
        }

        // Prepara os dados
        $data = [
            'name' => $_POST['name'],
            'birth_date' => !empty($_POST['birth_date']) ? $_POST['birth_date'] : null,
            'marital_status' => !empty($_POST['marital_status']) ? $_POST['marital_status'] : null,
            'phone' => !empty($_POST['phone']) ? $_POST['phone'] : null,
            'whatsapp' => !empty($_POST['whatsapp']) ? $_POST['whatsapp'] : null,
            'email' => !empty($_POST['email']) ? $_POST['email'] : null,
            'address' => !empty($_POST['address']) ? $_POST['address'] : null,
            'number' => !empty($_POST['number']) ? $_POST['number'] : null,
            'complement' => !empty($_POST['complement']) ? $_POST['complement'] : null,
            'neighborhood' => !empty($_POST['neighborhood']) ? $_POST['neighborhood'] : null,
            'city' => !empty($_POST['city']) ? $_POST['city'] : null,
            'zipcode' => !empty($_POST['zipcode']) ? $_POST['zipcode'] : null,
            'gender' => !empty($_POST['gender']) ? $_POST['gender'] : null,
            'first_visit_date' => !empty($_POST['first_visit_date']) ? $_POST['first_visit_date'] : date('Y-m-d'),
            'how_knew_church' => !empty($_POST['how_knew_church']) ? $_POST['how_knew_church'] : null,
            'prayer_requests' => !empty($_POST['prayer_requests']) ? $_POST['prayer_requests'] : null,
            'observations' => !empty($_POST['observations']) ? $_POST['observations'] : null,
            'status' => 'not_contacted',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Prepara a query
        $fields = implode(', ', array_keys($data));
        $values = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO visitors ($fields) VALUES ($values)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_values($data));

        $_SESSION['success'] = 'Obrigado por se cadastrar! Em breve entraremos em contato.';
        header('Location: visitor-form.php');
        exit;

    } catch (Exception $e) {
        error_log("Erro ao salvar visitante: " . $e->getMessage());
        $_SESSION['error'] = 'Erro ao processar cadastro. Por favor, tente novamente.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Visitante</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
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
                <img src="assets/img/logo.png" alt="Logo da Igreja" class="church-logo">
                <h1 class="h3">Cadastro de Visitante</h1>
                <p class="text-muted">Seja bem-vindo! Por favor, preencha seus dados.</p>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['success'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $_SESSION['error'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <form action="visitor-form.php" method="post" class="needs-validation" novalidate>
                <!-- Informações Pessoais -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Informações Pessoais</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nome Completo *</label>
                                <input type="text" name="name" class="form-control" value="<?= $_POST['name'] ?? '' ?>" required>
                                <div class="invalid-feedback">Por favor, informe seu nome completo.</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Data de Nascimento</label>
                                <input type="date" name="birth_date" class="form-control" value="<?= $_POST['birth_date'] ?? '' ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Estado Civil</label>
                                <select name="marital_status" class="form-select">
                                    <option value="">Selecione...</option>
                                    <option value="single" <?= isset($_POST['marital_status']) && $_POST['marital_status'] === 'single' ? 'selected' : '' ?>>Solteiro(a)</option>
                                    <option value="married" <?= isset($_POST['marital_status']) && $_POST['marital_status'] === 'married' ? 'selected' : '' ?>>Casado(a)</option>
                                    <option value="divorced" <?= isset($_POST['marital_status']) && $_POST['marital_status'] === 'divorced' ? 'selected' : '' ?>>Divorciado(a)</option>
                                    <option value="widowed" <?= isset($_POST['marital_status']) && $_POST['marital_status'] === 'widowed' ? 'selected' : '' ?>>Viúvo(a)</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Gênero</label>
                                <select name="gender" class="form-select">
                                    <option value="">Selecione...</option>
                                    <option value="M" <?= isset($_POST['gender']) && $_POST['gender'] === 'M' ? 'selected' : '' ?>>Masculino</option>
                                    <option value="F" <?= isset($_POST['gender']) && $_POST['gender'] === 'F' ? 'selected' : '' ?>>Feminino</option>
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
                                <input type="tel" name="phone" class="form-control phone-mask" value="<?= $_POST['phone'] ?? '' ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">WhatsApp</label>
                                <input type="tel" name="whatsapp" class="form-control phone-mask" value="<?= $_POST['whatsapp'] ?? '' ?>">
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label class="form-label">E-mail</label>
                                <input type="email" name="email" class="form-control" value="<?= $_POST['email'] ?? '' ?>">
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
                                <input type="text" name="zipcode" class="form-control cep-mask" value="<?= $_POST['zipcode'] ?? '' ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cidade</label>
                                <input type="text" name="city" class="form-control" value="<?= $_POST['city'] ?? '' ?>">
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Endereço</label>
                                <input type="text" name="address" class="form-control" value="<?= $_POST['address'] ?? '' ?>">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Número</label>
                                <input type="text" name="number" class="form-control" value="<?= $_POST['number'] ?? '' ?>">
                            </div>
                            
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Complemento</label>
                                <input type="text" name="complement" class="form-control" value="<?= $_POST['complement'] ?? '' ?>">
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Bairro</label>
                                <input type="text" name="neighborhood" class="form-control" value="<?= $_POST['neighborhood'] ?? '' ?>">
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
                                <input type="date" name="first_visit_date" class="form-control" value="<?= $_POST['first_visit_date'] ?? date('Y-m-d') ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Como conheceu a igreja?</label>
                                <input type="text" name="how_knew_church" class="form-control" value="<?= $_POST['how_knew_church'] ?? '' ?>">
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Pedidos de Oração</label>
                                <textarea name="prayer_requests" class="form-control" rows="3"><?= $_POST['prayer_requests'] ?? '' ?></textarea>
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Observações</label>
                                <textarea name="observations" class="form-control" rows="3"><?= $_POST['observations'] ?? '' ?></textarea>
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
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
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
