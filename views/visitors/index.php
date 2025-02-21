<?php
$title = 'Visitantes';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<div class="container-fluid py-4">
    <input type="hidden" name="_token" value="<?= isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : '' ?>">
    <!-- Page Title -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Visitantes</h1>
        <div>
            </a>
            <a href="<?= url('/visitors/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Novo Visitante
            </a>
        </div>
    </div>

    <?php require '_indicators.php'; ?>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="<?= url('/visitors') ?>" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="search" class="form-control" placeholder="Nome, email ou telefone" 
                           value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="not_contacted" <?= ($_GET['status'] ?? '') === 'not_contacted' ? 'selected' : '' ?>>Não Contactado</option>
                        <option value="contacted" <?= ($_GET['status'] ?? '') === 'contacted' ? 'selected' : '' ?>>Contactado</option>
                        <option value="forwarded_to_group" <?= ($_GET['status'] ?? '') === 'forwarded_to_group' ? 'selected' : '' ?>>Encaminhado para Grupo</option>
                        <option value="group_member" <?= ($_GET['status'] ?? '') === 'group_member' ? 'selected' : '' ?>>Membro de Grupo</option>
                        <option value="not_interested" <?= ($_GET['status'] ?? '') === 'not_interested' ? 'selected' : '' ?>>Não quer participar</option>
                        <option value="wants_online_group" <?= ($_GET['status'] ?? '') === 'wants_online_group' ? 'selected' : '' ?>>Quer Grupo Online</option>
                        <option value="already_in_group" <?= ($_GET['status'] ?? '') === 'already_in_group' ? 'selected' : '' ?>>Já participa de Grupo</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Grupo</label>
                    <select name="gc_id" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach ($groups as $group): ?>
                            <option value="<?= $group['id'] ?>" <?= ($_GET['gc_id'] ?? '') == $group['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($group['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Visitors List -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Contato</th>
                            <th>Bairro</th>
                            <th>Dias Disponíveis</th>
                            <th>Grupo</th>
                            <th>Cadastro</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($visitors)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-info-circle me-2"></i>Nenhum visitante encontrado
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php 
                            $dayTranslations = [
                                'monday' => 'Segunda',
                                'tuesday' => 'Terça',
                                'wednesday' => 'Quarta',
                                'thursday' => 'Quinta',
                                'friday' => 'Sexta',
                                'saturday' => 'Sábado',
                                'sunday' => 'Domingo'
                            ];
                            ?>
                            <?php foreach ($visitors as $visitor): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-primary text-white me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border-radius: 50%;">
                                                <?= strtoupper(substr($visitor['name'], 0, 1)) ?>
                                            </div>
                                            <span><?= htmlspecialchars($visitor['name']) ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="contact-info">
                                            <?php if (!empty($visitor['phone'])): ?>
                                                <div class="mb-1 d-flex align-items-center">
                                                    <a href="tel:<?= htmlspecialchars($visitor['phone']) ?>" class="text-decoration-none me-2">
                                                        <i class="fas fa-phone-alt me-1"></i>
                                                        <span><?= htmlspecialchars(trim($visitor['phone'])) ?></span>
                                                    </a>
                                                    <a href="https://wa.me/<?= formatPhoneForWhatsApp($visitor['phone']) ?>" 
                                                       target="_blank" 
                                                       class="btn btn-success btn-sm" 
                                                       title="Abrir WhatsApp">
                                                        <i class="fab fa-whatsapp"></i>
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (!empty($visitor['whatsapp']) && trim($visitor['whatsapp']) !== trim($visitor['phone'])): ?>
                                                <div class="mb-1">
                                                    <i class="fab fa-whatsapp me-1 text-success"></i>
                                                    <span><?= htmlspecialchars(trim($visitor['whatsapp'])) ?></span>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (!empty($visitor['email'])): ?>
                                                <div>
                                                    <a href="mailto:<?= htmlspecialchars($visitor['email']) ?>" class="text-decoration-none">
                                                        <i class="fas fa-envelope me-1"></i>
                                                        <span><?= htmlspecialchars($visitor['email']) ?></span>
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (empty($visitor['phone']) && empty($visitor['whatsapp']) && empty($visitor['email'])): ?>
                                                <span class="text-muted">Não informado</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($visitor['neighborhood'] ?? '') ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($visitor['available_days'])): ?>
                                            <?php 
                                            $days = explode(',', $visitor['available_days']);
                                            foreach ($days as $day): ?>
                                                <span class="badge bg-secondary me-1">
                                                    <?= $dayTranslations[$day] ?? $day ?>
                                                </span>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($visitor['group_name'])): ?>
                                            <span class="badge bg-info">
                                                <?= htmlspecialchars($visitor['group_name']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($visitor['created_at'])) ?></td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#quickViewModal"
                                                    data-visitor-id="<?= $visitor['id'] ?>"
                                                    title="Visualização Rápida">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a href="<?= url('/visitors/' . $visitor['id'] . '/edit') ?>" 
                                               class="btn btn-sm btn-outline-primary"
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="deleteVisitor(<?= $visitor['id'] ?>)"
                                                    title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Visualização Rápida -->
