<div class="container-fluid">
    <!-- Page Title -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Caixa de Entrada</h1>
        <div>
            <a href="/communication/compose" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Nova Mensagem
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <!-- Total Messages -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total de Mensagens
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $stats['total_messages'] ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-envelope fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Unread Messages -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Não Lidas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $stats['unread_count'] ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-envelope-exclamation fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Unique Contacts -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Contatos Únicos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $stats['unique_contacts'] ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Last Message -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Última Mensagem
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= (new DateTime($stats['last_message_date']))->format('d/m/Y') ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-clock-history fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Messages List -->
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Mensagens</h6>
                    <div class="dropdown no-arrow">
                        <button class="btn btn-link btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li>
                                <a class="dropdown-item" href="/communication/sent">
                                    <i class="bi bi-send me-2"></i> Mensagens Enviadas
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="#" onclick="markAllAsRead()">
                                    <i class="bi bi-check-all me-2"></i> Marcar Todas como Lidas
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="messagesTable">
                            <thead>
                                <tr>
                                    <th style="width: 30px"></th>
                                    <th>Remetente</th>
                                    <th>Assunto</th>
                                    <th>Data</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($messages as $message): ?>
                                <tr class="<?= $message['read_at'] ? '' : 'fw-bold' ?>">
                                    <td>
                                        <i class="bi bi-<?= $message['read_at'] ? 'envelope-open' : 'envelope-fill' ?>"></i>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($message['sender_name']) ?>
                                        <small class="text-muted d-block">
                                            <?= htmlspecialchars($message['sender_email']) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <a href="/communication/view/<?= $message['id'] ?>" class="text-decoration-none">
                                            <?= htmlspecialchars($message['subject']) ?>
                                        </a>
                                        <?php if (!empty($message['content'])): ?>
                                        <small class="text-muted d-block text-truncate" style="max-width: 300px;">
                                            <?= htmlspecialchars(strip_tags($message['content'])) ?>
                                        </small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= (new DateTime($message['created_at']))->format('d/m/Y H:i') ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="/communication/view/<?= $message['id'] ?>" 
                                               class="btn btn-sm btn-primary"
                                               data-bs-toggle="tooltip"
                                               title="Ver Mensagem">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="/communication/compose?reply_to=<?= $message['id'] ?>" 
                                               class="btn btn-sm btn-info"
                                               data-bs-toggle="tooltip"
                                               title="Responder">
                                                <i class="bi bi-reply"></i>
                                            </a>
                                            <?php if (!$message['read_at']): ?>
                                            <button type="button"
                                                    class="btn btn-sm btn-success"
                                                    onclick="markAsRead(<?= $message['id'] ?>)"
                                                    data-bs-toggle="tooltip"
                                                    title="Marcar como Lida">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($page > 1 || count($messages) === $limit): ?>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            Mostrando <?= count($messages) ?> mensagens
                        </div>
                        <nav>
                            <ul class="pagination">
                                <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $page - 1 ?>">Anterior</a>
                                </li>
                                <?php endif; ?>
                                
                                <?php if (count($messages) === $limit): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $page + 1 ?>">Próxima</a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DataTables -->
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#messagesTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json'
        },
        order: [[3, 'desc']],
        pageLength: 25,
        columnDefs: [
            { orderable: false, targets: [0, 4] }
        ]
    });
    
    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(tooltip => new bootstrap.Tooltip(tooltip));
});

async function markAsRead(messageId) {
    try {
        const response = await fetch(`/communication/mark-read/${messageId}`, {
            method: 'POST'
        });
        
        if (response.ok) {
            location.reload();
        } else {
            throw new Error('Erro ao marcar mensagem como lida');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Ocorreu um erro ao marcar a mensagem como lida');
    }
}

async function markAllAsRead() {
    if (!confirm('Deseja marcar todas as mensagens como lidas?')) {
        return;
    }
    
    try {
        const response = await fetch('/communication/mark-all-read', {
            method: 'POST'
        });
        
        if (response.ok) {
            location.reload();
        } else {
            throw new Error('Erro ao marcar mensagens como lidas');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Ocorreu um erro ao marcar as mensagens como lidas');
    }
}
</script>
