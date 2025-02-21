<?php require_once VIEWS_PATH . '/partials/header.php'; ?>

<div class="container-fluid">
    <div class="text-center">
        <div class="error mx-auto" data-text="500">500</div>
        <p class="lead text-gray-800 mb-5">Erro Interno do Servidor</p>
        <p class="text-gray-500 mb-0">Algo deu errado... Por favor, tente novamente mais tarde.</p>
        <?php if (APP_DEBUG && isset($error)): ?>
            <div class="alert alert-danger mt-4">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <a href="<?= url('dashboard') ?>">&larr; Voltar ao Dashboard</a>
    </div>
</div>

<?php require_once VIEWS_PATH . '/partials/footer.php'; ?>