<div class="modal fade" id="quickViewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalhes do Visitante</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Nome Completo</label>
                        <p class="mb-0" id="visitorName"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Data de Nascimento</label>
                        <p class="mb-0" id="visitorBirthDate"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Telefone</label>
                        <p class="mb-0" id="visitorPhone"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">E-mail</label>
                        <p class="mb-0" id="visitorEmail"></p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label text-muted">Endereço</label>
                        <p class="mb-0" id="visitorAddress"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Bairro</label>
                        <p class="mb-0" id="visitorNeighborhood"></p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label text-muted">Como Conheceu a Igreja</label>
                        <p class="mb-0" id="visitorHowKnewChurch"></p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label text-muted">Pedidos de Oração</label>
                        <p class="mb-0" id="visitorPrayerRequests"></p>
                    </div>
                    <div class="col-12">
                        <label class="form-label text-muted">Observações</label>
                        <p class="mb-0" id="visitorObservations"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" id="editVisitorBtn" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i>Editar
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('quickViewModal');
        const bsModal = new bootstrap.Modal(modal);

        // Limpar os campos quando o modal for fechado
        modal.addEventListener('hidden.bs.modal', function () {
            document.getElementById('visitorName').textContent = '';
            document.getElementById('visitorBirthDate').textContent = '';
            document.getElementById('visitorPhone').textContent = '';
            document.getElementById('visitorEmail').textContent = '';
            document.getElementById('visitorAddress').textContent = '';
            document.getElementById('visitorNeighborhood').textContent = '';
            document.getElementById('visitorHowKnewChurch').textContent = '';
            document.getElementById('visitorPrayerRequests').textContent = '';
            document.getElementById('visitorObservations').textContent = '';
        });

        document.querySelectorAll('.view-visitor').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const data = this.dataset;
                document.getElementById('visitorName').textContent = data.name;
                document.getElementById('visitorBirthDate').textContent = data.birthDate || '-';
                document.getElementById('visitorPhone').textContent = data.phone || '-';
                document.getElementById('visitorEmail').textContent = data.email || '-';
                document.getElementById('visitorAddress').textContent = data.address || '-';
                document.getElementById('visitorNeighborhood').textContent = data.neighborhood || '-';
                document.getElementById('visitorHowKnewChurch').textContent = data.howKnewChurch || '-';
                document.getElementById('visitorPrayerRequests').textContent = data.prayerRequests || '-';
                document.getElementById('visitorObservations').textContent = data.observations || '-';
                
                // Atualizar o link de edição
                document.getElementById('editVisitorBtn').href = `${window.location.origin}${url('/visitors/' + data.id + '/edit')}`;

                bsModal.show();
            });
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar todos os tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>

<script>
    function deleteVisitor(visitorId) {
        const token = document.querySelector('input[name="_token"]').value;
        if (!token) {
            Swal.fire('Erro!', 'Token de segurança não encontrado. Recarregue a página.', 'error');
            return;
        }

        Swal.fire({
            title: 'Tem certeza?',
            text: "Esta ação não poderá ser revertida!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/visitors/${visitorId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire(
                            'Excluído!',
                            'O visitante foi excluído com sucesso.',
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire(
                            'Erro!',
                            data.message || 'Ocorreu um erro ao excluir o visitante.',
                            'error'
                        );
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire(
                        'Erro!',
                        'Ocorreu um erro ao excluir o visitante.',
                        'error'
                    );
                });
            }
        });
    }
</script>
