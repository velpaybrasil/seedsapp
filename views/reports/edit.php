<?php
$title = 'Editar Relatório';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Editar Relatório</h1>
    
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-file-alt me-1"></i>
                Editar Relatório
            </div>
            <div>
                <a href="/reports" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Voltar
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="/reports/<?= $report['id'] ?>/update" method="POST">
                <?= csrf_field() ?>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nome do Relatório *</label>
                        <input type="text" class="form-control" id="name" name="name" required
                            value="<?= htmlspecialchars($report['name']) ?>">
                    </div>
                    
                    <div class="col-md-6">
                        <label for="type" class="form-label">Tipo de Relatório *</label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="">Selecione o tipo</option>
                            <option value="visitors" <?= $report['type'] === 'visitors' ? 'selected' : '' ?>>Visitantes</option>
                            <option value="groups" <?= $report['type'] === 'groups' ? 'selected' : '' ?>>Grupos</option>
                            <option value="volunteers" <?= $report['type'] === 'volunteers' ? 'selected' : '' ?>>Voluntários</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($report['description'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <h5>Campos do Relatório</h5>
                        <div id="fields-container">
                            <!-- Os campos serão carregados dinamicamente via JavaScript -->
                        </div>
                        <button type="button" class="btn btn-outline-primary mt-2" onclick="addField()">
                            <i class="fas fa-plus"></i> Adicionar Campo
                        </button>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <h5>Filtros</h5>
                        <div id="filters-container">
                            <!-- Os filtros serão carregados dinamicamente via JavaScript -->
                        </div>
                        <button type="button" class="btn btn-outline-primary mt-2" onclick="addFilter()">
                            <i class="fas fa-plus"></i> Adicionar Filtro
                        </button>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="/reports" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Definição dos campos disponíveis por tipo
const availableFields = {
    visitors: [
        { field: 'name', label: 'Nome' },
        { field: 'email', label: 'E-mail' },
        { field: 'phone', label: 'Telefone' },
        { field: 'address', label: 'Endereço' },
        { field: 'birth_date', label: 'Data de Nascimento' },
        { field: 'first_visit_date', label: 'Data da Primeira Visita' },
        { field: 'how_knew_church', label: 'Como Conheceu a Igreja' },
        { field: 'status', label: 'Status' },
        { field: 'created_at', label: 'Data de Cadastro' }
    ],
    groups: [
        { field: 'name', label: 'Nome' },
        { field: 'leader_id', label: 'Líder' },
        { field: 'co_leader_id', label: 'Co-líder' },
        { field: 'meeting_day', label: 'Dia da Reunião' },
        { field: 'meeting_time', label: 'Horário' },
        { field: 'address', label: 'Endereço' },
        { field: 'max_participants', label: 'Máximo de Participantes' },
        { field: 'created_at', label: 'Data de Criação' }
    ],
    volunteers: [
        { field: 'name', label: 'Nome' },
        { field: 'email', label: 'E-mail' },
        { field: 'phone', label: 'Telefone' },
        { field: 'ministry', label: 'Ministério' },
        { field: 'status', label: 'Status' },
        { field: 'created_at', label: 'Data de Cadastro' }
    ]
};

// Definição dos operadores disponíveis
const operators = [
    { value: '=', label: 'Igual a' },
    { value: '!=', label: 'Diferente de' },
    { value: '>', label: 'Maior que' },
    { value: '<', label: 'Menor que' },
    { value: '>=', label: 'Maior ou igual a' },
    { value: '<=', label: 'Menor ou igual a' },
    { value: 'LIKE', label: 'Contém' },
    { value: 'NOT LIKE', label: 'Não contém' },
    { value: 'IS NULL', label: 'É nulo' },
    { value: 'IS NOT NULL', label: 'Não é nulo' }
];

let fieldCount = 0;
let filterCount = 0;

function updateAvailableFields() {
    const type = document.getElementById('type').value;
    const fields = availableFields[type] || [];
    
    // Atualiza os campos existentes
    document.querySelectorAll('.field-select').forEach(select => {
        const currentValue = select.value;
        select.innerHTML = '<option value="">Selecione um campo</option>';
        fields.forEach(field => {
            const option = new Option(field.label, field.field);
            select.add(option);
        });
        select.value = currentValue;
    });
    
    // Atualiza os filtros existentes
    document.querySelectorAll('.filter-field-select').forEach(select => {
        const currentValue = select.value;
        select.innerHTML = '<option value="">Selecione um campo</option>';
        fields.forEach(field => {
            const option = new Option(field.label, field.field);
            select.add(option);
        });
        select.value = currentValue;
    });
}

function addField() {
    const container = document.getElementById('fields-container');
    const type = document.getElementById('type').value;
    const fields = availableFields[type] || [];
    
    const fieldDiv = document.createElement('div');
    fieldDiv.className = 'row mb-2 align-items-center';
    fieldDiv.innerHTML = `
        <div class="col-5">
            <select class="form-select field-select" name="fields[${fieldCount}][field]" required>
                <option value="">Selecione um campo</option>
                ${fields.map(field => `
                    <option value="${field.field}">${field.label}</option>
                `).join('')}
            </select>
        </div>
        <div class="col-5">
            <input type="text" class="form-control" name="fields[${fieldCount}][alias]" 
                placeholder="Nome de exibição (opcional)">
        </div>
        <div class="col-2">
            <button type="button" class="btn btn-danger" onclick="this.closest('.row').remove()">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    
    container.appendChild(fieldDiv);
    fieldCount++;
}

function addFilter() {
    const container = document.getElementById('filters-container');
    const type = document.getElementById('type').value;
    const fields = availableFields[type] || [];
    
    const filterDiv = document.createElement('div');
    filterDiv.className = 'row mb-2 align-items-center';
    filterDiv.innerHTML = `
        <div class="col-4">
            <select class="form-select filter-field-select" name="filters[${filterCount}][field]" required>
                <option value="">Selecione um campo</option>
                ${fields.map(field => `
                    <option value="${field.field}">${field.label}</option>
                `).join('')}
            </select>
        </div>
        <div class="col-3">
            <select class="form-select" name="filters[${filterCount}][operator]" required>
                <option value="">Selecione um operador</option>
                ${operators.map(op => `
                    <option value="${op.value}">${op.label}</option>
                `).join('')}
            </select>
        </div>
        <div class="col-3">
            <input type="text" class="form-control" name="filters[${filterCount}][default_value]" 
                placeholder="Valor padrão (opcional)">
        </div>
        <div class="col-2">
            <button type="button" class="btn btn-danger" onclick="this.closest('.row').remove()">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    
    container.appendChild(filterDiv);
    filterCount++;
}

// Atualiza os campos quando o tipo de relatório muda
document.getElementById('type').addEventListener('change', updateAvailableFields);

// Carrega os campos existentes
document.addEventListener('DOMContentLoaded', function() {
    const report = <?= json_encode($report) ?>;
    
    // Carrega os campos
    if (report.fields && report.fields.length > 0) {
        report.fields.forEach(field => {
            const container = document.getElementById('fields-container');
            const type = document.getElementById('type').value;
            const fields = availableFields[type] || [];
            
            const fieldDiv = document.createElement('div');
            fieldDiv.className = 'row mb-2 align-items-center';
            fieldDiv.innerHTML = `
                <div class="col-5">
                    <select class="form-select field-select" name="fields[${fieldCount}][field]" required>
                        <option value="">Selecione um campo</option>
                        ${fields.map(f => `
                            <option value="${f.field}" ${f.field === field.field ? 'selected' : ''}>
                                ${f.label}
                            </option>
                        `).join('')}
                    </select>
                </div>
                <div class="col-5">
                    <input type="text" class="form-control" name="fields[${fieldCount}][alias]" 
                        value="${field.alias || ''}"
                        placeholder="Nome de exibição (opcional)">
                </div>
                <div class="col-2">
                    <button type="button" class="btn btn-danger" onclick="this.closest('.row').remove()">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            
            container.appendChild(fieldDiv);
            fieldCount++;
        });
    }
    
    // Carrega os filtros
    if (report.filters && report.filters.length > 0) {
        report.filters.forEach(filter => {
            const container = document.getElementById('filters-container');
            const type = document.getElementById('type').value;
            const fields = availableFields[type] || [];
            
            const filterDiv = document.createElement('div');
            filterDiv.className = 'row mb-2 align-items-center';
            filterDiv.innerHTML = `
                <div class="col-4">
                    <select class="form-select filter-field-select" name="filters[${filterCount}][field]" required>
                        <option value="">Selecione um campo</option>
                        ${fields.map(f => `
                            <option value="${f.field}" ${f.field === filter.field ? 'selected' : ''}>
                                ${f.label}
                            </option>
                        `).join('')}
                    </select>
                </div>
                <div class="col-3">
                    <select class="form-select" name="filters[${filterCount}][operator]" required>
                        <option value="">Selecione um operador</option>
                        ${operators.map(op => `
                            <option value="${op.value}" ${op.value === filter.operator ? 'selected' : ''}>
                                ${op.label}
                            </option>
                        `).join('')}
                    </select>
                </div>
                <div class="col-3">
                    <input type="text" class="form-control" name="filters[${filterCount}][default_value]" 
                        value="${filter.default_value || ''}"
                        placeholder="Valor padrão (opcional)">
                </div>
                <div class="col-2">
                    <button type="button" class="btn btn-danger" onclick="this.closest('.row').remove()">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            
            container.appendChild(filterDiv);
            filterCount++;
        });
    }
});
</script>
