<!DOCTYPE html>
<html lang="pt-BR" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'GC Manager' ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Estilos Customizados -->
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
    
    <style>
        :root {
            --blue: #4e73df;
            --indigo: #6610f2;
            --purple: #6f42c1;
            --pink: #e83e8c;
            --red: #e74a3b;
            --orange: #fd7e14;
            --yellow: #f6c23e;
            --green: #1cc88a;
            --teal: #20c9a6;
            --cyan: #36b9cc;
            --white: #fff;
            --gray: #858796;
            --gray-dark: #5a5c69;
            --primary: #4e73df;
            --secondary: #858796;
            --success: #1cc88a;
            --info: #36b9cc;
            --warning: #f6c23e;
            --danger: #e74a3b;
            --light: #f8f9fc;
            --dark: #5a5c69;
            --breakpoint-xs: 0;
            --breakpoint-sm: 576px;
            --breakpoint-md: 768px;
            --breakpoint-lg: 992px;
            --breakpoint-xl: 1200px;
            --font-family-sans-serif: "Nunito",-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";
            --font-family-monospace: SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace;
        }
        
        body {
            background-color: #f8f9fc;
            font-family: Nunito,-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #858796;
            text-align: left;
        }
        
        .topbar {
            height: 4.375rem;
            position: fixed;
            top: 0;
            right: 0;
            left: 0;
            z-index: 1030;
        }
        
        .navbar {
            background-color: #fff;
            box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15)!important;
        }
        
        .navbar-brand img {
            height: 2.5rem;
            width: auto;
        }
        
        .navbar-dark .navbar-brand img {
            content: url('<?= asset('logo/logo_branca.png') ?>');
        }
        
        .navbar-light .navbar-brand img {
            content: url('<?= asset('logo/logo_azul.png') ?>');
        }
        
        .nav-item .nav-link {
            position: relative;
            color: #858796;
            padding: .75rem;
            display: flex;
            align-items: center;
        }
        
        .nav-item .nav-link:hover {
            color: #4e73df;
        }
        
        .nav-item .nav-link.active {
            color: #4e73df;
            font-weight: 700;
        }
        
        .nav-item .nav-link i {
            margin-right: .75rem;
            font-size: .85rem;
        }
        
        .main-content {
            padding-top: 6.375rem;
            padding-left: 1.5rem;
            padding-right: 1.5rem;
            padding-bottom: 1.5rem;
        }
        
        .shadow {
            box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15)!important;
        }
        
        .card {
            position: relative;
            display: flex;
            flex-direction: column;
            min-width: 0;
            word-wrap: break-word;
            background-color: #fff;
            background-clip: border-box;
            border: 1px solid #e3e6f0;
            border-radius: .35rem;
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            padding: .75rem 1.25rem;
            margin-bottom: 0;
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            border-radius: calc(.35rem - 1px) calc(.35rem - 1px) 0 0;
        }
        
        .card-body {
            flex: 1 1 auto;
            min-height: 1px;
            padding: 1.25rem;
        }
        
        .h3, h3 {
            font-size: 1.75rem;
            font-weight: 400;
            line-height: 1.2;
            color: #5a5c69;
        }
        
        .text-gray-800 {
            color: #5a5c69!important;
        }
        
        .btn-primary {
            color: #fff;
            background-color: #4e73df;
            border-color: #4e73df;
            padding: .25rem .5rem;
            font-size: .875rem;
            line-height: 1.5;
            border-radius: .2rem;
        }
        
        .btn-primary:hover {
            color: #fff;
            background-color: #2e59d9;
            border-color: #2653d4;
        }
        
        .btn-primary:focus {
            color: #fff;
            background-color: #2e59d9;
            border-color: #2653d4;
            box-shadow: 0 0 0 0.2rem rgba(105,136,228,.5);
        }
    </style>
</head>

<body>
    <?php if (isset($_SESSION['user_id'])): ?>
        <!-- Topbar -->
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <a class="navbar-brand" href="<?= BASE_URL ?>">
                    <img src="<?= asset('logo/logo_azul.png') ?>" alt="<?= APP_NAME ?>">
                </a>
                
                <div class="d-flex align-items-center">
                    <!-- Botão de Tema -->
                    <button id="theme-toggle" class="theme-toggle" aria-label="Alternar tema">
                        <i class="fas fa-moon"></i>
                    </button>
                    
                    <button class="navbar-toggler ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav me-auto">
                            <li class="nav-item">
                                <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/dashboard') ? 'active' : '' ?>" href="<?= BASE_URL ?>/dashboard">
                                    <i class="bi bi-speedometer2"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/groups') ? 'active' : '' ?>" href="<?= BASE_URL ?>/groups">
                                    <i class="bi bi-people"></i> Grupos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/visitors') ? 'active' : '' ?>" href="<?= BASE_URL ?>/visitors">
                                    <i class="bi bi-person-plus"></i> Visitantes
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/ministries') ? 'active' : '' ?>" href="<?= BASE_URL ?>/ministries">
                                    <i class="bi bi-building"></i> Ministérios
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/reports') ? 'active' : '' ?>" href="<?= BASE_URL ?>/reports">
                                    <i class="bi bi-graph-up"></i> Relatórios
                                </a>
                            </li>
                        </ul>
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link text-danger" href="<?= BASE_URL ?>/logout">
                                    <i class="bi bi-box-arrow-right"></i> Sair
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <?php if (isset($_SESSION['flash'])): ?>
                <div class="alert alert-<?= $_SESSION['flash']['type'] ?> alert-dismissible fade show shadow-sm" role="alert">
                    <?= $_SESSION['flash']['message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>

            <?= $content ?>
        </div>
    <?php else: ?>
        <?= $content ?>
    <?php endif; ?>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script src="<?= asset('js/theme.js') ?>"></script>
    
    <?php if (isset($scripts)): ?>
        <?php foreach ($scripts as $script): ?>
            <script src="<?= BASE_URL . $script ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
