<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'GC Manager' ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        .sidebar {
            min-height: 100vh;
            background: #0d6efd;
            padding: 20px;
        }
        .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 8px 16px;
            border-radius: 4px;
            margin-bottom: 5px;
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            color: #fff;
            background: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }
        .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        .nav-link i {
            margin-right: 8px;
        }
        .main-content {
            padding: 20px;
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .card {
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border: none;
            border-radius: 8px;
        }
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #eee;
            padding: 15px 20px;
            border-radius: 8px 8px 0 0;
        }
        .card-body {
            padding: 20px;
        }
        .logo-container {
            text-align: center;
            padding: 20px 0;
            margin-bottom: 20px;
        }
        .logo-container img {
            max-width: 180px;
            height: auto;
        }
    </style>
</head>

<body class="bg-light">
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="container-fluid">
            <div class="row g-0">
                <!-- Sidebar -->
                <div class="col-md-2 sidebar">
                    <div class="logo-container">
                        <img src="<?= asset('logo/GCMANAGER-LOGO-BRANCA.png') ?>" alt="<?= APP_NAME ?>" class="img-fluid">
                    </div>
                    <nav class="nav flex-column">
                        <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/dashboard') ? 'active' : '' ?>" href="<?= BASE_URL ?>/dashboard">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                        <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/groups') ? 'active' : '' ?>" href="<?= BASE_URL ?>/groups">
                            <i class="bi bi-people"></i> Grupos
                        </a>
                        <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/visitors') ? 'active' : '' ?>" href="<?= BASE_URL ?>/visitors">
                            <i class="bi bi-person-plus"></i> Visitantes
                        </a>
                        <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/reports') ? 'active' : '' ?>" href="<?= BASE_URL ?>/reports">
                            <i class="bi bi-graph-up"></i> Relatórios
                        </a>
                        <hr class="my-3">
                        <a class="nav-link text-danger" href="<?= BASE_URL ?>/logout">
                            <i class="bi bi-box-arrow-right"></i> Sair
                        </a>
                    </nav>
                </div>

                <!-- Main Content -->
                <div class="col-md-10 main-content">
                    <?php if (isset($_SESSION['flash'])): ?>
                        <div class="alert alert-<?= $_SESSION['flash']['type'] ?> alert-dismissible fade show" role="alert">
                            <?= $_SESSION['flash']['message'] ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['flash']); ?>
                    <?php endif; ?>

                    <?= $content ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <?= $content ?>
    <?php endif; ?>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <?php if (isset($scripts)): ?>
        <?php foreach ($scripts as $script): ?>
            <script src="<?= BASE_URL . $script ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
