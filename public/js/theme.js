// Gerenciador de Tema
document.addEventListener('DOMContentLoaded', function() {
    // Função para atualizar o tema
    function setTheme(theme) {
        document.documentElement.setAttribute('data-bs-theme', theme);
        document.body.setAttribute('data-bs-theme', theme);
        localStorage.setItem('theme', theme);
    }

    // Inicializa com o tema salvo ou das configurações do usuário
    const savedTheme = localStorage.getItem('theme') || document.documentElement.getAttribute('data-bs-theme') || 'light';
    setTheme(savedTheme);

    // Monitora mudanças nos inputs de tema
    const themeInputs = document.querySelectorAll('input[name="theme"]');
    themeInputs.forEach(input => {
        input.addEventListener('change', function() {
            setTheme(this.value);
        });
    });
});
