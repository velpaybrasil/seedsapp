<?php
use App\Core\View;

View::extends('app');

// Seção de conteúdo
View::sectionStart('content');
?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">
            <?= $form ? 'Editar Formulário' : 'Novo Formulário' ?>
        </h1>
        <div>
            <?php if ($form): ?>
                <a href="<?= url('/f/' . $form['slug']) ?>" 
                   target="_blank"
                   class="btn btn-outline-primary me-2">
                    <i class="fas fa-eye"></i> Visualizar
                </a>
            <?php endif; ?>
            <a href="<?= url('/visitor-forms') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Configurações do Formulário -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Configurações do Formulário</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= $form ? url('/visitor-forms/' . $form['id']) : url('/visitor-forms') ?>">
                        <?php if ($form): ?>
                            <input type="hidden" name="_method" value="PUT">
                        <?php endif; ?>
                        <?= View::csrf() ?>

                        <div class="mb-3">
                            <label for="title" class="form-label">Título do Formulário</label>
                            <input type="text" 
                                   class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>" 
                                   id="title" 
                                   name="title"
                                   value="<?= htmlspecialchars($form['title'] ?? $old['title'] ?? '') ?>"
                                   required>
                            <?php if (isset($errors['title'])): ?>
                                <div class="invalid-feedback"><?= $errors['title'] ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>" 
                                      id="description" 
                                      name="description" 
                                      rows="3"><?= htmlspecialchars($form['description'] ?? $old['description'] ?? '') ?></textarea>
                            <?php if (isset($errors['description'])): ?>
                                <div class="invalid-feedback"><?= $errors['description'] ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="logo_url" class="form-label">URL do Logo</label>
                            <input type="url" 
                                   class="form-control <?= isset($errors['logo_url']) ? 'is-invalid' : '' ?>" 
                                   id="logo_url" 
                                   name="logo_url"
                                   value="<?= htmlspecialchars($form['logo_url'] ?? $old['logo_url'] ?? '') ?>">
                            <?php if (isset($errors['logo_url'])): ?>
                                <div class="invalid-feedback"><?= $errors['logo_url'] ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="theme_color" class="form-label">Cor do Tema</label>
                            <input type="color" 
                                   class="form-control form-control-color w-100 <?= isset($errors['theme_color']) ? 'is-invalid' : '' ?>" 
                                   id="theme_color" 
                                   name="theme_color"
                                   value="<?= htmlspecialchars($form['theme_color'] ?? $old['theme_color'] ?? '#0d6efd') ?>">
                            <?php if (isset($errors['theme_color'])): ?>
                                <div class="invalid-feedback"><?= $errors['theme_color'] ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="footer_text" class="form-label">Texto do Rodapé</label>
                            <textarea class="form-control <?= isset($errors['footer_text']) ? 'is-invalid' : '' ?>" 
                                      id="footer_text" 
                                      name="footer_text" 
                                      rows="3"><?= htmlspecialchars($form['footer_text'] ?? $old['footer_text'] ?? '') ?></textarea>
                            <?php if (isset($errors['footer_text'])): ?>
                                <div class="invalid-feedback"><?= $errors['footer_text'] ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="active" 
                                       name="active" 
                                       value="1"
                                       <?= ($form['active'] ?? $old['active'] ?? true) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="active">Formulário Ativo</label>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Campos do Formulário -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Campos do Formulário</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Campos Disponíveis -->
                        <div class="col-12 mb-4">
                            <div class="card border">
                                <div class="card-header bg-light">
                                    <h6 class="card-title mb-0">Campos Disponíveis</h6>
                                </div>
                                <div class="card-body p-2">
                                    <div id="availableFields" class="available-fields">
                                        <!-- Dados Pessoais -->
                                        <div class="field-category mb-2">
                                            <small class="text-muted d-block mb-1">Dados Pessoais</small>
                                            <div class="field-item" data-field='{"name":"name","label":"Nome","type":"text","required":true}'>
                                                <i class="fas fa-user me-2"></i>Nome <span class="text-danger">*</span>
                                            </div>
                                            <div class="field-item" data-field='{"name":"email","label":"E-mail","type":"email"}'>
                                                <i class="fas fa-envelope me-2"></i>E-mail
                                            </div>
                                            <div class="field-item" data-field='{"name":"phone","label":"Telefone","type":"phone"}'>
                                                <i class="fas fa-phone me-2"></i>Telefone
                                            </div>
                                            <div class="field-item" data-field='{"name":"birth_date","label":"Data de Nascimento","type":"date"}'>
                                                <i class="fas fa-calendar me-2"></i>Data de Nascimento
                                            </div>
                                        </div>

                                        <!-- Endereço -->
                                        <div class="field-category mb-2">
                                            <small class="text-muted d-block mb-1">Endereço</small>
                                            <div class="field-item" data-field='{"name":"address","label":"Endereço","type":"text"}'>
                                                <i class="fas fa-map-marker-alt me-2"></i>Endereço
                                            </div>
                                            <div class="field-item" data-field='{"name":"neighborhood","label":"Bairro","type":"text"}'>
                                                <i class="fas fa-map me-2"></i>Bairro
                                            </div>
                                            <div class="field-item" data-field='{"name":"city","label":"Cidade","type":"text"}'>
                                                <i class="fas fa-city me-2"></i>Cidade
                                            </div>
                                            <div class="field-item" data-field='{"name":"state","label":"Estado","type":"select","options":"AC\nAL\nAP\nAM\nBA\nCE\nDF\nES\nGO\nMA\nMT\nMS\nMG\nPA\nPB\nPR\nPE\nPI\nRJ\nRN\nRS\nRO\nRR\nSC\nSP\nSE\nTO"}'>
                                                <i class="fas fa-map-signs me-2"></i>Estado
                                            </div>
                                            <div class="field-item" data-field='{"name":"zip_code","label":"CEP","type":"text"}'>
                                                <i class="fas fa-mailbox me-2"></i>CEP
                                            </div>
                                        </div>

                                        <!-- Informações Pessoais -->
                                        <div class="field-category mb-2">
                                            <small class="text-muted d-block mb-1">Informações Pessoais</small>
                                            <div class="field-item" data-field='{"name":"marital_status","label":"Estado Civil","type":"select","options":"Solteiro(a)\nCasado(a)\nDivorciado(a)\nViúvo(a)"}'>
                                                <i class="fas fa-ring me-2"></i>Estado Civil
                                            </div>
                                            <div class="field-item" data-field='{"name":"has_children","label":"Tem Filhos?","type":"radio","options":"Sim\nNão"}'>
                                                <i class="fas fa-child me-2"></i>Tem Filhos?
                                            </div>
                                            <div class="field-item" data-field='{"name":"number_of_children","label":"Número de Filhos","type":"text"}'>
                                                <i class="fas fa-users me-2"></i>Número de Filhos
                                            </div>
                                            <div class="field-item" data-field='{"name":"profession","label":"Profissão","type":"text"}'>
                                                <i class="fas fa-briefcase me-2"></i>Profissão
                                            </div>
                                        </div>

                                        <!-- Informações Religiosas -->
                                        <div class="field-category mb-2">
                                            <small class="text-muted d-block mb-1">Informações Religiosas</small>
                                            <div class="field-item" data-field='{"name":"church_member","label":"É Membro de Igreja?","type":"radio","options":"Sim\nNão"}'>
                                                <i class="fas fa-church me-2"></i>É Membro de Igreja?
                                            </div>
                                            <div class="field-item" data-field='{"name":"previous_church","label":"Igreja Anterior","type":"text"}'>
                                                <i class="fas fa-place-of-worship me-2"></i>Igreja Anterior
                                            </div>
                                            <div class="field-item" data-field='{"name":"conversion_date","label":"Data de Conversão","type":"date"}'>
                                                <i class="fas fa-cross me-2"></i>Data de Conversão
                                            </div>
                                            <div class="field-item" data-field='{"name":"baptism_date","label":"Data de Batismo","type":"date"}'>
                                                <i class="fas fa-water me-2"></i>Data de Batismo
                                            </div>
                                        </div>

                                        <!-- Outros -->
                                        <div class="field-category">
                                            <small class="text-muted d-block mb-1">Outros</small>
                                            <div class="field-item" data-field='{"name":"prayer_requests","label":"Pedidos de Oração","type":"textarea"}'>
                                                <i class="fas fa-pray me-2"></i>Pedidos de Oração
                                            </div>
                                            <div class="field-item" data-field='{"name":"observations","label":"Observações","type":"textarea"}'>
                                                <i class="fas fa-comment me-2"></i>Observações
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Campos Selecionados -->
                        <div class="col-12">
                            <div class="card border">
                                <div class="card-header bg-light">
                                    <h6 class="card-title mb-0">Campos Selecionados</h6>
                                </div>
                                <div class="card-body p-2">
                                    <div id="selectedFields" class="selected-fields">
                                        <?php if (empty($fields)): ?>
                                            <div class="text-center text-muted py-4" id="emptySelectedFields">
                                                <i class="fas fa-arrows-alt fa-2x mb-2"></i>
                                                <p>Arraste os campos desejados para cá</p>
                                            </div>
                                        <?php else: ?>
                                            <?php foreach ($fields as $field): ?>
                                                <div class="field-item selected" data-field-id="<?= $field['id'] ?>">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <i class="fas fa-grip-vertical me-2 handle"></i>
                                                            <span><?= View::escape($field['field_label']) ?></span>
                                                            <?= $field['is_required'] ? '<span class="text-danger">*</span>' : '' ?>
                                                        </div>
                                                        <button type="button" class="btn btn-link text-danger p-0" onclick="removeField(this)">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
