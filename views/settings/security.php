<div class="container-fluid">
    <!-- Page Title -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Segurança</h1>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-lg-8">
            <!-- Change Password -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Alterar Senha</h6>
                </div>
                <div class="card-body">
                    <form action="/settings/update-password" method="POST">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Senha Atual</label>
                            <input type="password" 
                                   class="form-control" 
                                   id="current_password" 
                                   name="current_password" 
                                   required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Nova Senha</label>
                            <input type="password" 
                                   class="form-control" 
                                   id="new_password" 
                                   name="new_password" 
                                   required>
                            <div class="form-text">
                                A senha deve ter pelo menos 8 caracteres
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirmar Nova Senha</label>
                            <input type="password" 
                                   class="form-control" 
                                   id="confirm_password" 
                                   name="confirm_password" 
                                   required>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-key"></i> Alterar Senha
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Email -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Alterar E-mail</h6>
                </div>
                <div class="card-body">
                    <form action="/settings/update-email" method="POST">
                        <div class="mb-3">
                            <label for="current_email" class="form-label">E-mail Atual</label>
                            <input type="email" 
                                   class="form-control" 
                                   value="<?= htmlspecialchars($profile['email']) ?>" 
                                   disabled>
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_email" class="form-label">Novo E-mail</label>
                            <input type="email" 
                                   class="form-control" 
                                   id="new_email" 
                                   name="new_email" 
                                   required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Senha</label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password" 
                                   name="password" 
                                   required>
                            <div class="form-text">
                                Digite sua senha atual para confirmar a alteração
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-envelope"></i> Alterar E-mail
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Security Information -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informações de Segurança</h6>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="mb-2">Última Alteração de Senha</h6>
                        <p class="text-muted">
                            <?= $profile['password_updated_at'] 
                                ? (new DateTime($profile['password_updated_at']))->format('d/m/Y H:i') 
                                : 'Nunca' ?>
                        </p>
                    </div>

                    <div class="mb-4">
                        <h6 class="mb-2">Último Acesso</h6>
                        <p class="text-muted">
                            <?= $profile['last_login_at']
                                ? (new DateTime($profile['last_login_at']))->format('d/m/Y H:i')
                                : 'Nunca' ?>
                        </p>
                    </div>

                    <div class="mb-4">
                        <h6 class="mb-2">IP do Último Acesso</h6>
                        <p class="text-muted">
                            <?= $profile['last_login_ip'] ?? 'N/A' ?>
                        </p>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h6 class="mb-2">Dicas de Segurança</h6>
                        <ul class="text-muted small">
                            <li>Use senhas fortes com letras, números e símbolos</li>
                            <li>Não compartilhe suas credenciais com ninguém</li>
                            <li>Altere sua senha regularmente</li>
                            <li>Verifique regularmente as atividades da sua conta</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Session Management -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Sessões Ativas</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($activeSessions)): ?>
                        <?php foreach ($activeSessions as $session): ?>
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <i class="bi bi-<?= $session['device_type'] === 'mobile' ? 'phone' : 'laptop' ?> 
                                          fa-2x text-gray-300"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="small fw-bold">
                                    <?= htmlspecialchars($session['device_name']) ?>
                                    <?php if ($session['is_current']): ?>
                                    <span class="badge bg-primary">Atual</span>
                                    <?php endif; ?>
                                </div>
                                <div class="small text-muted">
                                    <?= htmlspecialchars($session['location']) ?> ·
                                    <?= htmlspecialchars($session['browser']) ?> ·
                                    Último acesso: <?= (new DateTime($session['last_activity']))->format('d/m H:i') ?>
                                </div>
                            </div>
                            <?php if (!$session['is_current']): ?>
                            <div class="flex-shrink-0">
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger"
                                        onclick="terminateSession('<?= $session['id'] ?>')">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted mb-0">Nenhuma outra sessão ativa</p>
                    <?php endif; ?>

                    <div class="d-grid gap-2 mt-3">
                        <button type="button" 
                                class="btn btn-danger"
                                onclick="terminateAllSessions()">
                            <i class="bi bi-shield-x"></i> Encerrar Todas as Sessões
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Password strength validation
    $('#new_password').on('input', function() {
        const password = $(this).val();
        const strength = calculatePasswordStrength(password);
        updatePasswordStrength(strength);
    });
    
    // Password confirmation validation
    $('#confirm_password').on('input', function() {
        const newPassword = $('#new_password').val();
        const confirmPassword = $(this).val();
        
        if (newPassword === confirmPassword) {
            $(this).removeClass('is-invalid').addClass('is-valid');
        } else {
            $(this).removeClass('is-valid').addClass('is-invalid');
        }
    });
});

function calculatePasswordStrength(password) {
    let strength = 0;
    
    // Length check
    if (password.length >= 8) strength += 25;
    
    // Contains lowercase letters
    if (password.match(/[a-z]+/)) strength += 25;
    
    // Contains uppercase letters
    if (password.match(/[A-Z]+/)) strength += 25;
    
    // Contains numbers or special characters
    if (password.match(/[0-9]+/) || password.match(/[$@#&!]+/)) strength += 25;
    
    return strength;
}

function updatePasswordStrength(strength) {
    const strengthBar = $('#password-strength');
    let strengthClass = 'bg-danger';
    let strengthText = 'Fraca';
    
    if (strength >= 75) {
        strengthClass = 'bg-success';
        strengthText = 'Forte';
    } else if (strength >= 50) {
        strengthClass = 'bg-warning';
        strengthText = 'Média';
    }
    
    strengthBar
        .width(`${strength}%`)
        .removeClass('bg-danger bg-warning bg-success')
        .addClass(strengthClass);
        
    $('#password-strength-text').text(strengthText);
}

async function terminateSession(sessionId) {
    if (!confirm('Tem certeza que deseja encerrar esta sessão?')) {
        return;
    }
    
    try {
        const response = await fetch(`/gcmanager/settings/terminate-session/${sessionId}`, {
            method: 'POST'
        });
        
        if (response.ok) {
            location.reload();
        } else {
            throw new Error('Erro ao encerrar sessão');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Ocorreu um erro ao encerrar a sessão');
    }
}

async function terminateAllSessions() {
    if (!confirm('Tem certeza que deseja encerrar todas as sessões? Você precisará fazer login novamente.')) {
        return;
    }
    
    try {
        const response = await fetch('/settings/terminate-all-sessions', {
            method: 'POST'
        });
        
        if (response.ok) {
            window.location.href = '/login';
        } else {
            throw new Error('Erro ao encerrar sessões');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Ocorreu um erro ao encerrar as sessões');
    }
}
</script>
