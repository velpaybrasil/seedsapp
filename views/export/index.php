<div class="container-fluid">
    <!-- Page Title -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Exportar Dados</h1>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Messages Export -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Exportar Mensagens</h6>
                </div>
                <div class="card-body">
                    <form id="exportMessagesForm" onsubmit="return exportMessages(event)">
                        <div class="mb-3">
                            <label for="messageType" class="form-label">Tipo de Mensagens</label>
                            <select class="form-select" id="messageType" name="type">
                                <option value="all">Todas as Mensagens</option>
                                <option value="sent">Mensagens Enviadas</option>
                                <option value="received">Mensagens Recebidas</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="messageStartDate" class="form-label">Data Inicial</label>
                                    <input type="date" class="form-control" id="messageStartDate" name="start_date">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="messageEndDate" class="form-label">Data Final</label>
                                    <input type="date" class="form-control" id="messageEndDate" name="end_date">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="messageFormat" class="form-label">Formato</label>
                            <select class="form-select" id="messageFormat" name="format">
                                <option value="excel">Excel (.xlsx)</option>
                                <option value="pdf">PDF (.pdf)</option>
                                <option value="csv">CSV (.csv)</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-download"></i> Exportar Mensagens
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Notifications Export -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Exportar Notificações</h6>
                </div>
                <div class="card-body">
                    <form id="exportNotificationsForm" onsubmit="return exportNotifications(event)">
                        <div class="mb-3">
                            <label for="notificationType" class="form-label">Status</label>
                            <select class="form-select" id="notificationType" name="type">
                                <option value="all">Todas as Notificações</option>
                                <option value="read">Notificações Lidas</option>
                                <option value="unread">Notificações Não Lidas</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="notificationStartDate" class="form-label">Data Inicial</label>
                                    <input type="date" class="form-control" id="notificationStartDate" name="start_date">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="notificationEndDate" class="form-label">Data Final</label>
                                    <input type="date" class="form-control" id="notificationEndDate" name="end_date">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notificationFormat" class="form-label">Formato</label>
                            <select class="form-select" id="notificationFormat" name="format">
                                <option value="excel">Excel (.xlsx)</option>
                                <option value="pdf">PDF (.pdf)</option>
                                <option value="csv">CSV (.csv)</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-download"></i> Exportar Notificações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Financial Transactions Export -->
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Exportar Transações Financeiras</h6>
                </div>
                <div class="card-body">
                    <form id="exportTransactionsForm" onsubmit="return exportTransactions(event)">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="transactionType" class="form-label">Tipo de Transação</label>
                                    <select class="form-select" id="transactionType" name="type">
                                        <option value="all">Todas as Transações</option>
                                        <option value="income">Entradas</option>
                                        <option value="expense">Saídas</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="transactionCategory" class="form-label">Categoria</label>
                                    <select class="form-select" id="transactionCategory" name="category">
                                        <option value="">Todas as Categorias</option>
                                        <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>">
                                            <?= htmlspecialchars($category['name']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="transactionFormat" class="form-label">Formato</label>
                                    <select class="form-select" id="transactionFormat" name="format">
                                        <option value="excel">Excel (.xlsx)</option>
                                        <option value="pdf">PDF (.pdf)</option>
                                        <option value="csv">CSV (.csv)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="transactionStartDate" class="form-label">Data Inicial</label>
                                    <input type="date" class="form-control" id="transactionStartDate" name="start_date">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="transactionEndDate" class="form-label">Data Final</label>
                                    <input type="date" class="form-control" id="transactionEndDate" name="end_date">
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-download"></i> Exportar Transações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
async function exportData(endpoint, formData) {
    try {
        const params = new URLSearchParams(formData).toString();
        const response = await fetch(`/export/${endpoint}?${params}`);
        const data = await response.json();
        
        if (data.success) {
            // Create a temporary link to download the file
            const link = document.createElement('a');
            link.href = data.file;
            link.download = '';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            Toast.success('Arquivo exportado com sucesso!');
        } else {
            throw new Error(data.error || 'Erro ao exportar dados');
        }
    } catch (error) {
        console.error('Error:', error);
        Toast.error('Erro ao exportar dados: ' + error.message);
    }
}

function exportMessages(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    exportData('messages', formData);
    return false;
}

function exportNotifications(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    exportData('notifications', formData);
    return false;
}

function exportTransactions(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    exportData('transactions', formData);
    return false;
}

// Set default dates
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date();
    const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
    
    const formatDate = date => date.toISOString().split('T')[0];
    
    const startDateInputs = document.querySelectorAll('[name="start_date"]');
    const endDateInputs = document.querySelectorAll('[name="end_date"]');
    
    startDateInputs.forEach(input => input.value = formatDate(firstDayOfMonth));
    endDateInputs.forEach(input => input.value = formatDate(today));
});
</script>
