<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid">
    <div class="row justify-content-center align-items-center" style="min-height: calc(100vh - 60px);">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body text-center p-5">
                    <div class="display-1 text-primary fw-bold mb-4" style="font-size: 8rem;">404</div>
                    <h1 class="h3 mb-4">Página Não Encontrada</h1>
                    <p class="text-muted mb-4">
                        Parece que você está perdido no caminho...<br>
                        Mas não se preocupe, há sempre um caminho de volta!
                    </p>
                    <p class="text-primary fst-italic mb-5">
                        "Eu sou o caminho, a verdade e a vida." (João 14:6)
                    </p>
                    <a href="/gcmanager/dashboard" class="btn btn-primary">
                        <i class="bi bi-house-door me-2"></i>
                        Voltar ao Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