View::sectionEnd();

// Seção de estilos
View::sectionStart('styles');
?>
<style>
    .field-list {
        max-height: 400px;
        overflow-y: auto;
    }
    .field-item {
        cursor: pointer;
        transition: all 0.2s;
    }
    .field-item:hover {
        background-color: #f8f9fa;
    }

    .field-item {
        padding: 0.5rem;
        margin-bottom: 0.25rem;
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        cursor: move;
        user-select: none;
    }

    .field-item:hover {
        background: #f8f9fa;
    }

    .field-item.selected {
        background: #e9ecef;
    }

    .field-item.sortable-ghost {
        opacity: 0.5;
    }

    .field-category small {
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
    }

    .handle {
        cursor: move;
        color: #adb5bd;
    }

    .selected-fields {
        min-height: 100px;
        border: 2px dashed #dee2e6;
        border-radius: 0.25rem;
        padding: 1rem;
    }

    .selected-fields.sortable-drag-active {
        border-color: #0d6efd;
        background: #f8f9fa;
    }
</style>
<?php
View::sectionEnd();

// Seção de scripts
View::sectionStart('scripts');
?>
<script>
// Carregar Sortable.js
async function loadSortableJS() {
    return new Promise((resolve, reject) => {
        if (window.Sortable) {
            resolve(window.Sortable);
            return;
        }

        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js';
        script.onload = () => resolve(window.Sortable);
        script.onerror = () => reject(new Error('Erro ao carregar Sortable.js'));
        document.body.appendChild(script);
    });
}

