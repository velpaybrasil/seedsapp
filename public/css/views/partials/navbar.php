<?php if (isset($_SESSION['user_id'])): ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="<?= url('/dashboard') ?>">
            <img src="<?= asset('logo/GCMANAGER-LOGO.png') ?>" alt="<?= APP_NAME ?>" height="30">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?= url('/dashboard') ?>">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= url('/visitors') ?>">
                        <i class="bi bi-people"></i> Visitantes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= url('/groups') ?>">
                        <i class="bi bi-collection"></i> Grupos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= url('/reports') ?>">
                        <i class="bi bi-graph-up"></i> Relatórios
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> <?= $_SESSION['user_name'] ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="<?= url('/profile') ?>">
                                <i class="bi bi-person"></i> Perfil
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="<?= url('/logout') ?>">
                                <i class="bi bi-box-arrow-right"></i> Sair
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<?php endif; ?>
