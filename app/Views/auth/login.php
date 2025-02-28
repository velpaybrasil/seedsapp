<?php \App\Core\View::section('content') ?>
<div class="auth-card">
    <div class="auth-header">
        <img src="<?= asset('img/logo.png') ?>" alt="<?= APP_NAME ?>" class="auth-logo floating">
        <h4 class="text-primary mb-2">Bem-vindo ao <?= APP_NAME ?></h4>
        <p class="text-muted">Faça login para continuar</p>
    </div>

    <?php if (isset($_SESSION['flash'])): ?>
        <div class="alert alert-<?= $_SESSION['flash']['type'] ?> alert-dismissible fade show mx-3" role="alert">
            <?= $_SESSION['flash']['message'] ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="auth-form">
        <form action="<?= url('login') ?>" method="POST" id="loginForm" novalidate>
            <?= csrf_field() ?>
            
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-envelope"></i>
                    </span>
                    <input type="email" 
                           class="form-control <?= isset($_SESSION['errors']['email']) ? 'is-invalid' : '' ?>" 
                           id="email" 
                           name="email" 
                           value="<?= old('email') ?>"
                           required 
                           autofocus
                           autocomplete="email"
                           placeholder="seu@email.com">
                    <div class="invalid-feedback">
                        <?= isset($_SESSION['errors']['email']) ? $_SESSION['errors']['email'] : 'Por favor, insira um e-mail válido.' ?>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="password" class="form-label mb-0">Senha</label>
                    <a href="<?= url('forgot-password') ?>" class="small">
                        Esqueceu a senha?
                    </a>
                </div>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" 
                           class="form-control <?= isset($_SESSION['errors']['password']) ? 'is-invalid' : '' ?>" 
                           id="password" 
                           name="password" 
                           required
                           autocomplete="current-password"
                           placeholder="Sua senha">
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                        <i class="bi bi-eye"></i>
                    </button>
                    <div class="invalid-feedback">
                        <?= isset($_SESSION['errors']['password']) ? $_SESSION['errors']['password'] : 'Por favor, insira sua senha.' ?>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label user-select-none" for="remember">
                        Lembrar-me
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-3" id="submitButton">
                <span class="d-flex align-items-center justify-content-center">
                    <i class="bi bi-box-arrow-in-right me-2"></i>
                    <span>Entrar</span>
                    <div class="spinner-border spinner-border-sm ms-2 d-none" role="status" id="submitSpinner">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                </span>
            </button>

            <a href="https://wa.me/5585997637850" target="_blank" class="btn btn-outline-secondary w-100 mb-4">
                <span class="d-flex align-items-center justify-content-center">
                    <i class="bi bi-whatsapp me-2"></i>
                    <span>Suporte via WhatsApp</span>
                </span>
            </a>

            <div class="text-center text-muted small">
                &copy; <?= date('Y') ?> <?= APP_NAME ?>. Todos os direitos reservados.
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    const submitButton = document.getElementById('submitButton');
    const submitSpinner = document.getElementById('submitSpinner');
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    // Toggle password visibility
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        togglePassword.querySelector('i').classList.toggle('bi-eye');
        togglePassword.querySelector('i').classList.toggle('bi-eye-slash');
    });

    // Form submission
    form.addEventListener('submit', function() {
        submitButton.setAttribute('disabled', 'disabled');
        submitSpinner.classList.remove('d-none');
    });
});
</script>
<?php \App\Core\View::endSection() ?>
