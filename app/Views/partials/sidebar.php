<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= url('dashboard') ?>">
        <div class="sidebar-brand-icon">
            <i class="fas fa-layer-group"></i>
        </div>
        <div class="sidebar-brand-text mx-3"><?= APP_NAME ?></div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item <?= strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false ? 'active' : '' ?>">
        <a class="nav-link" href="<?= url('dashboard') ?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Nav Item - Groups -->
    <li class="nav-item <?= strpos($_SERVER['REQUEST_URI'], '/groups') !== false ? 'active' : '' ?>">
        <a class="nav-link" href="<?= url('groups') ?>">
            <i class="fas fa-fw fa-users"></i>
            <span>Grupos</span>
        </a>
    </li>

    <!-- Nav Item - Visitors -->
    <li class="nav-item <?= strpos($_SERVER['REQUEST_URI'], '/visitors') !== false ? 'active' : '' ?>">
        <a class="nav-link" href="<?= url('visitors') ?>">
            <i class="fas fa-fw fa-user-friends"></i>
            <span>Visitantes</span>
        </a>
    </li>

    <!-- Nav Item - Reports -->
    <li class="nav-item <?= strpos($_SERVER['REQUEST_URI'], '/reports') !== false ? 'active' : '' ?>">
        <a class="nav-link" href="<?= url('reports') ?>">
            <i class="fas fa-fw fa-chart-area"></i>
            <span>Relatórios</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
