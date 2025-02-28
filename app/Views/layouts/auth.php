<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= isset($title) ? $title : APP_NAME ?> - Login</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom styles -->
    <link href="<?= asset('css/auth.css') ?>" rel="stylesheet">
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">

    <style>
        .bg-login-image {
            background: url('https://source.unsplash.com/K4mSJ7kc0As/600x800');
            background-position: center;
            background-size: cover;
        }
    </style>
</head>

<body class="bg-gradient-primary">
    <div class="container">
        <?php foreach (\App\Core\View::getFlashMessages() as $flash): ?>
            <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show mt-4" role="alert">
                <?= $flash['message'] ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endforeach; ?>

        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-11 col-sm-8 col-md-6 col-lg-5 col-xl-4">
                <?= $content ?>
            </div>
        </div>

        <div class="text-center mt-4">
            <p class="text-white">Copyright &copy; <?= APP_NAME ?> <?= date('Y') ?></p>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom scripts -->
    <script src="<?= asset('js/auth.js') ?>"></script>
    <script src="<?= asset('js/script.js') ?>"></script>
</body>
</html>
