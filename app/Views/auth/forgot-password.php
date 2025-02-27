<?php
$title = 'Recuperar Senha - ' . APP_NAME;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= $title ?></title>

    <!-- Custom fonts for this template-->
    <link href="<?= asset('vendor/fontawesome-free/css/all.min.css') ?>" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="<?= asset('css/sb-admin-2.min.css') ?>" rel="stylesheet">
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
</head>

<body class="bg-gradient-primary">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-password-image"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-2">Esqueceu sua Senha?</h1>
                                        <p class="mb-4">Digite seu endereço de e-mail abaixo e enviaremos um link para redefinir sua senha.</p>
                                    </div>

                                    <?php foreach (\App\Core\View::getFlashMessages() as $flash): ?>
                                        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show mb-4" role="alert">
                                            <i class="fas fa-exclamation-circle mr-2"></i>
                                            <?= $flash['message'] ?>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    <?php endforeach; ?>

                                    <form class="user" action="/forgot-password" method="POST">
                                        <div class="form-group">
                                            <input type="email" class="form-control form-control-user" id="email" name="email"
                                                placeholder="Digite seu endereço de e-mail..." required>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            Redefinir Senha
                                        </button>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="/login">Voltar para o Login</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="<?= asset('vendor/jquery/jquery.min.js') ?>"></script>
    <script src="<?= asset('vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

    <!-- Core plugin JavaScript-->
    <script src="<?= asset('vendor/jquery-easing/jquery.easing.min.js') ?>"></script>

    <!-- Custom scripts for all pages-->
    <script src="<?= asset('js/sb-admin-2.min.js') ?>"></script>
</body>
</html>
