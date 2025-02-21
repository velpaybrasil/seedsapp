<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GCManager - <?= $title ?? 'Gestão de Igreja' ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= url('assets/img/favicon.png') ?>">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="<?= url('assets/css/style.css') ?>" rel="stylesheet">
    
    <!-- Custom CSS -->
    <?php if (isset($styles)) echo $styles; ?>
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= url('dashboard') ?>">
                <img src="<?= url('assets/img/logo.png') ?>" alt="GCManager" height="30">
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('dashboard') ?>">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('visitors') ?>">
                                <i class="fas fa-user-friends"></i> Visitantes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('groups') ?>">
                                <i class="fas fa-users"></i> Grupos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('ministries') ?>">
                                <i class="fas fa-church"></i> Ministérios
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="fas fa-hand-holding-usd"></i> Financeiro
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="<?= url('financial') ?>">
                                        <i class="fas fa-list"></i> Transações
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?= url('financial/categories') ?>">
                                        <i class="fas fa-tags"></i> Categorias
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?= url('financial/tithes-offerings') ?>">
                                        <i class="fas fa-gift"></i> Dízimos e Ofertas
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?= url('financial/reports') ?>">
                                        <i class="fas fa-chart-bar"></i> Relatórios
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('messages') ?>">
                                <i class="fas fa-envelope"></i> Mensagens
                                <span class="badge bg-danger unread-count" style="display: none;"></span>
                            </a>
                        </li>
                    </ul>
                    
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle"></i> <?= $_SESSION['user_name'] ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="<?= url('profile') ?>">
                                        <i class="fas fa-user"></i> Meu Perfil
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?= url('settings') ?>">
                                        <i class="fas fa-cog"></i> Configurações
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="<?= url('logout') ?>">
                                        <i class="fas fa-sign-out-alt"></i> Sair
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="container-fluid py-4">
        <!-- Flash Messages -->
        <?php if (isset($_SESSION['flash'])): ?>
            <div class="alert alert-<?= $_SESSION['flash']['type'] ?> alert-dismissible fade show" role="alert">
                <?= $_SESSION['flash']['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>
        
        <!-- Page Content -->
        <?= $content ?>
    </main>
    
    <!-- Footer -->
    <footer class="bg-white py-4 mt-auto border-top">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6 text-center text-lg-start mb-3 mb-lg-0">
                    &copy; <?= date('Y') ?> GCManager. Todos os direitos reservados.
                </div>
                <div class="col-lg-6 text-center text-lg-end">
                    <a href="<?= url('terms') ?>" class="text-muted me-3">Termos de Uso</a>
                    <a href="<?= url('privacy') ?>" class="text-muted me-3">Política de Privacidade</a>
                    <a href="<?= url('help') ?>" class="text-muted">Ajuda</a>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pt.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="<?= url('assets/js/app.js') ?>"></script>
    
    <!-- Custom Scripts -->
    <?php if (isset($scripts)) echo $scripts; ?>
    
    <script>
        // Inicializa componentes
        $(document).ready(function() {
            // Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
            
            // Flatpickr (Calendário)
            $('.datepicker').flatpickr({
                locale: 'pt',
                dateFormat: 'd/m/Y'
            });
            
            $('.datetimepicker').flatpickr({
                locale: 'pt',
                dateFormat: 'd/m/Y H:i',
                enableTime: true,
                time_24hr: true
            });
            
            // Tooltips
            const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltips.forEach(tooltip => new bootstrap.Tooltip(tooltip));
            
            // Atualiza contador de mensagens não lidas
            function updateUnreadCount() {
                $.get('<?= url('messages/unread-count') ?>', function(data) {
                    const count = data.count;
                    const badge = $('.unread-count');
                    
                    if (count > 0) {
                        badge.text(count).show();
                    } else {
                        badge.hide();
                    }
                });
            }
            
            // Atualiza a cada 5 minutos
            updateUnreadCount();
            setInterval(updateUnreadCount, 5 * 60 * 1000);
            
            // Confirmação de exclusão
            $('.btn-delete').click(function(e) {
                e.preventDefault();
                
                const url = $(this).attr('href');
                
                Swal.fire({
                    title: 'Tem certeza?',
                    text: 'Esta ação não poderá ser desfeita!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sim, excluir!',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });
    </script>
</body>
</html>
