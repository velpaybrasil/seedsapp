<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'GC Manager - Sistema de Gestão de Grupos' ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/styles.css?v=<?= time() ?>">
    
    <?php if (isset($styles) && is_array($styles)): ?>
        <?php foreach ($styles as $style): ?>
            <link href="<?= $style ?>" rel="stylesheet" />
        <?php endforeach; ?>
    <?php endif; ?>
    
    <script>
        // Verificar se o CSS está sendo carregado
        window.addEventListener('load', function() {
            const styleSheet = document.querySelector('link[href*="<?= BASE_URL ?>/assets/css/styles.css"]');
            if (styleSheet) {
                console.log('CSS carregado com sucesso:', styleSheet.href);
                // Verificar se os estilos estão sendo aplicados
                const body = document.querySelector('body');
                const styles = window.getComputedStyle(body);
                console.log('Border top:', styles.borderTopWidth, styles.borderTopColor);
            } else {
                console.error('CSS não encontrado');
            }
        });
    </script>
</head>
<body class="app-layout">
    <!-- Top Bar -->
    <nav class="top-bar">
        <div class="top-bar-content">
            <!-- Logo -->
            <a href="<?= BASE_URL ?>/" class="logo">
                <img src="<?= BASE_URL ?>/assets/img/logo_azul.png" alt="GC Manager" height="32">
            </a>

            <!-- Main Navigation -->
            <div class="main-nav">
                <ul class="nav-menu">
                    <li><a href="<?= BASE_URL ?>/dashboard" class="<?= $currentPage === 'dashboard' ? 'active' : '' ?>">Dashboard</a></li>
                    <li><a href="<?= BASE_URL ?>/groups" class="<?= $currentPage === 'groups' ? 'active' : '' ?>">Grupos</a></li>
                    <li><a href="<?= BASE_URL ?>/visitors" class="<?= $currentPage === 'visitors' ? 'active' : '' ?>">Visitantes</a></li>
                    <li><a href="<?= BASE_URL ?>/reports" class="<?= $currentPage === 'reports' ? 'active' : '' ?>">Relatórios</a></li>
                </ul>
            </div>

            <!-- User Menu -->
            <div class="user-menu">
                <div class="notifications">
                    <button class="btn btn-icon" data-bs-toggle="dropdown">
                        <i class="bi bi-bell"></i>
                        <?php if (isset($notificationCount) && $notificationCount > 0): ?>
                            <span class="badge"><?= $notificationCount ?></span>
                        <?php endif; ?>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <div class="dropdown-header">Notificações</div>
                        <?php if (empty($notifications)): ?>
                            <div class="dropdown-item text-muted">Nenhuma notificação</div>
                        <?php else: ?>
                            <?php foreach ($notifications as $notification): ?>
                                <a href="<?= $notification['link'] ?>" class="dropdown-item">
                                    <div class="notification-icon <?= $notification['type'] ?>">
                                        <i class="bi bi-<?= $notification['icon'] ?>"></i>
                                    </div>
                                    <div class="notification-content">
                                        <div class="notification-text"><?= $notification['text'] ?></div>
                                        <div class="notification-time"><?= $notification['time'] ?></div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="user-profile">
                    <button class="btn btn-icon" data-bs-toggle="dropdown">
                        <div class="avatar">
                            <?php if (isset($user['avatar']) && $user['avatar']): ?>
                                <img src="<?= $user['avatar'] ?>" alt="<?= $user['name'] ?>">
                            <?php else: ?>
                                <div class="avatar-initial"><?= isset($user['name']) ? strtoupper(substr($user['name'], 0, 1)) : 'U' ?></div>
                            <?php endif; ?>
                        </div>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <div class="dropdown-header">
                            <div class="fw-bold"><?= $user['name'] ?? 'Usuário' ?></div>
                            <div class="text-muted small"><?= $user['email'] ?? '' ?></div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="<?= BASE_URL ?>/profile" class="dropdown-item">
                            <i class="bi bi-person me-2"></i> Meu Perfil
                        </a>
                        <a href="<?= BASE_URL ?>/settings" class="dropdown-item">
                            <i class="bi bi-gear me-2"></i> Configurações
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="<?= BASE_URL ?>/logout" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i> Sair
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?> alert-dismissible fade show" role="alert">
                <?= $_SESSION['flash_message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
        <?php endif; ?>
        <?= $content ?? '' ?>
    </main>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php if (isset($scripts) && is_array($scripts)): ?>
        <?php foreach ($scripts as $script): ?>
            <script src="<?= $script ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
