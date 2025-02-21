<?php
$title = 'Ficha de Visitante';
$styles = '<link href="/assets/css/public.css" rel="stylesheet">';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="text-center mb-4">Ficha de Visitante</h2>
                    
                    <?php if (!empty($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?= $_SESSION['error'] ?>
                            <?php unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="/public/visitor/store" method="POST" class="needs-validation" novalidate>
                        <!-- Dados Pessoais -->
                        <h5 class="mb-3">Dados Pessoais</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Nome Completo *</label>
                                <input type="text" name="name" class="form-control" required>
                                <div class="invalid-feedback">Por favor, informe seu nome.</div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Data de Nascimento</label>
                                <input type="date" name="birth_date" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Gênero</label>
                                <select name="gender" class="form-select">
                                    <option value="">Selecione...</option>
                                    <option value="M">Masculino</option>
                                    <option value="F">Feminino</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Estado Civil</label>
                                <select name="marital_status" class="form-select">
                                    <option value="">Selecione...</option>
                                    <option value="single">Solteiro(a)</option>
                                    <option value="married">Casado(a)</option>
                                    <option value="divorced">Divorciado(a)</option>
                                    <option value="widowed">Viúvo(a)</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Contato -->
                        <h5 class="mb-3">Contato</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Telefone</label>
                                <input type="tel" name="phone" class="form-control phone-mask">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">WhatsApp</label>
                                <input type="tel" name="whatsapp" class="form-control phone-mask">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">E-mail</label>
                                <input type="email" name="email" class="form-control">
                            </div>
                        </div>
                        
                        <!-- Endereço -->
                        <h5 class="mb-3">Endereço</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">CEP</label>
                                <input type="text" name="zipcode" class="form-control cep-mask">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Cidade</label>
                                <input type="text" name="city" class="form-control">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Endereço</label>
                                <input type="text" name="address" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Número</label>
                                <input type="text" name="number" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Complemento</label>
                                <input type="text" name="complement" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Bairro</label>
                                <input type="text" name="neighborhood" class="form-control">
                            </div>
                        </div>
                        
                        <!-- Informações Adicionais -->
                        <h5 class="mb-3">Informações Adicionais</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <label class="form-label">Como conheceu a igreja?</label>
                                <select name="how_knew_church" class="form-select">
                                    <option value="">Selecione...</option>
                                    <option value="indication">Indicação</option>
                                    <option value="social_media">Redes Sociais</option>
                                    <option value="event">Evento</option>
                                    <option value="passing_by">Passando em frente</option>
                                    <option value="other">Outro</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Pedidos de Oração</label>
                                <textarea name="prayer_requests" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Observações</label>
                                <textarea name="observations" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Enviar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts específicos -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validação do formulário
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
    
    // Máscaras
    $('.phone-mask').mask('(00) 00000-0000');
    $('.cep-mask').mask('00000-000');
    
    // Busca de CEP
    $('.cep-mask').on('blur', function() {
        var cep = $(this).val().replace(/\D/g, '');
        if (cep.length === 8) {
            $.get(`https://viacep.com.br/ws/${cep}/json/`, function(data) {
                if (!data.erro) {
                    $('input[name="address"]').val(data.logradouro);
                    $('input[name="neighborhood"]').val(data.bairro);
                    $('input[name="city"]').val(data.localidade);
                }
            });
        }
    });
});
</script>
