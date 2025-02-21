<?php
/**
 * Lista de participantes do grupo
 * Espera receber a variável $participants
 */
?>
<!-- Lista de Participantes -->
<div class="card mt-4">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-users me-1"></i>
                Participantes do Grupo
            </div>
            <div>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addParticipantModal">
                    <i class="fas fa-plus me-1"></i> Adicionar Participante
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($participants)): ?>
            <p class="text-muted">Nenhum participante cadastrado neste grupo.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Telefone</th>
                            <th>Email</th>
                            <th>Dias Disponíveis</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($participants as $participant): ?>
                            <tr>
                                <td><?= htmlspecialchars($participant['name']) ?></td>
                                <td>
                                    <?php if (!empty($participant['phone'])): ?>
                                        <a href="tel:<?= $participant['phone'] ?>">
                                            <?= htmlspecialchars($participant['phone']) ?>
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($participant['email'])): ?>
                                        <a href="mailto:<?= $participant['email'] ?>">
                                            <?= htmlspecialchars($participant['email']) ?>
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    if (!empty($participant['available_days'])) {
                                        $days = explode(',', $participant['available_days']);
                                        $dayNames = [
                                            'sunday' => 'Dom',
                                            'monday' => 'Seg',
                                            'tuesday' => 'Ter',
                                            'wednesday' => 'Qua',
                                            'thursday' => 'Qui',
                                            'friday' => 'Sex',
                                            'saturday' => 'Sáb'
                                        ];
                                        $formattedDays = array_map(function($day) use ($dayNames) {
                                            return $dayNames[$day] ?? $day;
                                        }, $days);
                                        echo implode(', ', $formattedDays);
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="/visitors/<?= $participant['id'] ?>/edit" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="/visitors/<?= $participant['id'] ?>" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Adicionar Participante -->
<div class="modal fade" id="addParticipantModal" tabindex="-1" aria-labelledby="addParticipantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addParticipantModalLabel">Adicionar Participante ao Grupo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="searchVisitorForm" class="mb-4">
                    <div class="input-group">
                        <input type="text" class="form-control" id="visitorSearch" 
                               placeholder="Digite o nome, email ou telefone do visitante">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </form>

                <div id="searchResults" class="d-none">
                    <h6 class="mb-3">Resultados da Busca:</h6>
                    <div class="list-group" id="visitorList"></div>
                </div>

                <div id="noResults" class="alert alert-info d-none">
                    Nenhum visitante encontrado. 
                    <a href="/visitors/create" class="alert-link">Cadastrar novo visitante</a>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('searchVisitorForm');
    const searchInput = document.getElementById('visitorSearch');
    const searchResults = document.getElementById('searchResults');
    const visitorList = document.getElementById('visitorList');
    const noResults = document.getElementById('noResults');
    const groupId = <?= $group['id'] ?>;

    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const searchTerm = searchInput.value.trim();
        
        if (searchTerm.length < 2) {
            alert('Digite pelo menos 2 caracteres para buscar.');
            return;
        }

        fetch(`/api/visitors/search?q=${encodeURIComponent(searchTerm)}`)
            .then(response => response.json())
            .then(data => {
                visitorList.innerHTML = '';
                
                if (data.length > 0) {
                    data.forEach(user => {
                        const item = document.createElement('a');
                        item.href = '#';
                        item.className = 'list-group-item list-group-item-action';
                        item.innerHTML = `
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">${user.name}</h6>
                                <button class="btn btn-sm btn-success add-user" data-user-id="${user.id}">
                                    <i class="fas fa-plus"></i> Adicionar
                                </button>
                            </div>
                            <p class="mb-1">
                                ${user.phone ? `<i class="fas fa-phone me-1"></i>${user.phone}<br>` : ''}
                                ${user.email ? `<i class="fas fa-envelope me-1"></i>${user.email}` : ''}
                            </p>
                        `;
                        
                        item.querySelector('.add-user').addEventListener('click', function(e) {
                            e.preventDefault();
                            addUserToGroup(user.id);
                        });
                        
                        visitorList.appendChild(item);
                    });
                    
                    searchResults.classList.remove('d-none');
                    noResults.classList.add('d-none');
                } else {
                    searchResults.classList.add('d-none');
                    noResults.classList.remove('d-none');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erro ao buscar usuários. Por favor, tente novamente.');
            });
    });

    function addUserToGroup(userId) {
        fetch('/api/groups/add-participant', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                group_id: groupId,
                user_id: userId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); // Recarrega a página para mostrar o novo participante
            } else {
                throw new Error(data.error || 'Erro ao adicionar usuário ao grupo');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message || 'Erro ao adicionar usuário ao grupo. Por favor, tente novamente.');
        });
    }
});
</script>
