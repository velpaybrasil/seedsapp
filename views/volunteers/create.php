<div class="container-fluid">
    <!-- Page Title -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Novo Voluntário</h1>
        <div>
            <a href="/volunteers" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Formulário de Cadastro</h6>
                </div>
                <div class="card-body">
                    <form action="/volunteers/create" method="POST" id="volunteerForm">
                        <!-- Personal Information -->
                        <div class="mb-4">
                            <h5 class="text-gray-700 mb-3">Informações Pessoais</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Nome Completo</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="name" 
                                           name="name" 
                                           required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">E-mail</label>
                                    <input type="email" 
                                           class="form-control" 
                                           id="email" 
                                           name="email" 
                                           required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Senha</label>
                                    <div class="input-group">
                                        <input type="password" 
                                               class="form-control" 
                                               id="password" 
                                               name="password" 
                                               required>
                                        <button class="btn btn-outline-secondary" 
                                                type="button" 
                                                onclick="togglePassword('password')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="password_confirm" class="form-label">Confirmar Senha</label>
                                    <div class="input-group">
                                        <input type="password" 
                                               class="form-control" 
                                               id="password_confirm" 
                                               required>
                                        <button class="btn btn-outline-secondary" 
                                                type="button" 
                                                onclick="togglePassword('password_confirm')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ministry Information -->
                        <div class="mb-4">
                            <h5 class="text-gray-700 mb-3">Informações do Ministério</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="ministry" class="form-label">Ministério</label>
                                    <select class="form-select" id="ministry" name="ministry" required>
                                        <option value="">Selecione...</option>
                                        <option value="música">Música</option>
                                        <option value="mídia">Mídia</option>
                                        <option value="recepção">Recepção</option>
                                        <option value="infantil">Infantil</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Availability -->
                        <div class="mb-4">
                            <h5 class="text-gray-700 mb-3">Disponibilidade</h5>
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Dia</th>
                                                    <th>Manhã</th>
                                                    <th>Tarde</th>
                                                    <th>Noite</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $days = [
                                                    'domingo' => 'Domingo',
                                                    'segunda' => 'Segunda',
                                                    'terca' => 'Terça',
                                                    'quarta' => 'Quarta',
                                                    'quinta' => 'Quinta',
                                                    'sexta' => 'Sexta',
                                                    'sabado' => 'Sábado'
                                                ];
                                                
                                                foreach ($days as $key => $day):
                                                ?>
                                                <tr>
                                                    <td><?= $day ?></td>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" 
                                                                   type="checkbox" 
                                                                   name="availability[<?= $key ?>][]" 
                                                                   value="morning">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" 
                                                                   type="checkbox" 
                                                                   name="availability[<?= $key ?>][]" 
                                                                   value="afternoon">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" 
                                                                   type="checkbox" 
                                                                   name="availability[<?= $key ?>][]" 
                                                                   value="night">
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Cadastrar Voluntário
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('volunteerForm');
    const password = document.getElementById('password');
    const passwordConfirm = document.getElementById('password_confirm');
    
    form.addEventListener('submit', function(e) {
        if (password.value !== passwordConfirm.value) {
            e.preventDefault();
            alert('As senhas não conferem!');
            return;
        }
        
        // Check if at least one availability is selected
        const availabilities = document.querySelectorAll('input[type="checkbox"]:checked');
        if (availabilities.length === 0) {
            e.preventDefault();
            alert('Selecione pelo menos um horário de disponibilidade!');
            return;
        }
    });
});

function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = input.nextElementSibling.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>
