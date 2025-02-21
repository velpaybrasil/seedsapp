<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <img src="<?= asset('img/logo.png') ?>" alt="<?= APP_NAME ?>" class="img-fluid mb-4" style="max-height: 100px;">
                        <h4 class="text-primary">Bem-vindo ao <?= APP_NAME ?></h4>
                        <p class="text-muted">Faça login para continuar</p>
                    </div>

                    <?php if (isset($_SESSION['flash'])): ?>
                        <div class="alert alert-<?= $_SESSION['flash']['type'] ?> alert-dismissible fade show" role="alert">
                            <?= $_SESSION['flash']['message'] ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?= url('login') ?>" method="POST">
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
                                       placeholder="seu@email.com">
                                <?php if (isset($_SESSION['errors']['email'])): ?>
                                    <div class="invalid-feedback">
                                        <?= $_SESSION['errors']['email'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between">
                                <label for="password" class="form-label">Senha</label>
                                <a href="<?= url('forgot-password') ?>" class="text-decoration-none small">
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
                                       placeholder="Sua senha">
                                <?php if (isset($_SESSION['errors']['password'])): ?>
                                    <div class="invalid-feedback">
                                        <?= $_SESSION['errors']['password'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">
                                    Lembrar-me
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            Entrar
                        </button>
                    </form>
                </div>
            </div>

            <div class="text-center mt-4">
                <p class="text-muted mb-0">
                    &copy; <?= date('Y') ?> <?= APP_NAME ?>. Todos os direitos reservados.
                </p>
            </div>
        </div>
    </div>
</div>
