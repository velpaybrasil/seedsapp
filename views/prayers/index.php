<?php 
use App\Models\PrayerRequest;
$title = 'Pedidos de Oração'; 
?>

<div class="container-fluid py-4">
    <!-- Page Title -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Pedidos de Oração</h1>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newPrayerRequestModal">
            <i class="fas fa-plus me-2"></i>Novo Pedido
        </button>
    </div>

    <!-- Kanban Board -->
    <div class="row">
        <!-- Pending Column -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-white py-2">
                    <h6 class="mb-0">Pendentes</h6>
                </div>
                <div class="card-body kanban-column p-2" data-status="pending">
                    <?php if (empty($pendingRequests)): ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-pray fa-2x mb-2"></i>
                            <p>Nenhum pedido pendente</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($pendingRequests as $request): ?>
                            <div class="card prayer-request-card mb-2" data-id="<?= $request['id'] ?>">
                                <div class="card-body p-3">
                                    <h6 class="card-title mb-1"><?= htmlspecialchars($request['visitor_name']) ?></h6>
                                    <p class="card-text small text-muted mb-2"><?= htmlspecialchars($request['request']) ?></p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted"><?= date('d/m/Y', strtotime($request['created_at'])) ?></small>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-secondary edit-request" 
                                                    data-id="<?= $request['id'] ?>"
                                                    data-name="<?= htmlspecialchars($request['visitor_name']) ?>"
                                                    data-description="<?= htmlspecialchars($request['request']) ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-request" data-id="<?= $request['id'] ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- In Progress Column -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white py-2">
                    <h6 class="mb-0">Em Oração</h6>
                </div>
                <div class="card-body kanban-column p-2" data-status="praying">
                    <?php if (empty($prayingRequests)): ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-pray fa-2x mb-2"></i>
                            <p>Nenhum pedido em oração</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($prayingRequests as $request): ?>
                            <div class="card prayer-request-card mb-2" data-id="<?= $request['id'] ?>">
                                <div class="card-body p-3">
                                    <h6 class="card-title mb-1"><?= htmlspecialchars($request['visitor_name']) ?></h6>
                                    <p class="card-text small text-muted mb-2"><?= htmlspecialchars($request['request']) ?></p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted"><?= date('d/m/Y', strtotime($request['created_at'])) ?></small>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-secondary edit-request" 
                                                    data-id="<?= $request['id'] ?>"
                                                    data-name="<?= htmlspecialchars($request['visitor_name']) ?>"
                                                    data-description="<?= htmlspecialchars($request['request']) ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-request" data-id="<?= $request['id'] ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Answered Column -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white py-2">
                    <h6 class="mb-0">Respondidos</h6>
                </div>
                <div class="card-body kanban-column p-2" data-status="completed">
                    <?php if (empty($completedRequests)): ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-pray fa-2x mb-2"></i>
                            <p>Nenhum pedido respondido</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($completedRequests as $request): ?>
                            <div class="card prayer-request-card mb-2" data-id="<?= $request['id'] ?>">
                                <div class="card-body p-3">
                                    <h6 class="card-title mb-1"><?= htmlspecialchars($request['visitor_name']) ?></h6>
                                    <p class="card-text small text-muted mb-2"><?= htmlspecialchars($request['request']) ?></p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted"><?= date('d/m/Y', strtotime($request['created_at'])) ?></small>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-secondary edit-request" 
                                                    data-id="<?= $request['id'] ?>"
                                                    data-name="<?= htmlspecialchars($request['visitor_name']) ?>"
                                                    data-description="<?= htmlspecialchars($request['request']) ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-request" data-id="<?= $request['id'] ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Prayer Request Modal -->
<div class="modal fade" id="newPrayerRequestModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= url('/prayers') ?>" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Novo Pedido de Oração</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="visitor_name" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="visitor_name" name="visitor_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="request" class="form-label">Pedido</label>
                        <textarea class="form-control" id="request" name="request" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Prayer Request Modal -->
<div class="modal fade" id="editPrayerRequestModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editPrayerRequestForm" method="POST">
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Pedido de Oração</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_visitor_name" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="edit_visitor_name" name="visitor_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_request" class="form-label">Pedido</label>
                        <textarea class="form-control" id="edit_request" name="request" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.kanban-column {
    min-height: 200px;
    padding: 1rem;
    background-color: #fff;
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
</style>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
// Initialize Sortable for each column
document.querySelectorAll('.kanban-column').forEach(column => {
    new Sortable(column, {
        group: 'prayer-requests',
        animation: 150,
        ghostClass: 'prayer-request-card-ghost',
        chosenClass: 'prayer-request-card-chosen',
        onEnd: function(evt) {
            const requestId = evt.item.dataset.id;
            const newStatus = evt.to.dataset.status;
            
            // Update request status via AJAX
            fetch(`/prayers/${requestId}/status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ status: newStatus })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    // Revert the move if update failed
                    evt.from.appendChild(evt.item);
                    Swal.fire('Erro!', 'Não foi possível atualizar o status.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Revert the move on error
                evt.from.appendChild(evt.item);
                Swal.fire('Erro!', 'Ocorreu um erro ao atualizar o status.', 'error');
            });
        }
    });
});

// Edit Prayer Request
$('.edit-request').click(function() {
    const id = $(this).data('id');
    const name = $(this).data('name');
    const description = $(this).data('description');
    
    $('#edit_visitor_name').val(name);
    $('#edit_request').val(description);
    $('#editPrayerRequestForm').attr('action', `/prayers/${id}`);
    $('#editPrayerRequestModal').modal('show');
});

// Delete Prayer Request
$('.delete-request').click(function() {
    const id = $(this).data('id');
    
    Swal.fire({
        title: 'Tem certeza?',
        text: "Esta ação não pode ser desfeita!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sim, excluir!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/prayers/${id}`, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $(this).closest('.prayer-request-card').remove();
                    Swal.fire('Excluído!', 'O pedido foi excluído com sucesso.', 'success');
                } else {
                    Swal.fire('Erro!', 'Não foi possível excluir o pedido.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Erro!', 'Ocorreu um erro ao excluir o pedido.', 'error');
            });
        }
    });
});
</script>
