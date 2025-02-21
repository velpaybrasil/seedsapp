<?php
/**
 * Seção de Membros e Pré-inscrições
 * Inclui as tabs, tabelas e modais necessários
 */
?>

<!-- Tabs para Participantes -->
<div class="card mb-4">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="membersTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="members-tab" data-bs-toggle="tab" 
                    data-bs-target="#members" type="button" role="tab">
                    <i class="fas fa-users me-1"></i> Membros
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pending-tab" data-bs-toggle="tab" 
                    data-bs-target="#pending" type="button" role="tab">
                    <i class="fas fa-clock me-1"></i> Pré-inscrições
                    <span class="badge bg-primary pending-count" style="display: none;">0</span>
                </button>
            </li>
        </ul>
    </div>
    
    <div class="card-body">
        <div class="tab-content" id="membersTabsContent">
            <!-- Tab Membros -->
            <div class="tab-pane fade show active" id="members" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Membros do Grupo</h5>
                    <a href="<?= url("groups/{$group['id']}/pre-register") ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Nova Pré-inscrição
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover" id="membersTable">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Contato</th>
                                <th>Data de Entrada</th>
                                <th>Função</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Preenchido via JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab Pré-inscrições -->
            <div class="tab-pane fade" id="pending" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Pré-inscrições Pendentes</h5>
                    <a href="<?= url("groups/{$group['id']}/pre-register") ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Nova Pré-inscrição
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover" id="pendingTable">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Contato</th>
                                <th>Data da Solicitação</th>
                                <th>Observações</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Preenchido via JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Histórico -->
<div class="modal fade" id="historyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Histórico do Membro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Status Anterior</th>
                                <th>Novo Status</th>
                                <th>Alterado Por</th>
                                <th>Observações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Preenchido via JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Aprovação/Rejeição -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Atualizar Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="statusForm">
                    <input type="hidden" id="memberId" name="memberId">
                    <input type="hidden" id="newStatus" name="newStatus">
                    
                    <div class="mb-3">
                        <label for="statusNotes" class="form-label">Observações</label>
                        <textarea class="form-control" id="statusNotes" name="notes" rows="3"
                            placeholder="Opcional: adicione uma observação sobre esta decisão"></textarea>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <span class="status-message"></span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmStatus">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<!-- Group Members Manager -->
<script src="/js/group-members.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const manager = new GroupMemberManager(<?= $group['id'] ?>);
});
</script>
