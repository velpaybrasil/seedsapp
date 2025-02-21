<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$title = 'Editar Visitante';
$styles = '<link href="/assets/css/visitors.css" rel="stylesheet">';
?>

<div class="container-fluid">
    <!-- Page Title -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Editar Visitante</h1>
        <div>
            <a href="/visitors" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    
    <!-- Form Card -->
    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="/visitors/<?= $visitor['id'] ?>/update" class="needs-validation" enctype="multipart/form-data" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <?php 
                // Define $data para o formulário usar os valores atuais do visitante
                $data = $visitor;
                require 'form.php'; 
                ?>
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
