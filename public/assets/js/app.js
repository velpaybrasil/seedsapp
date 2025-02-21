// App Configuration
const config = {
    currency: {
        locale: 'pt-BR',
        options: {
            style: 'currency',
            currency: 'BRL'
        }
    },
    date: {
        format: 'DD/MM/YYYY'
    },
    charts: {
        colors: {
            primary: '#2563eb',
            secondary: '#64748b',
            success: '#16a34a',
            danger: '#dc2626',
            warning: '#ca8a04',
            info: '#0891b2'
        }
    }
};

// Utility Functions
const utils = {
    // Format currency
    formatCurrency: (value) => {
        return new Intl.NumberFormat(config.currency.locale, config.currency.options).format(value);
    },
    
    // Format date
    formatDate: (date) => {
        return new Date(date).toLocaleDateString(config.currency.locale);
    },
    
    // Format datetime
    formatDateTime: (datetime) => {
        return new Date(datetime).toLocaleString(config.currency.locale);
    },
    
    // Show loading spinner
    showLoading: () => {
        const spinner = `
            <div class="spinner-overlay">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', spinner);
    },
    
    // Hide loading spinner
    hideLoading: () => {
        const spinner = document.querySelector('.spinner-overlay');
        if (spinner) {
            spinner.remove();
        }
    },
    
    // Show toast notification
    showToast: (message, type = 'success') => {
        const toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        
        toast.fire({
            icon: type,
            title: message
        });
    },
    
    // Confirm action
    confirm: async (options = {}) => {
        const defaultOptions = {
            title: 'Tem certeza?',
            text: 'Esta ação não poderá ser desfeita!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim',
            cancelButtonText: 'Cancelar'
        };
        
        const result = await Swal.fire({
            ...defaultOptions,
            ...options
        });
        
        return result.isConfirmed;
    }
};

// Chart Functions
const charts = {
    // Line Chart
    createLineChart: (ctx, data, options = {}) => {
        const defaultOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        };
        
        return new Chart(ctx, {
            type: 'line',
            data: data,
            options: {
                ...defaultOptions,
                ...options
            }
        });
    },
    
    // Bar Chart
    createBarChart: (ctx, data, options = {}) => {
        const defaultOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        };
        
        return new Chart(ctx, {
            type: 'bar',
            data: data,
            options: {
                ...defaultOptions,
                ...options
            }
        });
    },
    
    // Pie Chart
    createPieChart: (ctx, data, options = {}) => {
        const defaultOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top'
                }
            }
        };
        
        return new Chart(ctx, {
            type: 'pie',
            data: data,
            options: {
                ...defaultOptions,
                ...options
            }
        });
    },
    
    // Doughnut Chart
    createDoughnutChart: (ctx, data, options = {}) => {
        const defaultOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            cutout: '70%'
        };
        
        return new Chart(ctx, {
            type: 'doughnut',
            data: data,
            options: {
                ...defaultOptions,
                ...options
            }
        });
    }
};

// Form Functions
const forms = {
    // Initialize form validation
    initValidation: (form) => {
        form.addEventListener('submit', (e) => {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    },
    
    // Initialize money input mask
    initMoneyMask: (input) => {
        input.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, '');
            value = (parseInt(value) / 100).toFixed(2);
            e.target.value = utils.formatCurrency(value);
        });
    },
    
    // Initialize phone input mask
    initPhoneMask: (input) => {
        input.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 11) value = value.slice(0, 11);
            if (value.length > 7) {
                value = value.slice(0, 7) + '-' + value.slice(7);
            }
            if (value.length > 2) {
                value = '(' + value.slice(0, 2) + ') ' + value.slice(2);
            }
            e.target.value = value;
        });
    },
    
    // Initialize CPF input mask
    initCpfMask: (input) => {
        input.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 11) value = value.slice(0, 11);
            if (value.length > 9) {
                value = value.slice(0, 9) + '-' + value.slice(9);
            }
            if (value.length > 6) {
                value = value.slice(0, 6) + '.' + value.slice(6);
            }
            if (value.length > 3) {
                value = value.slice(0, 3) + '.' + value.slice(3);
            }
            e.target.value = value;
        });
    }
};

// AJAX Functions
const ajax = {
    // GET request
    get: async (url, options = {}) => {
        try {
            utils.showLoading();
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                },
                ...options
            });
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error:', error);
            utils.showToast('Erro ao carregar dados', 'error');
            throw error;
        } finally {
            utils.hideLoading();
        }
    },
    
    // POST request
    post: async (url, body, options = {}) => {
        try {
            utils.showLoading();
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(body),
                ...options
            });
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error:', error);
            utils.showToast('Erro ao enviar dados', 'error');
            throw error;
        } finally {
            utils.hideLoading();
        }
    }
};

// Export modules
window.App = {
    config,
    utils,
    charts,
    forms,
    ajax
};
