    <!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitantes - GCManager</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
    <?php if (isset($styles)) echo $styles; ?>

    <style>
        body { background-color: #f8f9fa; }
        .table th { font-weight: 600; }
        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        .status-badge {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <!-- Page Title -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0">Visitantes</h1>
            <div>
                <a href="/visitors/export" class="btn btn-outline-primary me-2">
                    <i class="fas fa-download me-2"></i>Exportar
                </a>
                <a href="/visitors/create" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Novo Visitante
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form action="/visitors" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Buscar</label>
                        <input type="text" name="search" class="form-control" placeholder="Nome, email ou telefone" 
                               value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Todos</option>
                            <option value="lead" <?= ($_GET['status'] ?? '') === 'lead' ? 'selected' : '' ?>>Novo</option>
                            <option value="contacted" <?= ($_GET['status'] ?? '') === 'contacted' ? 'selected' : '' ?>>Contactado</option>
                            <option value="gc_linked" <?= ($_GET['status'] ?? '') === 'gc_linked' ? 'selected' : '' ?>>Em GC</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Grupo</label>
                        <select name="gc_id" class="form-select">
                            <option value="">Todos</option>
                            <?php foreach ($groups as $group): ?>
                                <option value="<?= $group['id'] ?>" <?= ($_GET['gc_id'] ?? '') == $group['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($group['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Visitors List -->
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Contato</th>
                            <th>Status</th>
                            <th>Grupo</th>
                            <th>Data</th>
                            <th width="100">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($visitors)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-search fa-2x mb-3"></i>
                                        <p class="mb-0">Nenhum visitante encontrado</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($visitors as $visitor): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if ($visitor['photo']): ?>
                                                <img src="<?= $visitor['photo'] ?>" class="avatar me-3">
                                            <?php else: ?>
                                                <div class="avatar me-3 bg-primary text-white d-flex align-items-center justify-content-center">
                                                    <?= strtoupper(substr($visitor['name'], 0, 1)) ?>
                                                </div>
                                            <?php endif; ?>
                                            <?= htmlspecialchars($visitor['name']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div><?= htmlspecialchars($visitor['email']) ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($visitor['phone']) ?></small>
                                    </td>
                                    <td>
                                        <?php
                                        $statusClass = [
                                            'lead' => 'bg-info',
                                            'contacted' => 'bg-warning',
                                            'gc_linked' => 'bg-success'
                                        ][$visitor['status']] ?? 'bg-secondary';
                                        
                                        $statusText = [
                                            'lead' => 'Novo',
                                            'contacted' => 'Contactado',
                                            'gc_linked' => 'Em GC'
                                        ][$visitor['status']] ?? 'Desconhecido';
                                        ?>
                                        <span class="badge <?= $statusClass ?> status-badge">
                                            <?= $statusText ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($visitor['group_name'] ?? 'Não definido') ?></td>
                                    <td><?= date('d/m/Y', strtotime($visitor['created_at'])) ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="/visitors/<?= $visitor['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="/visitors/<?= $visitor['id'] ?>/edit" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                    data-bs-toggle="modal" data-bs-target="#deleteModal<?= $visitor['id'] ?>"
                                                    title="Excluir visitante">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="card-footer">
                    <nav>
                        <ul class="pagination justify-content-center mb-0">
                            <?php if ($currentPage > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $currentPage - 1 . $query_string ?>">Anterior</a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i . $query_string ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($currentPage < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $currentPage + 1 . $query_string ?>">Próximo</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php if (isset($scripts)) echo $scripts; ?>
</body>
</html>
