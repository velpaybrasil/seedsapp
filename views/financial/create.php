<div class="container-fluid">
    <!-- Page Title -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Nova Transação</h1>
        <a href="/financial" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>

    <!-- Content Row -->
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Registrar Nova Transação</h6>
                </div>
                <div class="card-body">
                    <form action="/financial/create" method="POST" id="transactionForm">
                        <div class="row">
                            <!-- Contributor Information -->
                            <div class="col-md-8 mb-3">
                                <label for="contributor_name" class="form-label">Nome do Contribuinte</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="contributor_name" 
                                       name="contributor_name" 
                                       required
                                       list="contributorsList">
                                <datalist id="contributorsList">
                                    <!-- Will be populated via AJAX -->
                                </datalist>
                            </div>
                            
                            <!-- Transaction Date -->
                            <div class="col-md-4 mb-3">
                                <label for="transaction_date" class="form-label">Data</label>
                                <input type="date" 
                                       class="form-control" 
                                       id="transaction_date" 
                                       name="transaction_date" 
                                       value="<?= date('Y-m-d') ?>"
                                       required>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Amount -->
                            <div class="col-md-4 mb-3">
                                <label for="amount" class="form-label">Valor</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="text" 
                                           class="form-control" 
                                           id="amount" 
                                           name="amount" 
                                           required
                                           pattern="^R?\$?\s*(\d{1,3}(\.?\d{3})*|\d+)(,\d{2})?$"
                                           data-mask="#.##0,00"
                                           data-mask-reverse="true">
                                </div>
                            </div>
                            
                            <!-- Transaction Type -->
                            <div class="col-md-4 mb-3">
                                <label for="type" class="form-label">Tipo</label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="">Selecione...</option>
                                    <option value="tithe">Dízimo</option>
                                    <option value="offering">Oferta</option>
                                </select>
                            </div>
                            
                            <!-- Payment Method -->
                            <div class="col-md-4 mb-3">
                                <label for="payment_method" class="form-label">Método de Pagamento</label>
                                <select class="form-select" id="payment_method" name="payment_method" required>
                                    <option value="">Selecione...</option>
                                    <option value="cash">Dinheiro</option>
                                    <option value="pix">PIX</option>
                                    <option value="credit">Cartão de Crédito</option>
                                    <option value="debit">Cartão de Débito</option>
                                    <option value="transfer">Transferência</option>
                                    <option value="check">Cheque</option>
                                </select>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-4">
                            <label for="notes" class="form-label">Observações</label>
                            <textarea class="form-control" 
                                      id="notes" 
                                      name="notes" 
                                      rows="3"></textarea>
                        </div>

                        <!-- Receipt -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="generate_receipt" 
                                       name="generate_receipt" 
                                       checked>
                                <label class="form-check-label" for="generate_receipt">
                                    Gerar recibo para o contribuinte
                                </label>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Registrar Transação
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery Mask Plugin -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize amount mask
    $('#amount').mask('#.##0,00', {
        reverse: true,
        placeholder: '0,00'
    });
    
    // Load contributors list
    loadContributors();
    
    // Form validation
    const form = document.getElementById('transactionForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (form.checkValidity()) {
            // Submit form
            form.submit();
        }
        
        form.classList.add('was-validated');
    });
});

async function loadContributors() {
    try {
        const response = await fetch('/financial/contributors');
        const contributors = await response.json();
        
        const datalist = document.getElementById('contributorsList');
        contributors.forEach(contributor => {
            const option = document.createElement('option');
            option.value = contributor.name;
            datalist.appendChild(option);
        });
    } catch (error) {
        console.error('Error loading contributors:', error);
    }
}

// Format amount on blur
document.getElementById('amount').addEventListener('blur', function(e) {
    const value = e.target.value;
    if (value) {
        const numericValue = value.replace(/\D/g, '');
        const formattedValue = (numericValue / 100).toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        });
        e.target.value = formattedValue.replace('R$', '').trim();
    }
});
</script>
