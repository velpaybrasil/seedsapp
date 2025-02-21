<?php
/**
 * Modal para exibir detalhes do grupo
 */
?>
<div class="modal fade" id="groupDetailsModal" tabindex="-1" aria-labelledby="groupDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="groupDetailsModalLabel">Detalhes do Grupo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Informações Básicas</h6>
                        <p><strong>Nome:</strong> <span id="modal-group-name"></span></p>
                        <p><strong>Descrição:</strong> <span id="modal-group-description"></span></p>
                        <p><strong>Ministério:</strong> <span id="modal-group-ministry"></span></p>
                    </div>
                    <div class="col-md-6">
                        <h6>Reuniões</h6>
                        <p><strong>Dia:</strong> <span id="modal-group-meeting-day"></span></p>
                        <p><strong>Horário:</strong> <span id="modal-group-meeting-time"></span></p>
                        <p><strong>Endereço:</strong> <span id="modal-group-meeting-address"></span></p>
                        <p><strong>Bairro:</strong> <span id="modal-group-neighborhood"></span></p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h6>Liderança</h6>
                        <p><strong>Líderes:</strong> <span id="modal-group-leaders"></span></p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h6>Informações Adicionais</h6>
                        <p><strong>Capacidade:</strong> <span id="modal-group-max-participants"></span> participantes</p>
                        <p><strong>Status:</strong> <span id="modal-group-status"></span></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <a href="#" class="btn btn-primary" id="modal-group-edit-link">Editar</a>
            </div>
        </div>
    </div>
</div>

<script>
function showGroupDetails(groupId) {
    fetch(`/groups/${groupId}/details`)
    .then(response => response.json())
    .then(data => {
        document.getElementById('modal-group-name').textContent = data.name;
        document.getElementById('modal-group-description').textContent = data.description;
        document.getElementById('modal-group-ministry').textContent = data.ministry_name;
        document.getElementById('modal-group-meeting-day').textContent = data.meeting_day;
        document.getElementById('modal-group-meeting-time').textContent = data.meeting_time;
        document.getElementById('modal-group-meeting-address').textContent = data.meeting_address;
        document.getElementById('modal-group-neighborhood').textContent = data.neighborhood;
        document.getElementById('modal-group-leaders').textContent = data.leaders;
        document.getElementById('modal-group-max-participants').textContent = data.max_participants;
        document.getElementById('modal-group-status').textContent = data.status === 'active' ? 'Ativo' : 'Inativo';
        document.getElementById('modal-group-edit-link').href = `/groups/${groupId}/edit`;
        var modal = new bootstrap.Modal(document.getElementById('groupDetailsModal'));
        modal.show();
    });
}
</script>
