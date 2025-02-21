<div class="container-fluid">
    <!-- Page Title -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Meu Perfil</h1>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Profile Information -->
        <div class="col-xl-4 order-xl-1">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informações do Perfil</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img src="<?= $profile['avatar'] ?? '/assets/img/default-avatar.png' ?>" 
                             class="img-profile rounded-circle" 
                             style="width: 150px; height: 150px; object-fit: cover;">
                        <h4 class="mt-3"><?= htmlspecialchars($profile['name']) ?></h4>
                        <p class="text-muted mb-1"><?= htmlspecialchars($profile['role']) ?></p>
                        <p class="text-muted mb-4">
                            Membro desde <?= (new DateTime($profile['joined_date']))->format('d/m/Y') ?>
                        </p>
                    </div>

                    <div class="border-top pt-3">
                        <div class="row">
                            <div class="col-sm-4">
                                <h6 class="mb-0">Mensagens</h6>
                            </div>
                            <div class="col-sm-8 text-secondary">
                                <?= $stats['total_messages'] ?>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-4">
                                <h6 class="mb-0">Notificações</h6>
                            </div>
                            <div class="col-sm-8 text-secondary">
                                <?= $stats['total_notifications'] ?>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-4">
                                <h6 class="mb-0">Escalas</h6>
                            </div>
                            <div class="col-sm-8 text-secondary">
                                <?= $stats['total_schedules'] ?>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-4">
                                <h6 class="mb-0">Transações</h6>
                            </div>
                            <div class="col-sm-8 text-secondary">
                                <?= $stats['total_transactions'] ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Atividade Recente</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <?php foreach ($recentActivity as $activity): ?>
                        <div class="timeline-item">
                            <div class="timeline-item-marker">
                                <div class="timeline-item-marker-indicator bg-<?= match($activity['type']) {
                                    'message' => 'primary',
                                    'notification' => 'info',
                                    'schedule' => 'success',
                                    'transaction' => 'warning'
                                } ?>">
                                    <i class="bi bi-<?= match($activity['type']) {
                                        'message' => 'envelope',
                                        'notification' => 'bell',
                                        'schedule' => 'calendar-event',
                                        'transaction' => 'cash'
                                    } ?>"></i>
                                </div>
                                <div class="timeline-item-time">
                                    <?= (new DateTime($activity['created_at']))->format('d/m H:i') ?>
                                </div>
                            </div>
                            <div class="timeline-item-content">
                                <?= htmlspecialchars($activity['title']) ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Form -->
        <div class="col-xl-8 order-xl-0">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Editar Perfil</h6>
                </div>
                <div class="card-body">
                    <form action="/settings/update-profile" method="POST" enctype="multipart/form-data">
                        <!-- Personal Information -->
                        <h5 class="heading-small text-muted mb-4">Informações Pessoais</h5>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nome Completo</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="name" 
                                           name="name" 
                                           value="<?= htmlspecialchars($profile['name']) ?>" 
                                           required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">E-mail</label>
                                    <input type="email" 
                                           class="form-control" 
                                           value="<?= htmlspecialchars($profile['email']) ?>" 
                                           disabled>
                                    <small class="form-text text-muted">
                                        Para alterar seu e-mail, acesse as configurações de segurança
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Telefone</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="phone" 
                                           name="phone" 
                                           value="<?= htmlspecialchars($profile['phone'] ?? '') ?>"
                                           data-mask="(00) 00000-0000">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="birth_date" class="form-label">Data de Nascimento</label>
                                    <input type="date" 
                                           class="form-control" 
                                           id="birth_date" 
                                           name="birth_date" 
                                           value="<?= $profile['birth_date'] ?? '' ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Address -->
                        <h5 class="heading-small text-muted mb-4">Endereço</h5>
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="mb-3">
                                    <label for="address" class="form-label">Endereço</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="address" 
                                           name="address" 
                                           value="<?= htmlspecialchars($profile['address'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <label for="postal_code" class="form-label">CEP</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="postal_code" 
                                           name="postal_code" 
                                           value="<?= htmlspecialchars($profile['postal_code'] ?? '') ?>"
                                           data-mask="00000-000">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="city" class="form-label">Cidade</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="city" 
                                           name="city" 
                                           value="<?= htmlspecialchars($profile['city'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="state" class="form-label">Estado</label>
                                    <select class="form-select" id="state" name="state">
                                        <option value="">Selecione...</option>
                                        <?php
                                        $states = [
                                            'AC' => 'Acre', 'AL' => 'Alagoas', 'AP' => 'Amapá',
                                            'AM' => 'Amazonas', 'BA' => 'Bahia', 'CE' => 'Ceará',
                                            'DF' => 'Distrito Federal', 'ES' => 'Espírito Santo',
                                            'GO' => 'Goiás', 'MA' => 'Maranhão', 'MT' => 'Mato Grosso',
                                            'MS' => 'Mato Grosso do Sul', 'MG' => 'Minas Gerais',
                                            'PA' => 'Pará', 'PB' => 'Paraíba', 'PR' => 'Paraná',
                                            'PE' => 'Pernambuco', 'PI' => 'Piauí', 'RJ' => 'Rio de Janeiro',
                                            'RN' => 'Rio Grande do Norte', 'RS' => 'Rio Grande do Sul',
                                            'RO' => 'Rondônia', 'RR' => 'Roraima', 'SC' => 'Santa Catarina',
                                            'SP' => 'São Paulo', 'SE' => 'Sergipe', 'TO' => 'Tocantins'
                                        ];
                                        
                                        foreach ($states as $uf => $name):
                                        ?>
                                        <option value="<?= $uf ?>" <?= ($profile['state'] ?? '') === $uf ? 'selected' : '' ?>>
                                            <?= $name ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Bio -->
                        <h5 class="heading-small text-muted mb-4">Sobre Você</h5>
                        <div class="mb-3">
                            <label for="bio" class="form-label">Biografia</label>
                            <textarea class="form-control" 
                                      id="bio" 
                                      name="bio" 
                                      rows="3"><?= htmlspecialchars($profile['bio'] ?? '') ?></textarea>
                        </div>

                        <!-- Avatar -->
                        <h5 class="heading-small text-muted mb-4">Foto do Perfil</h5>
                        <div class="mb-3">
                            <label for="avatar" class="form-label">Upload de Nova Foto</label>
                            <input type="file" 
                                   class="form-control" 
                                   id="avatar" 
                                   name="avatar" 
                                   accept="image/jpeg,image/png,image/gif">
                            <small class="form-text text-muted">
                                Formatos permitidos: JPG, PNG, GIF. Tamanho máximo: 5MB
                            </small>
                        </div>

                        <!-- Social Media -->
                        <h5 class="heading-small text-muted mb-4">Redes Sociais</h5>
                        <?php
                        $socialMedia = json_decode($profile['social_media'] ?? '{}', true) ?: [];
                        $networks = [
                            'facebook' => 'Facebook',
                            'instagram' => 'Instagram',
                            'twitter' => 'Twitter',
                            'linkedin' => 'LinkedIn'
                        ];
                        
                        foreach ($networks as $key => $label):
                        ?>
                        <div class="mb-3">
                            <label for="social_<?= $key ?>" class="form-label"><?= $label ?></label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-<?= $key ?>"></i>
                                </span>
                                <input type="text" 
                                       class="form-control" 
                                       id="social_<?= $key ?>" 
                                       name="social_media[<?= $key ?>]" 
                                       value="<?= htmlspecialchars($socialMedia[$key] ?? '') ?>"
                                       placeholder="URL do seu perfil">
                            </div>
                        </div>
                        <?php endforeach; ?>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery Mask Plugin -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize masks
    $('#phone').mask('(00) 00000-0000');
    $('#postal_code').mask('00000-000');
    
    // Preview avatar image before upload
    $('#avatar').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('.img-profile').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Auto-fill address using CEP
    $('#postal_code').blur(function() {
        const cep = $(this).val().replace(/\D/g, '');
        
        if (cep.length === 8) {
            $.getJSON(`https://viacep.com.br/ws/${cep}/json/`, function(data) {
                if (!data.erro) {
                    $('#address').val(data.logradouro);
                    $('#city').val(data.localidade);
                    $('#state').val(data.uf);
                }
            });
        }
    });
});
</script>

<style>
.timeline {
    position: relative;
    padding: 1rem;
}

.timeline-item {
    position: relative;
    padding-left: 3rem;
    margin-bottom: 1rem;
}

.timeline-item:last-child {
    margin-bottom: 0;
}

.timeline-item-marker {
    position: absolute;
    left: 0;
    top: 0;
}

.timeline-item-marker-indicator {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2rem;
    height: 2rem;
    border-radius: 100%;
    color: #fff;
}

.timeline-item-marker-indicator i {
    font-size: 1rem;
}

.timeline-item-time {
    font-size: 0.875rem;
    color: #6c757d;
    margin-top: 0.25rem;
}

.timeline-item-content {
    padding-top: 0.25rem;
}
</style>
