<?php 
use App\Core\View;

if (isset($_SESSION['user_id'])): ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= View::url('/dashboard') ?>">
            <img src="<?= View::asset('img/logo_branca.png') ?>" alt="SeedsApp" height="30" style="max-width: 150px; height: auto;">
        </a>
        
        <div class="d-flex align-items-center">
            <!-- Botão de Tema -->
            <button id="theme-toggle" class="theme-toggle me-3" aria-label="Alternar tema">
                <i class="fas fa-moon"></i>
            </button>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?= is_active('/dashboard') ?>" href="<?= View::url('/dashboard') ?>">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= is_active('/visitors') ?>" href="<?= View::url('/visitors') ?>">
                        <i class="fas fa-user-friends"></i> Visitantes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= is_active('/groups') ?>" href="<?= View::url('/groups') ?>">
                        <i class="fas fa-users"></i> Grupos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= is_active('/ministries') ?>" href="<?= View::url('/ministries') ?>">
                        <i class="fas fa-church"></i> Ministérios
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= is_active('/reports') ?>" href="<?= View::url('/reports') ?>">
                        <i class="fas fa-chart-bar"></i> Relatórios
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= is_active('/prayers') ?>" href="<?= View::url('/prayers') ?>">
                        <i class="fas fa-pray"></i> Pedidos de Oração
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= is_active('/users') ?>" href="<?= View::url('/users') ?>">
                        <i class="fas fa-users-cog"></i> Usuários
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['user_name']) ?>&background=random" 
                             alt="<?= htmlspecialchars($_SESSION['user_name']) ?>"
                             class="rounded-circle me-1"
                             width="32"
                             height="32">
                        <span><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="<?= View::url('/profile') ?>">
                                <i class="fas fa-user me-2"></i>Perfil
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= View::url('/settings') ?>">
                                <i class="fas fa-cog me-2"></i>Configurações
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="<?= View::url('/logout') ?>">
                                <i class="fas fa-sign-out-alt me-2"></i>Sair
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<?php endif; ?>
