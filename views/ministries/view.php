<?php
/**
 * @var array $ministry
 * @var array $volunteers
 */
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Detalhes do Ministério</h1>
        <div>
            <a href="<?= url("ministries/{$ministry['id']}/edit") ?>" class="btn btn-primary">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="<?= url('ministries') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informações do Ministério</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong>Nome:</strong>
                        </div>
                        <div class="col-md-9">
                            <?= htmlspecialchars($ministry['name']) ?>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong>Status:</strong>
                        </div>
                        <div class="col-md-9">
                            <span class="badge bg-<?= $ministry['status'] === 'active' ? 'success' : 'danger' ?>">
                                <?= $ministry['status'] === 'active' ? 'Ativo' : 'Inativo' ?>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Descrição:</strong>
                        </div>
                        <div class="col-md-9">
                            <?= nl2br(htmlspecialchars($ministry['description'])) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Voluntários</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($volunteers)): ?>
                        <ul class="list-group">
                            <?php foreach ($volunteers as $volunteer): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?= htmlspecialchars($volunteer['name']) ?>
                                    <span class="badge bg-primary rounded-pill"><?= $volunteer['role'] ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted mb-0">Nenhum voluntário cadastrado.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
