<?php
$title = isset($visitor) ? 'Editar Visitante' : 'Novo Visitante';
$action = isset($visitor) ? "/gcmanager/visitors/{$visitor['id']}/update" : "/gcmanager/visitors/store";
$data = isset($visitor) ? $visitor : (isset($data) ? $data : []);
?>

<div class="container-fluid">
    <!-- Page Title -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0"><?= $title ?></h1>
        <a href="/gcmanager/visitors" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>Voltar
        </a>
    </div>

    <!-- Form Card -->
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="<?= $action ?>" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                <!-- Personal Information -->
                <div class="row mb-4">
                    <div class="col-12 mb-3">
                        <h5 class="card-title">Informações Pessoais</h5>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Nome *</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= htmlspecialchars($data['name'] ?? '') ?>" required>
                        <div class="invalid-feedback">
                            Por favor, informe o nome.
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="birth_date" class="form-label">Data de Nascimento</label>
                        <input type="date" class="form-control" id="birth_date" name="birth_date"
                               value="<?= htmlspecialchars($data['birth_date'] ?? '') ?>">
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Gênero</label>
                        <select name="gender" class="form-select">
                            <option value="">Selecione</option>
                            <option value="M" <?= isset($visitor['gender']) && $visitor['gender'] === 'M' ? 'selected' : '' ?>>Masculino</option>
                            <option value="F" <?= isset($visitor['gender']) && $visitor['gender'] === 'F' ? 'selected' : '' ?>>Feminino</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                               value="<?= htmlspecialchars($data['email'] ?? '') ?>">
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="phone" class="form-label">Telefone</label>
                        <input type="tel" class="form-control phone-mask" id="phone" name="phone"
                               value="<?= htmlspecialchars($data['phone'] ?? '') ?>">
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label class="form-label">WhatsApp</label>
                        <input type="text" name="whatsapp" class="form-control phone-mask"
                               value="<?= htmlspecialchars($visitor['whatsapp'] ?? '') ?>">
                    </div>
                </div>
                
                <!-- Address -->
                <div class="row mb-4">
                    <div class="col-12 mb-3">
                        <h5 class="card-title">Endereço</h5>
                    </div>
                    
                    <div class="col-md-2 mb-3">
                        <label class="form-label">CEP</label>
                        <input type="text" name="zipcode" class="form-control zipcode-mask"
                               value="<?= htmlspecialchars($visitor['zipcode'] ?? '') ?>">
                    </div>
                    
                    <div class="col-md-8 mb-3">
                        <label for="address" class="form-label">Logradouro</label>
                        <input type="text" class="form-control" id="address" name="address"
                               value="<?= htmlspecialchars($data['address'] ?? '') ?>">
                    </div>
                    
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Número</label>
                        <input type="text" name="number" class="form-control"
                               value="<?= htmlspecialchars($visitor['number'] ?? '') ?>">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Complemento</label>
                        <input type="text" name="complement" class="form-control"
                               value="<?= htmlspecialchars($visitor['complement'] ?? '') ?>">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Bairro</label>
                        <input type="text" name="neighborhood" class="form-control"
                               value="<?= htmlspecialchars($visitor['neighborhood'] ?? '') ?>">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Cidade</label>
                        <input type="text" name="city" class="form-control"
                               value="<?= htmlspecialchars($visitor['city'] ?? '') ?>">
                    </div>
                </div>
                
                <!-- Church Information -->
                <div class="row mb-4">
                    <div class="col-12 mb-3">
                        <h5 class="card-title">Informações da Igreja</h5>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="first_visit_date" class="form-label">Data da Primeira Visita</label>
                        <input type="date" class="form-control" id="first_visit_date" name="first_visit_date"
                               value="<?= htmlspecialchars($data['first_visit_date'] ?? '') ?>">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="how_knew_church" class="form-label">Como Conheceu a Igreja</label>
                        <select class="form-select" id="how_knew_church" name="how_knew_church">
                            <option value="">Selecione...</option>
                            <option value="indicacao" <?= ($data['how_knew_church'] ?? '') === 'indicacao' ? 'selected' : '' ?>>Indicação</option>
                            <option value="redes_sociais" <?= ($data['how_knew_church'] ?? '') === 'redes_sociais' ? 'selected' : '' ?>>Redes Sociais</option>
                            <option value="evento" <?= ($data['how_knew_church'] ?? '') === 'evento' ? 'selected' : '' ?>>Evento</option>
                            <option value="passou_em_frente" <?= ($data['how_knew_church'] ?? '') === 'passou_em_frente' ? 'selected' : '' ?>>Passou em Frente</option>
                            <option value="outro" <?= ($data['how_knew_church'] ?? '') === 'outro' ? 'selected' : '' ?>>Outro</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="new" <?= ($data['status'] ?? '') === 'new' ? 'selected' : '' ?>>Novo</option>
                            <option value="in_progress" <?= ($data['status'] ?? '') === 'in_progress' ? 'selected' : '' ?>>Em Acompanhamento</option>
                            <option value="converted" <?= ($data['status'] ?? '') === 'converted' ? 'selected' : '' ?>>Convertido</option>
                            <option value="member" <?= ($data['status'] ?? '') === 'member' ? 'selected' : '' ?>>Membro</option>
                            <option value="inactive" <?= ($data['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inativo</option>
                        </select>
                        <div class="invalid-feedback">Por favor, selecione o status.</div>
                    </div>
                    
                    <div class="col-12 mb-3">
                        <label for="prayer_requests" class="form-label">Pedidos de Oração</label>
                        <textarea class="form-control" id="prayer_requests" name="prayer_requests" rows="3"
                                  ><?= htmlspecialchars($data['prayer_requests'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="col-12">
                        <label for="observations" class="form-label">Observações</label>
                        <textarea class="form-control" id="observations" name="observations" rows="3"
                                  ><?= htmlspecialchars($data['observations'] ?? '') ?></textarea>
                    </div>
                </div>
                
                <!-- Photo -->
                <div class="row mb-4">
                    <div class="col-12 mb-3">
                        <h5 class="card-title">Foto</h5>
                    </div>
                    
                    <div class="col-12">
                        <div class="d-flex align-items-center">
                            <?php if (isset($visitor['photo']) && $visitor['photo']): ?>
                                <img src="<?= htmlspecialchars($visitor['photo']) ?>" class="avatar-lg me-3" alt="Foto atual">
                            <?php endif; ?>
                            
                            <div class="flex-grow-1">
                                <input type="file" name="photo" class="form-control" accept="image/*">
                                <small class="form-text text-muted">
                                    Formatos aceitos: JPG, PNG. Tamanho máximo: 2MB
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="row">
                    <div class="col-12 d-flex justify-content-end gap-2">
                        <a href="/gcmanager/visitors" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Salvar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializa validação do formulário
    const form = document.querySelector('.needs-validation');
    form.addEventListener('submit', function(e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.classList.add('was-validated');
    });
    
    // Inicializa máscaras
    const phones = document.querySelectorAll('.phone-mask');
    phones.forEach(phone => {
        App.forms.initPhoneMask(phone);
    });
    
    const zipcodes = document.querySelectorAll('.zipcode-mask');
    zipcodes.forEach(zipcode => {
        zipcode.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 8) value = value.slice(0, 8);
            if (value.length > 5) {
                value = value.slice(0, 5) + '-' + value.slice(5);
            }
            e.target.value = value;
            
            // Se completou o CEP, busca o endereço
            if (value.length === 9) {
                fetch(`https://viacep.com.br/ws/${value.replace('-', '')}/json/`)
                    .then(response => response.json())
                    .then(data => {
                        if (!data.erro) {
                            document.querySelector('[name="address"]').value = data.logradouro;
                            document.querySelector('[name="neighborhood"]').value = data.bairro;
                            document.querySelector('[name="city"]').value = data.localidade;
                        }
                    });
            }
        });
    });
});
</script>
