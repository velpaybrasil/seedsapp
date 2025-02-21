class Toast {
    static #container;
    static #defaultOptions = {
        duration: 3000,
        position: 'top-right'
    };
    
    static init() {
        // Create toast container if it doesn't exist
        if (!this.#container) {
            this.#container = document.createElement('div');
            this.#container.id = 'toast-container';
            this.#container.className = 'toast-container position-fixed p-3';
            document.body.appendChild(this.#container);
            
            // Add styles for different positions
            this.#setPosition(this.#defaultOptions.position);
        }
    }
    
    static #setPosition(position) {
        const [vertical, horizontal] = position.split('-');
        this.#container.style.top = vertical === 'top' ? '0' : 'auto';
        this.#container.style.bottom = vertical === 'bottom' ? '0' : 'auto';
        this.#container.style.left = horizontal === 'left' ? '0' : 'auto';
        this.#container.style.right = horizontal === 'right' ? '0' : 'auto';
    }
    
    static show(message, type = 'info', options = {}) {
        this.init();
        
        const settings = { ...this.#defaultOptions, ...options };
        
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast align-items-center border-0 show`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        
        // Set background color based on type
        const bgClass = `bg-${type === 'error' ? 'danger' : type}`;
        toast.classList.add(bgClass, 'text-white');
        
        // Create toast content
        const content = document.createElement('div');
        content.className = 'd-flex';
        
        const body = document.createElement('div');
        body.className = 'toast-body';
        body.textContent = message;
        
        const closeButton = document.createElement('button');
        closeButton.type = 'button';
        closeButton.className = 'btn-close btn-close-white me-2 m-auto';
        closeButton.setAttribute('data-bs-dismiss', 'toast');
        closeButton.setAttribute('aria-label', 'Close');
        
        content.appendChild(body);
        content.appendChild(closeButton);
        toast.appendChild(content);
        
        // Add toast to container
        this.#container.appendChild(toast);
        
        // Remove toast after duration
        setTimeout(() => {
            toast.remove();
        }, settings.duration);
        
        // Add click handler to close button
        closeButton.addEventListener('click', () => toast.remove());
    }
    
    static success(message, options = {}) {
        this.show(message, 'success', options);
    }
    
    static error(message, options = {}) {
        this.show(message, 'error', options);
    }
    
    static info(message, options = {}) {
        this.show(message, 'info', options);
    }
    
    static warning(message, options = {}) {
        this.show(message, 'warning', options);
    }
}
