<?php
$title = 'Ministérios';
?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Ministérios</h1>
        <div class="d-flex gap-2">
            <a href="<?= url('ministries/create') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo Ministério
            </a>
        </div>
    </div>

    <?php if (isset($flash)): ?>
        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
            <?= $flash['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Search Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control form-control-sm" 
                           placeholder="Buscar por nome..." 
                           value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-control form-control-sm">
                        <option value="">Todos os Status</option>
                        <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Ativo</option>
                        <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inativo</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Líderes</th>
                            <th>Grupos</th>
                            <th>Participantes</th>
                            <th>Status</th>
                            <th width="100">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($ministries)): ?>
                            <?php foreach ($ministries as $ministry): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <h6 class="mb-0"><?= htmlspecialchars($ministry['name'] ?? '') ?></h6>
                                                <?php if (!empty($ministry['description'])): ?>
                                                    <small class="text-muted"><?= htmlspecialchars($ministry['description']) ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($ministry['leaders']) ?></td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?= $ministry['total_groups'] ?> grupo<?= $ministry['total_groups'] != 1 ? 's' : '' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">
                                            <?= $ministry['total_participants'] ?> pessoa<?= $ministry['total_participants'] != 1 ? 's' : '' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?= isset($ministry['active']) && $ministry['active'] ? 'bg-success' : 'bg-warning' ?>">
                                            <?= isset($ministry['active']) && $ministry['active'] ? 'Ativo' : 'Inativo' ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="<?= BASE_URL ?>/ministries/edit/<?= $ministry['id'] ?>" 
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger delete-ministry" 
                                                    data-id="<?= $ministry['id'] ?>"
                                                    data-name="<?= htmlspecialchars($ministry['name']) ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">
                                    <div class="text-muted py-4">
                                        <i class="fas fa-search fa-2x mb-3 d-block"></i>
                                        <p class="mb-0">Nenhum ministério encontrado</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
