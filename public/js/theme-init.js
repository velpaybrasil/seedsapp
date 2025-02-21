// Inicialização do Tema
(function() {
    // Recupera o tema salvo ou usa o padrão
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
    
    // Atualiza o ícone do botão se ele já existir
    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        const icon = themeToggle.querySelector('i');
        if (icon) {
            icon.className = savedTheme === 'light' 
                ? 'fas fa-moon' 
                : 'fas fa-sun';
        }
    }
})();
