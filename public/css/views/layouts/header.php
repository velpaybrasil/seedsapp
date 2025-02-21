<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>GC Manager - Sistema de Gestão de Grupos</title>
    <link href="/gcmanager/public/assets/css/styles.css" rel="stylesheet" />
    <link href="/gcmanager/public/assets/css/custom.css" rel="stylesheet" />
    <?= isset($styles) ? $styles : '' ?>
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom styles for DataTables -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 3.5rem;
        }
        .top-bar {
            background: #fff;
            box-shadow: 0 1px 2px rgba(0,0,0,0.075);
            padding: 0.75rem 1rem;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        .top-bar-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .nav-menu {
            display: flex;
            gap: 1.5rem;
            align-items: center;
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .nav-menu a {
            color: #6c757d;
            text-decoration: none;
            font-size: 0.9rem;
            padding: 0.25rem 0;
            position: relative;
        }
        .nav-menu a:hover {
            color: #2c3e50;
        }
        .nav-menu a.active {
            color: #2c3e50;
        }
        .nav-menu a.active:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background-color: #2c3e50;
        }
        .user-info {
            color: #6c757d;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-left: 1.5rem;
        }
        .user-info i {
            color: #6c757d;
        }
        main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1.5rem;
        }
        .btn-primary {
            background-color: #2c3e50;
            border-color: #2c3e50;
        }
        .btn-primary:hover {
            background-color: #34495e;
            border-color: #34495e;
        }
        .card {
            border: none;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="top-bar">
        <div class="top-bar-content">
            <ul class="nav-menu">
                <li>
                    <a href="/gcmanager/public/dashboard" class="<?= strpos($_SERVER['REQUEST_URI'], '/gcmanager/public/dashboard') !== false ? 'active' : '' ?>">
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="/gcmanager/public/groups" class="<?= strpos($_SERVER['REQUEST_URI'], '/gcmanager/public/groups') !== false ? 'active' : '' ?>">
                        Grupos
                    </a>
                </li>
                <li>
                    <a href="/gcmanager/public/visitors" class="<?= strpos($_SERVER['REQUEST_URI'], '/gcmanager/public/visitors') !== false ? 'active' : '' ?>">
                        Visitantes
                    </a>
                </li>
                <li>
                    <a href="/gcmanager/public/reports" class="<?= strpos($_SERVER['REQUEST_URI'], '/gcmanager/public/reports') !== false ? 'active' : '' ?>">
                        Relatórios
                    </a>
                </li>
            </ul>
            
            <div class="d-flex align-items-center">
                <?php if (strpos($_SERVER['REQUEST_URI'], '/gcmanager/public/groups') !== false && !strpos($_SERVER['REQUEST_URI'], '/gcmanager/public/groups/create') && !strpos($_SERVER['REQUEST_URI'], '/gcmanager/public/groups/edit')): ?>
                    <a href="/gcmanager/public/groups/create" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Novo Grupo
                    </a>
                <?php endif; ?>
                
                <div class="user-info">
                    <i class="fas fa-user-circle"></i>
                    <?= $_SESSION['user_name'] ?? 'Usuário' ?>
                </div>
            </div>
        </div>
    </div>
    
    <main>
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?> alert-dismissible fade show mb-4" role="alert">
                <?= $_SESSION['flash_message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
        <?php endif; ?>
