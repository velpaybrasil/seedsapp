<!DOCTYPE html>
<html lang="pt-BR" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sistema de Gestão de Células">
    <meta name="author" content="Seeds App">
    <?= csrf_meta() ?>
    
    <title><?= $title ?? APP_NAME ?></title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="<?= asset('img/favicon.png') ?>" type="image/x-icon">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom styles -->
    <link href="<?= asset('css/auth.css') ?>" rel="stylesheet">
</head>

<body class="auth-page">
    <main class="auth-wrapper">
        <?php \App\Core\View::renderSection('content') ?>
    </main>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
