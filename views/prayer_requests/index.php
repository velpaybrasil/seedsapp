<?php 
use App\Models\PrayerRequest;
use App\Core\View;

View::extends('app');

View::section('styles');
?>
<style>
.kanban-column {
    min-height: 200px;
    padding: 1rem;
    background-color: #f8f9fa;
    border-radius: 0.25rem;
}

.prayer-request-card {
    cursor: move;
    transition: transform 0.2s, box-shadow 0.2s;
}

.prayer-request-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}

.prayer-request-card.sortable-ghost {
    opacity: 0.5;
    background-color: #e9ecef;
}

.prayer-request-card.sortable-chosen {
    background-color: #fff;
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.animate-card {
    animation: fadeInUp 0.5s ease-in-out;
}
</style>
<?php View::endSection(); ?>

<?php View::section('content'); ?>
<div class="container-fluid py-4">
    <!-- Page Title -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Pedidos de Oração</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newPrayerRequestModal">
            <i class="fas fa-plus me-2"></i>Novo Pedido
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <!-- Total de Pedidos -->
        <div class="col-sm-6 col-md-6 col-xl-3">
            <div class="card h-100 border-0 bg-gradient animate-card" 
                 style="background-color: #4158D0;background-image: linear-gradient(43deg, #4158D0 0%, #C850C0 46%, #FFCC70 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="rounded-circle bg-white bg-opacity-25 p-2">
                            <i class="fas fa-pray fa-lg text-white"></i>
                        </div>
                        <span class="badge bg-white bg-opacity-25 text-white">Total</span>
                    </div>
                    <h3 class="display-5 fw-bold text-white mb-1"><?= count($pendingRequests) + count($prayingRequests) + count($completedRequests) ?></h3>
                    <p class="text-white text-opacity-75 mb-0">Pedidos registrados</p>
                </div>
            </div>
        </div>

        <!-- Pedidos Pendentes -->
        <div class="col-sm-6 col-md-6 col-xl-3">
            <div class="card h-100 border-0 bg-gradient animate-card" 
                 style="background-color: #0093E9;background-image: linear-gradient(160deg, #0093E9 0%, #80D0C7 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="rounded-circle bg-white bg-opacity-25 p-2">
                            <i class="fas fa-clock fa-lg text-white"></i>
                        </div>
                        <span class="badge bg-white bg-opacity-25 text-white">Pendentes</span>
                    </div>
                    <h3 class="display-5 fw-bold text-white mb-1"><?= count($pendingRequests) ?></h3>
                    <p class="text-white text-opacity-75 mb-0">Aguardando oração</p>
                </div>
            </div>
        </div>

        <!-- Em Oração -->
        <div class="col-sm-6 col-md-6 col-xl-3">
            <div class="card h-100 border-0 bg-gradient animate-card" 
                 style="background-color: #8EC5FC;background-image: linear-gradient(62deg, #8EC5FC 0%, #E0C3FC 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="rounded-circle bg-white bg-opacity-25 p-2">
                            <i class="fas fa-hands fa-lg text-white"></i>
                        </div>
                        <span class="badge bg-white bg-opacity-25 text-white">Em Oração</span>
                    </div>
                    <h3 class="display-5 fw-bold text-white mb-1"><?= count($prayingRequests) ?></h3>
                    <p class="text-white text-opacity-75 mb-0">Sendo intercedidos</p>
                </div>
            </div>
        </div>

        <!-- Concluídos -->
        <div class="col-sm-6 col-md-6 col-xl-3">
            <div class="card h-100 border-0 bg-gradient animate-card" 
                 style="background-color: #85FFBD;background-image: linear-gradient(45deg, #85FFBD 0%, #FFFB7D 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="rounded-circle bg-white bg-opacity-25 p-2">
                            <i class="fas fa-check-circle fa-lg text-white"></i>
                        </div>
                        <span class="badge bg-white bg-opacity-25 text-white">Concluídos</span>
                    </div>
                    <h3 class="display-5 fw-bold text-white mb-1"><?= count($completedRequests) ?></h3>
                    <p class="text-white text-opacity-75 mb-0">Pedidos atendidos</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Kanban Board -->
    <div class="row">
        <!-- Pending Column -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">Pendentes</h5>
                </div>
                <div class="card-body">
                    <div class="kanban-column" data-status="pending">
                        <?php if (empty($pendingRequests)): ?>
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-pray fa-2x mb-2"></i>
                                <p>Nenhum pedido pendente</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($pendingRequests as $request): ?>
                                <div class="card prayer-request-card mb-2" data-id="<?= $request['id'] ?>">
                                    <div class="card-body">
                                        <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($request['visitor_name']) ?></h6>
                                        <p class="card-text"><?= htmlspecialchars($request['request']) ?></p>
                                        <small class="text-muted">
                                            <?= date('d/m/Y H:i', strtotime($request['created_at'])) ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Praying Column -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Em Oração</h5>
                </div>
                <div class="card-body">
                    <div class="kanban-column" data-status="praying">
                        <?php if (empty($prayingRequests)): ?>
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-pray fa-2x mb-2"></i>
                                <p>Nenhum pedido em oração</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($prayingRequests as $request): ?>
                                <div class="card prayer-request-card mb-2" data-id="<?= $request['id'] ?>">
                                    <div class="card-body">
                                        <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($request['visitor_name']) ?></h6>
                                        <p class="card-text"><?= htmlspecialchars($request['request']) ?></p>
                                        <small class="text-muted">
                                            <?= date('d/m/Y H:i', strtotime($request['created_at'])) ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completed Column -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Concluídos</h5>
                </div>
                <div class="card-body">
                    <div class="kanban-column" data-status="completed">
                        <?php if (empty($completedRequests)): ?>
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-pray fa-2x mb-2"></i>
                                <p>Nenhum pedido concluído</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($completedRequests as $request): ?>
                                <div class="card prayer-request-card mb-2" data-id="<?= $request['id'] ?>">
                                    <div class="card-body">
                                        <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($request['visitor_name']) ?></h6>
                                        <p class="card-text"><?= htmlspecialchars($request['request']) ?></p>
                                        <small class="text-muted">
                                            <?= date('d/m/Y H:i', strtotime($request['created_at'])) ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Prayer Request Modal -->
<div class="modal fade" id="newPrayerRequestModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Pedido de Oração</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= url('/prayer-requests/store') ?>" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="visitor_name" class="form-label">Nome do Visitante</label>
                        <input type="text" class="form-control" id="visitor_name" name="visitor_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="request" class="form-label">Pedido de Oração</label>
                        <textarea class="form-control" id="request" name="request" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Pedido</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php View::endSection(); ?>

<?php View::section('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Sortable for each kanban column
    document.querySelectorAll('.kanban-column').forEach(function(column) {
        new Sortable(column, {
            group: 'prayer-requests',
            animation: 150,
            onEnd: function(evt) {
                const requestId = evt.item.dataset.id;
                const newStatus = evt.to.dataset.status;
                
                // Send AJAX request to update status
                fetch('/prayer-requests/update-status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${requestId}&status=${newStatus}`
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        // If update failed, move item back
                        evt.from.appendChild(evt.item);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    evt.from.appendChild(evt.item);
                });
            }
        });
    });
});
</script>
<?php View::endSection(); ?>
