// Show loading spinner
function showLoading() {
    const spinner = document.createElement('div');
    spinner.className = 'spinner-overlay';
    spinner.innerHTML = `
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Carregando...</span>
        </div>
    `;
    document.body.appendChild(spinner);
}

// Hide loading spinner
function hideLoading() {
    const spinner = document.querySelector('.spinner-overlay');
    if (spinner) {
        spinner.remove();
    }
}

// Format currency
function formatCurrency(value) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value);
}

// Format date
function formatDate(date) {
    return new Intl.DateTimeFormat('pt-BR').format(new Date(date));
}

// Format datetime
function formatDateTime(datetime) {
    return new Intl.DateTimeFormat('pt-BR', {
        dateStyle: 'short',
        timeStyle: 'short'
    }).format(new Date(datetime));
}

// Confirm action
function confirmAction(message = 'Tem certeza que deseja realizar esta ação?') {
    return confirm(message);
}

// Handle form submission with AJAX
function handleFormSubmit(form, callback) {
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        showLoading();

        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: form.method,
                body: formData
            });

            const data = await response.json();
            hideLoading();

            if (callback) {
                callback(data);
            }
        } catch (error) {
            hideLoading();
            console.error('Error:', error);
            alert('Ocorreu um erro ao processar sua solicitação.');
        }
    });
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(tooltip => {
        new bootstrap.Tooltip(tooltip);
    });
});

// Initialize popovers
document.addEventListener('DOMContentLoaded', function() {
    const popovers = document.querySelectorAll('[data-bs-toggle="popover"]');
    popovers.forEach(popover => {
        new bootstrap.Popover(popover);
    });
});

// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});

// Handle file input change
function handleFileInput(input) {
    const file = input.files[0];
    const label = input.nextElementSibling;
    
    if (file) {
        label.textContent = file.name;
    } else {
        label.textContent = 'Escolher arquivo';
    }
}

// Format phone number
function formatPhone(phone) {
    phone = phone.replace(/\D/g, '');
    phone = phone.replace(/^(\d{2})(\d)/g, '($1) $2');
    phone = phone.replace(/(\d)(\d{4})$/, '$1-$2');
    return phone;
}

// Apply phone mask to input
function phoneMask(input) {
    input.addEventListener('input', (e) => {
        e.target.value = formatPhone(e.target.value);
    });
}

// Format CPF
function formatCPF(cpf) {
    cpf = cpf.replace(/\D/g, '');
    cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
    cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
    cpf = cpf.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    return cpf;
}

// Apply CPF mask to input
function cpfMask(input) {
    input.addEventListener('input', (e) => {
        e.target.value = formatCPF(e.target.value);
    });
}

// Format CEP
function formatCEP(cep) {
    cep = cep.replace(/\D/g, '');
    cep = cep.replace(/^(\d{5})(\d)/, '$1-$2');
    return cep;
}

// Apply CEP mask to input
function cepMask(input) {
    input.addEventListener('input', (e) => {
        e.target.value = formatCEP(e.target.value);
    });
}

// Fetch address by CEP
async function fetchAddressByCEP(cep) {
    try {
        showLoading();
        const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
        const data = await response.json();
        hideLoading();
        return data;
    } catch (error) {
        hideLoading();
        console.error('Error:', error);
        return null;
    }
}

// Initialize DataTables
function initDataTable(tableId, options = {}) {
    const defaultOptions = {
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json'
        },
        responsive: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]]
    };

    return new DataTable(`#${tableId}`, { ...defaultOptions, ...options });
}
