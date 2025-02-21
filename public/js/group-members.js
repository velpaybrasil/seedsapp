// Gerenciamento de Membros e Pré-inscrições
class GroupMemberManager {
    constructor(groupId) {
        this.groupId = groupId;
        this.membersTable = null;
        this.pendingTable = null;
        this.historyModal = null;
        this.statusModal = null;
        this.initialize();
    }

    initialize() {
        // Inicializar modais
        this.historyModal = new bootstrap.Modal(document.getElementById('historyModal'));
        this.statusModal = new bootstrap.Modal(document.getElementById('statusModal'));

        // Inicializar DataTables
        this.initializeTables();

        // Carregar dados iniciais
        this.loadMembers();
        this.loadPendingMembers();

        // Configurar event listeners
        this.setupEventListeners();
    }

    initializeTables() {
        this.membersTable = $('#membersTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json'
            },
            order: [[2, 'desc']],
            columns: [
                { data: 'visitor_name' },
                { data: 'contact' },
                { data: 'joined_at' },
                { data: 'role' },
                { data: 'actions', orderable: false }
            ]
        });

        this.pendingTable = $('#pendingTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json'
            },
            order: [[2, 'desc']],
            columns: [
                { data: 'visitor_name' },
                { data: 'contact' },
                { data: 'created_at' },
                { data: 'notes' },
                { data: 'actions', orderable: false }
            ]
        });
    }

    async loadMembers() {
        try {
            const response = await fetch(`/groups/${this.groupId}/members`);
            const data = await response.json();
            
            if (data.success) {
                this.membersTable.clear();
                data.data.forEach(member => {
                    this.membersTable.row.add({
                        visitor_name: member.visitor_name,
                        contact: `${member.email}<br>${member.phone}`,
                        joined_at: new Date(member.joined_at).toLocaleDateString('pt-BR'),
                        role: this.formatRole(member.role),
                        actions: this.getMemberActions(member)
                    });
                });
                this.membersTable.draw();
            }
        } catch (error) {
            console.error('Error loading members:', error);
            this.showToast('error', 'Erro ao carregar membros');
        }
    }

    async loadPendingMembers() {
        try {
            const response = await fetch(`/groups/${this.groupId}/pending-members`);
            const data = await response.json();
            
            if (data.success) {
                this.pendingTable.clear();
                data.data.forEach(member => {
                    this.pendingTable.row.add({
                        visitor_name: member.visitor_name,
                        contact: `${member.email}<br>${member.phone}`,
                        created_at: new Date(member.created_at).toLocaleDateString('pt-BR'),
                        notes: member.notes || '-',
                        actions: this.getPendingActions(member)
                    });
                });
                this.pendingTable.draw();
                
                // Atualizar contador
                this.updatePendingCount(data.data.length);
            }
        } catch (error) {
            console.error('Error loading pending members:', error);
            this.showToast('error', 'Erro ao carregar pré-inscrições');
        }
    }

    formatRole(role) {
        const roles = {
            'member': 'Membro',
            'leader': 'Líder',
            'co-leader': 'Co-líder'
        };
        return roles[role] || role;
    }

    getMemberActions(member) {
        return `
            <button class="btn btn-sm btn-info view-history" data-id="${member.id}">
                <i class="fas fa-history"></i>
            </button>
        `;
    }

    getPendingActions(member) {
        return `
            <div class="btn-group">
                <button class="btn btn-sm btn-success approve-member" data-id="${member.id}">
                    <i class="fas fa-check"></i>
                </button>
                <button class="btn btn-sm btn-danger reject-member" data-id="${member.id}">
                    <i class="fas fa-times"></i>
                </button>
                <button class="btn btn-sm btn-info view-history" data-id="${member.id}">
                    <i class="fas fa-history"></i>
                </button>
            </div>
        `;
    }

    updatePendingCount(count) {
        const badge = document.querySelector('.pending-count');
        if (badge) {
            if (count > 0) {
                badge.textContent = count;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        }
    }

    setupEventListeners() {
        document.addEventListener('click', e => {
            const target = e.target.closest('button');
            if (!target) return;

            const memberId = target.dataset.id;
            if (target.classList.contains('view-history')) {
                this.loadMemberHistory(memberId);
            } else if (target.classList.contains('approve-member')) {
                this.showStatusModal(memberId, 'approved');
            } else if (target.classList.contains('reject-member')) {
                this.showStatusModal(memberId, 'rejected');
            }
        });

        document.getElementById('confirmStatus')?.addEventListener('click', () => {
            this.updateMemberStatus();
        });
    }

    async loadMemberHistory(memberId) {
        try {
            const response = await fetch(`/groups/members/${memberId}/history`);
            const data = await response.json();
            
            if (data.success) {
                const tbody = document.querySelector('#historyModal tbody');
                tbody.innerHTML = '';
                
                data.data.forEach(item => {
                    tbody.innerHTML += `
                        <tr>
                            <td>${new Date(item.created_at).toLocaleString('pt-BR')}</td>
                            <td>${this.formatStatus(item.old_status)}</td>
                            <td>${this.formatStatus(item.new_status)}</td>
                            <td>${item.changed_by_name}</td>
                            <td>${item.notes || '-'}</td>
                        </tr>
                    `;
                });
                
                this.historyModal.show();
            }
        } catch (error) {
            console.error('Error loading history:', error);
            this.showToast('error', 'Erro ao carregar histórico');
        }
    }

    formatStatus(status) {
        const statuses = {
            'pending': 'Pendente',
            'approved': 'Aprovado',
            'rejected': 'Rejeitado'
        };
        return statuses[status] || status;
    }

    showStatusModal(memberId, status) {
        document.getElementById('memberId').value = memberId;
        document.getElementById('newStatus').value = status;
        document.querySelector('.status-message').textContent = 
            status === 'approved' 
                ? 'Você está prestes a aprovar este membro. Ele será notificado por e-mail.'
                : 'Você está prestes a rejeitar este membro. Ele será notificado por e-mail.';
        
        this.statusModal.show();
    }

    async updateMemberStatus() {
        const memberId = document.getElementById('memberId').value;
        const status = document.getElementById('newStatus').value;
        const notes = document.getElementById('statusNotes').value;

        try {
            const response = await fetch(`/groups/members/${memberId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ status, notes })
            });

            const data = await response.json();
            if (data.success) {
                this.statusModal.hide();
                await this.loadMembers();
                await this.loadPendingMembers();
                
                const message = status === 'approved' ? 'Membro aprovado com sucesso!' : 'Membro rejeitado com sucesso!';
                this.showToast('success', message);
            } else {
                this.showToast('error', data.message || 'Erro ao atualizar status');
            }
        } catch (error) {
            console.error('Error updating status:', error);
            this.showToast('error', 'Erro ao atualizar status');
        }
    }

    showToast(type, message) {
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        document.body.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }
}
