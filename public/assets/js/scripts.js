// Scripts personalizados
$(document).ready(function() {
    // Inicializa os dropdowns do Bootstrap
    var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
    var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
        return new bootstrap.Dropdown(dropdownToggleEl)
    });

    // Inicializa os tooltips do Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Inicializa os popovers do Bootstrap
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl)
    });

    // Fecha o menu lateral em dispositivos móveis quando um item é clicado
    $('.sidebar .nav-link').on('click', function() {
        if ($(window).width() < 768) {
            $('body').removeClass('sidebar-toggled');
            $('.sidebar').removeClass('toggled');
        }
    });

    // Adiciona a classe active ao item do menu atual
    var currentPath = window.location.pathname;
    $('.sidebar .nav-link').each(function() {
        var $this = $(this);
        if ($this.attr('href') === currentPath) {
            $this.parent().addClass('active');
        }
    });
});