// Inicializar drag and drop
async function initializeDragAndDrop() {
    try {
        const Sortable = await loadSortableJS();
        
        // Inicializar campos disponíveis
        const availableFields = document.getElementById('availableFields');
        if (availableFields) {
            new Sortable(availableFields, {
                group: {
                    name: 'fields',
                    pull: 'clone',
                    put: false
                },
                sort: false,
                animation: 150,
                onClone: function(evt) {
                    evt.clone.classList.remove('selected');
                }
            });
        }

        // Inicializar campos selecionados
        const selectedFields = document.getElementById('selectedFields');
        if (selectedFields) {
            new Sortable(selectedFields, {
                group: 'fields',
                animation: 150,
                handle: '.handle',
                dragClass: 'sortable-drag',
                ghostClass: 'sortable-ghost',
                onStart: function() {
                    selectedFields.classList.add('sortable-drag-active');
                },
                onEnd: function() {
                    selectedFields.classList.remove('sortable-drag-active');
                    updateFormPreview();
                },
                onAdd: function(evt) {
                    const item = evt.item;
                    const fieldData = JSON.parse(item.dataset.field || '{}');
                    addField(fieldData, item);
                },
                onUpdate: function() {
                    updateFieldOrder();
                    updateFormPreview();
                }
            });
        }

        // Inicializar preview
        updateFormPreview();
    } catch (error) {
        console.error('Erro ao inicializar drag and drop:', error);
    }
}

