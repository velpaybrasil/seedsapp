<?php
$title = 'Redefinir Senha - ' . APP_NAME;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= $title ?></title>

    <!-- Custom fonts for this template-->
    <link href="/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
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
                                        <h1 class="h4 text-gray-900 mb-2">Redefinir sua Senha</h1>
                                        <p class="mb-4">Digite sua nova senha abaixo.</p>
                                    </div>

                                    <?php if (isset($flash_messages)): ?>
                                        <?php foreach ($flash_messages as $flash): ?>
                                            <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show mb-4" role="alert">
                                                <i class="fas fa-exclamation-circle mr-2"></i>
                                                <?= $flash['message'] ?>
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>

                                    <form class="user" action="/reset-password" method="POST">
                                        <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user" id="password" name="password"
                                                placeholder="Digite sua nova senha..." required minlength="8">
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user" id="password_confirm" name="password_confirm"
                                                placeholder="Confirme sua nova senha..." required minlength="8">
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            Salvar Nova Senha
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
    <script src="/vendor/jquery/jquery.min.js"></script>
    <script src="/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="/js/sb-admin-2.min.js"></script>
</body>
</html>
