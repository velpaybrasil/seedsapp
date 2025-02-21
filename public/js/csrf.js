// Adiciona o token CSRF em todas as requisições AJAX
document.addEventListener('DOMContentLoaded', function() {
    // Pega o token CSRF da meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    
    if (csrfToken) {
        // Adiciona o token CSRF no header de todas as requisições AJAX
        const originalFetch = window.fetch;
        window.fetch = function(url, options = {}) {
            if (!options.headers) {
                options.headers = {};
            }

            // Adiciona o token CSRF no header
            if (typeof options.headers === 'object') {
                options.headers['X-CSRF-TOKEN'] = csrfToken;
            }

            return originalFetch(url, options);
        };

        // Adiciona o token CSRF em todos os formulários
        document.querySelectorAll('form').forEach(form => {
            if (!form.querySelector('input[name="csrf_token"]')) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'csrf_token';
                input.value = csrfToken;
                form.appendChild(input);
            }
        });

        // Observa o DOM para adicionar o token em novos formulários
        const observer = new MutationObserver(mutations => {
            mutations.forEach(mutation => {
                mutation.addedNodes.forEach(node => {
                    if (node.nodeName === 'FORM' && !node.querySelector('input[name="csrf_token"]')) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'csrf_token';
                        input.value = csrfToken;
                        node.appendChild(input);
                    }
                });
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
});
