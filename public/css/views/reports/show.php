<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?= htmlspecialchars($report['name']) ?></h1>
    
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-file-alt me-1"></i>
                <?php
                $types = [
                    'visitors' => 'Relatório de Visitantes',
                    'groups' => 'Relatório de Grupos',
                    'volunteers' => 'Relatório de Voluntários'
                ];
                echo $types[$report['type']] ?? 'Relatório';
                ?>
            </div>
            <div class="btn-group">
                <a href="/gcmanager/reports" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Voltar
                </a>
                <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-download me-1"></i> Exportar
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="/gcmanager/reports/<?= $report['id'] ?>/export?format=csv">
                            <i class="fas fa-file-csv me-1"></i> CSV
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="/gcmanager/reports/<?= $report['id'] ?>/export?format=excel">
                            <i class="fas fa-file-excel me-1"></i> Excel
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="/gcmanager/reports/<?= $report['id'] ?>/export?format=pdf">
                            <i class="fas fa-file-pdf me-1"></i> PDF
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="card-body">
            <?php if (!empty($report['description'])): ?>
                <div class="alert alert-info">
                    <?= nl2br(htmlspecialchars($report['description'])) ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($report['filters'])): ?>
                <form method="GET" class="mb-4">
                    <div class="row">
                        <?php foreach ($report['filters'] as $filter): ?>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">
                                    <?= htmlspecialchars($filter['alias'] ?? $filter['field']) ?>
                                </label>
                                
                                <?php if (in_array($filter['operator'], ['IS NULL', 'IS NOT NULL'])): ?>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" 
                                            id="filter_<?= $filter['field'] ?>"
                                            name="<?= $filter['field'] ?>"
                                            value="1"
                                            <?= isset($_GET[$filter['field']]) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="filter_<?= $filter['field'] ?>">
                                            <?= $filter['operator'] === 'IS NULL' ? 'É nulo' : 'Não é nulo' ?>
                                        </label>
                                    </div>
                                <?php else: ?>
                                    <input type="text" class="form-control"
                                        name="<?= $filter['field'] ?>"
                                        value="<?= htmlspecialchars($_GET[$filter['field']] ?? $filter['default_value'] ?? '') ?>"
                                        placeholder="Digite o valor...">
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                        
                        <div class="col-md-3 mb-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-1"></i> Filtrar
                            </button>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
            
            <?php if (empty($data)): ?>
                <div class="alert alert-warning">
                    Nenhum resultado encontrado para os filtros selecionados.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <?php foreach ($report['fields'] as $field): ?>
                                    <th><?= htmlspecialchars($field['alias'] ?? $field['field']) ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data as $row): ?>
                                <tr>
                                    <?php foreach ($report['fields'] as $field): ?>
                                        <td>
                                            <?php
                                            $value = $row[$field['field']];
                                            if (strpos($field['field'], '_date') !== false || strpos($field['field'], '_at') !== false) {
                                                echo $value ? date('d/m/Y H:i', strtotime($value)) : '-';
                                            } else {
                                                echo htmlspecialchars($value ?? '-');
                                            }
                                            ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
