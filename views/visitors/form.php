<?php
$title = isset($visitor) ? 'Editar Visitante' : 'Novo Visitante';
$action = isset($visitor) ? "/visitors/{$visitor['id']}/update" : "/visitors";
$data = isset($visitor) ? $visitor : (isset($data) ? $data : []);
$groups = isset($groups) ? $groups : [];
?>

<div class="container-fluid">
    <!-- Page Title -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0"><?= $title ?></h1>
        <a href="/visitors" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>Voltar
        </a>
    </div>

    <!-- Form Card -->
    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="<?= $action ?>" enctype="multipart/form-data" class="needs-validation" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
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
                        <label class="form-label">Estado Civil</label>
                        <select name="marital_status" class="form-select">
                            <option value="">Selecione</option>
                            <option value="single" <?= isset($data['marital_status']) && $data['marital_status'] === 'single' ? 'selected' : '' ?>>Solteiro(a)</option>
                            <option value="married" <?= isset($data['marital_status']) && $data['marital_status'] === 'married' ? 'selected' : '' ?>>Casado(a)</option>
                            <option value="divorced" <?= isset($data['marital_status']) && $data['marital_status'] === 'divorced' ? 'selected' : '' ?>>Divorciado(a)</option>
                            <option value="widowed" <?= isset($data['marital_status']) && $data['marital_status'] === 'widowed' ? 'selected' : '' ?>>Viúvo(a)</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Gênero</label>
                        <select name="gender" class="form-select">
                            <option value="">Selecione</option>
                            <option value="M" <?= isset($data['gender']) && $data['gender'] === 'M' ? 'selected' : '' ?>>Masculino</option>
                            <option value="F" <?= isset($data['gender']) && $data['gender'] === 'F' ? 'selected' : '' ?>>Feminino</option>
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
                               value="<?= htmlspecialchars($data['whatsapp'] ?? '') ?>">
                    </div>
                </div>
                
                <!-- Address -->
                <div class="row mb-4">
                    <div class="col-12 mb-3">
                        <h5 class="card-title">Endereço</h5>
                    </div>
                    
                    <div class="col-md-2 mb-3">
                        <label class="form-label">CEP</label>
                        <input type="text" name="zipcode" class="form-control cep-mask"
                               value="<?= htmlspecialchars($data['zipcode'] ?? '') ?>">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Endereço</label>
                        <input type="text" name="address" class="form-control"
                               value="<?= htmlspecialchars($data['address'] ?? '') ?>">
                    </div>
                    
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Número</label>
                        <input type="text" name="number" class="form-control"
                               value="<?= htmlspecialchars($data['number'] ?? '') ?>">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Complemento</label>
                        <input type="text" name="complement" class="form-control"
                               value="<?= htmlspecialchars($data['complement'] ?? '') ?>">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Bairro</label>
                        <input type="text" name="neighborhood" class="form-control"
                               value="<?= htmlspecialchars($data['neighborhood'] ?? '') ?>">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Cidade</label>
                        <input type="text" name="city" class="form-control"
                               value="<?= htmlspecialchars($data['city'] ?? '') ?>">
                    </div>
                </div>

                <!-- Church Information -->
                <div class="row mb-4">
                    <div class="col-12 mb-3">
                        <h5 class="card-title">Informações da Igreja</h5>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Como conheceu a igreja?</label>
                        <input type="text" name="how_knew_church" class="form-control"
                               value="<?= htmlspecialchars($data['how_knew_church'] ?? '') ?>">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Data da primeira visita</label>
                        <input type="date" name="first_visit_date" class="form-control"
                               value="<?= htmlspecialchars($data['first_visit_date'] ?? '') ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="not_contacted" <?= isset($data['status']) && $data['status'] === 'not_contacted' ? 'selected' : '' ?>>Não Contactado</option>
                            <option value="contacted" <?= isset($data['status']) && $data['status'] === 'contacted' ? 'selected' : '' ?>>Contactado</option>
                            <option value="forwarded_to_group" <?= isset($data['status']) && $data['status'] === 'forwarded_to_group' ? 'selected' : '' ?>>Encaminhado para Grupo</option>
                            <option value="group_member" <?= isset($data['status']) && $data['status'] === 'group_member' ? 'selected' : '' ?>>Membro de Grupo</option>
                            <option value="not_interested" <?= isset($data['status']) && $data['status'] === 'not_interested' ? 'selected' : '' ?>>Não quer participar</option>
                            <option value="wants_online_group" <?= isset($data['status']) && $data['status'] === 'wants_online_group' ? 'selected' : '' ?>>Quer Grupo Online</option>
                            <option value="already_in_group" <?= isset($data['status']) && $data['status'] === 'already_in_group' ? 'selected' : '' ?>>Já participa de Grupo</option>
                        </select>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Deseja participar de um grupo?</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="wants_group" id="wants_group_yes" value="yes" <?= isset($data['wants_group']) && $data['wants_group'] === 'yes' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="wants_group_yes">Sim</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="wants_group" id="wants_group_no" value="no" <?= isset($data['wants_group']) && $data['wants_group'] === 'no' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="wants_group_no">Não</label>
                        </div>
                    </div>

                    <div class="col-md-12 mb-3" id="group_selection" style="<?= isset($data['wants_group']) && $data['wants_group'] === 'yes' ? '' : 'display: none;' ?>">
                        <label class="form-label">Grupo</label>
                        <select name="group_id" class="form-select">
                            <option value="">Selecione um grupo</option>
                            <?php foreach ($groups as $group): ?>
                                <option value="<?= $group['id'] ?>" <?= isset($data['group_id']) && $data['group_id'] == $group['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($group['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-12 mb-3" id="available_days_section" style="<?= isset($data['wants_group']) && $data['wants_group'] === 'yes' ? '' : 'display: none;' ?>">
                        <label class="form-label">Dias disponíveis para participar</label>
                        <div class="row">
                            <?php
                            $days = [
                                'monday' => 'Segunda-feira',
                                'tuesday' => 'Terça-feira',
                                'wednesday' => 'Quarta-feira',
                                'thursday' => 'Quinta-feira',
                                'friday' => 'Sexta-feira',
                                'saturday' => 'Sábado',
                                'sunday' => 'Domingo'
                            ];
                            $selectedDays = isset($data['available_days']) ? explode(',', $data['available_days']) : [];
                            foreach ($days as $value => $label):
                            ?>
                            <div class="col-md-4 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="available_days[]" 
                                           value="<?= $value ?>" id="day_<?= $value ?>"
                                           <?= in_array($value, $selectedDays) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="day_<?= $value ?>">
                                        <?= $label ?>
                                    </label>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Prayer Request -->
                <div class="row mb-4">
                    <div class="col-12 mb-3">
                        <h5 class="card-title">Pedido de Oração</h5>
                    </div>
                    
                    <div class="col-12">
                        <textarea class="form-control" name="prayer_requests" rows="3" placeholder="Digite aqui o pedido de oração..."><?= htmlspecialchars($data['prayer_requests'] ?? '') ?></textarea>
                    </div>
                </div>

                <!-- Follow-up -->
                <div class="row mb-4">
                    <div class="col-12 mb-3">
                        <h5 class="card-title">Follow-up</h5>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="follow_up_date" class="form-label">Data do Follow-up</label>
                        <input type="date" class="form-control" id="follow_up_date" name="follow_up_date"
                               value="<?= htmlspecialchars($data['follow_up_date'] ?? '') ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="follow_up_status" class="form-label">Status do Follow-up</label>
                        <select class="form-select" id="follow_up_status" name="follow_up_status">
                            <option value="pending" <?= isset($data['follow_up_status']) && $data['follow_up_status'] === 'pending' ? 'selected' : '' ?>>Pendente</option>
                            <option value="completed" <?= isset($data['follow_up_status']) && $data['follow_up_status'] === 'completed' ? 'selected' : '' ?>>Concluído</option>
                            <option value="cancelled" <?= isset($data['follow_up_status']) && $data['follow_up_status'] === 'cancelled' ? 'selected' : '' ?>>Cancelado</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="follow_up_notes" class="form-label">Observações do Follow-up</label>
                        <textarea class="form-control" id="follow_up_notes" name="follow_up_notes" rows="3" 
                                  placeholder="Observações sobre o follow-up..."><?= htmlspecialchars($data['follow_up_notes'] ?? '') ?></textarea>
                    </div>
                </div>

                <!-- Photo -->
                <div class="row mb-4">
                    <div class="col-12 mb-3">
                        <h5 class="card-title">Foto</h5>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="photo" class="form-label">Foto do Visitante</label>
                        <?php if (isset($data['photo']) && $data['photo']): ?>
                            <div class="mb-2">
                                <img src="<?= htmlspecialchars($data['photo']) ?>" alt="Foto atual" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="row">
                    <div class="col-12 d-flex justify-content-end gap-2">
                        <a href="/visitors" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i><?= isset($visitor) ? 'Salvar Alterações' : 'Cadastrar Visitante' ?>
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
    var forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
});

$(document).ready(function() {
    const wantsGroupYes = document.getElementById('wants_group_yes');
    const wantsGroupNo = document.getElementById('wants_group_no');
    const groupSelection = document.getElementById('group_selection');
    const availableDaysSection = document.getElementById('available_days_section');

    function toggleGroupFields() {
        const showFields = wantsGroupYes.checked;
        groupSelection.style.display = showFields ? '' : 'none';
        availableDaysSection.style.display = showFields ? '' : 'none';
    }

    wantsGroupYes.addEventListener('change', toggleGroupFields);
    wantsGroupNo.addEventListener('change', toggleGroupFields);

    toggleGroupFields();
});