// Busca de campos
document.getElementById('searchFields').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const fields = document.querySelectorAll('#availableFields .field-item');
    
    fields.forEach(field => {
        const text = field.textContent.toLowerCase();
        const category = field.closest('.field-category');
        
        if (text.includes(searchTerm)) {
            field.style.display = '';
            if (category) category.style.display = '';
        } else {
            field.style.display = 'none';
            // Ocultar categoria se todos os campos estiverem ocultos
            if (category) {
                const visibleFields = category.querySelectorAll('.field-item[style="display: none;"]');
                category.style.display = visibleFields.length === category.querySelectorAll('.field-item').length ? 'none' : '';
            }
        }
    });
});

// Modal de configurações do campo
let currentFieldItem = null;
const fieldSettingsModal = new bootstrap.Modal(document.getElementById('fieldSettingsModal'));

window.editField = function(button) {
    currentFieldItem = button.closest('.field-item');
    const fieldData = JSON.parse(currentFieldItem.dataset.field || '{}');
    
    document.getElementById('fieldId').value = currentFieldItem.dataset.fieldId;
    document.getElementById('fieldLabel').value = fieldData.label;
    document.getElementById('fieldPlaceholder').value = fieldData.placeholder || '';
    document.getElementById('fieldHelpText').value = fieldData.helpText || '';
    document.getElementById('fieldRequired').checked = fieldData.required || false;
    
    const optionsContainer = document.getElementById('fieldOptionsContainer');
    if (['select', 'radio', 'checkbox'].includes(fieldData.type)) {
        optionsContainer.style.display = 'block';
        document.getElementById('fieldOptions').value = fieldData.options || '';
    } else {
        optionsContainer.style.display = 'none';
    }
    
    fieldSettingsModal.show();
};

window.saveFieldSettings = async function() {
    if (!currentFieldItem) return;
    
    const fieldData = JSON.parse(currentFieldItem.dataset.field || '{}');
    fieldData.label = document.getElementById('fieldLabel').value;
    fieldData.placeholder = document.getElementById('fieldPlaceholder').value;
    fieldData.helpText = document.getElementById('fieldHelpText').value;
    fieldData.required = document.getElementById('fieldRequired').checked;
    
    if (['select', 'radio', 'checkbox'].includes(fieldData.type)) {
        fieldData.options = document.getElementById('fieldOptions').value;
    }
    
    try {
        const response = await fetch('<?= url('/visitor-forms/' . $form['id'] . '/fields/') ?>' + currentFieldItem.dataset.fieldId, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'csrf_token': '<?= View::csrf_token() ?>'
            },
            body: JSON.stringify(fieldData)
        });

        const result = await response.json();
        if (result.success) {
            currentFieldItem.dataset.field = JSON.stringify(fieldData);
            updateFieldDisplay(currentFieldItem, fieldData);
            updateFormPreview();
            fieldSettingsModal.hide();
        } else {
            alert(result.message || 'Erro ao atualizar campo');
        }
    } catch (error) {
        alert('Erro ao atualizar campo: ' + error.message);
    }
};

