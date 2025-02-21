<?php
$title = 'Editar Visitante';
$styles = '<link href="/gcmanager/assets/css/visitors.css" rel="stylesheet">';
?>

<div class="container-fluid">
    <!-- Page Title -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Editar Visitante</h1>
        <div>
            <a href="/gcmanager/visitors" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    
    <!-- Form Card -->
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="/gcmanager/visitors/<?= $visitor['id'] ?>/update" method="post" class="needs-validation" novalidate>
                <?php 
                // Define $data para o formulário usar os valores atuais do visitante
                $data = $visitor;
                require 'form.php'; 
                ?>
                
                <hr class="my-4">
                
                <div class="d-flex justify-content-end gap-2">
                    <a href="/gcmanager/visitors" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Form Validation Script -->
<script>
(function () {
    'use strict'
    
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.querySelectorAll('.needs-validation')
    
    // Loop over them and prevent submission
    Array.prototype.slice.call(forms)
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                
                form.classList.add('was-validated')
            }, false)
        })
})()
</script>
