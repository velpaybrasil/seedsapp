<?php require_once VIEWS_PATH . '/partials/header.php'; ?>

<div class="container-fluid">
    <div class="text-center">
        <div class="error mx-auto" data-text="404">404</div>
        <p class="lead text-gray-800 mb-5">Página Não Encontrada</p>
        <p class="text-gray-500 mb-0">Parece que você encontrou uma falha na matrix...</p>
        <a href="<?= url('dashboard') ?>">&larr; Voltar ao Dashboard</a>
    </div>
</div>

<?php require_once VIEWS_PATH . '/partials/footer.php'; ?>