// Atualizar display do campo
function updateFieldDisplay(item, fieldData) {
    const label = item.querySelector('span');
    const badge = item.querySelector('.badge');
    
    if (label) label.textContent = fieldData.label;
    if (badge) {
        badge.style.display = fieldData.required ? '' : 'none';
    } else if (fieldData.required) {
        const div = item.querySelector('div');
        div.insertAdjacentHTML('beforeend', '<span class="badge bg-danger ms-2">Obrigatório</span>');
    }
}

// Atualizar preview do formulário
function updateFormPreview() {
    const preview = document.getElementById('formPreview');
    const fields = Array.from(document.querySelectorAll('#selectedFields .field-item'));
    
    if (fields.length === 0) {
        preview.innerHTML = `
            <div class="text-center text-muted py-4" id="emptyFormPreview">
                <i class="fas fa-file-alt fa-3x mb-3"></i>
                <p>Arraste campos para começar a construir seu formulário</p>
            </div>
        `;
        return;
    }
    
    let html = '<form class="row g-3">';
    
    fields.forEach(field => {
        const fieldData = JSON.parse(field.dataset.field || '{}');
        html += generateFieldHtml(fieldData);
    });
    
    html += '</form>';
    preview.innerHTML = html;
}

// Gerar HTML do campo
function generateFieldHtml(fieldData) {
    const required = fieldData.required ? 'required' : '';
    const helpText = fieldData.helpText ? `<div class="form-text">${fieldData.helpText}</div>` : '';
    
    let input = '';
    switch (fieldData.type) {
        case 'textarea':
            input = `<textarea class="form-control" id="${fieldData.name}" name="${fieldData.name}" 
                placeholder="${fieldData.placeholder || ''}" ${required}></textarea>`;
            break;
            
        case 'select':
            const options = (fieldData.options || '').split('\n')
                .map(opt => `<option value="${opt.trim()}">${opt.trim()}</option>`)
                .join('');
            input = `<select class="form-select" id="${fieldData.name}" name="${fieldData.name}" ${required}>
                <option value="">Selecione...</option>${options}</select>`;
            break;
            
        case 'radio':
        case 'checkbox':
            input = (fieldData.options || '').split('\n')
                .map((opt, i) => `
                    <div class="form-check">
                        <input class="form-check-input" type="${fieldData.type}" name="${fieldData.name}" 
                            id="${fieldData.name}_${i}" value="${opt.trim()}" ${required}>
                        <label class="form-check-label" for="${fieldData.name}_${i}">${opt.trim()}</label>
                    </div>
                `).join('');
            break;
            
        default:
            input = `<input type="${fieldData.type}" class="form-control" id="${fieldData.name}" 
                name="${fieldData.name}" placeholder="${fieldData.placeholder || ''}" ${required}>`;
    }
    
    return `
        <div class="col-md-6">
            <label for="${fieldData.name}" class="form-label">
                ${fieldData.label}
                ${fieldData.required ? '<span class="text-danger">*</span>' : ''}
            </label>
            ${input}
            ${helpText}
        </div>
    `;
}

// Salvar formulário
window.saveForm = async function() {
    const formData = new FormData(document.getElementById('formSettings'));
    
    try {
        const response = await fetch('<?= url('/visitor-forms/' . $form['id']) ?>', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'csrf_token': '<?= View::csrf_token() ?>'
            },
            body: JSON.stringify(Object.fromEntries(formData))
        });

        const result = await response.json();
        if (result.success) {
            alert('Formulário salvo com sucesso!');
        } else {
            alert(result.message || 'Erro ao salvar formulário');
        }
    } catch (error) {
        alert('Erro ao salvar formulário: ' + error.message);
    }
};

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    // Aguardar um momento para garantir que todos os scripts foram carregados
    setTimeout(initializeDragAndDrop, 500);
});
</script>
<?php
View::sectionEnd();
?>
