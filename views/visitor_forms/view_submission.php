<?php
$title = 'Detalhes da Submissão';
?>

<div class="container-fluid">
    <!-- Page Title -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0"><?= htmlspecialchars($form['title']) ?></h1>
            <p class="text-muted mb-0">Detalhes da Submissão</p>
        </div>
        <div>
            <a href="<?= url('/visitor-forms/' . $form['id'] . '/submissions') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Voltar para Submissões
            </a>
        </div>
    </div>

    <!-- Detalhes da Submissão -->
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Informações da Submissão</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-sm-4">Data da Submissão</dt>
                        <dd class="col-sm-8"><?= (new DateTime($submission['created_at']))->format('d/m/Y H:i:s') ?></dd>

                        <dt class="col-sm-4">IP</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($submission['ip_address']) ?></dd>

                        <dt class="col-sm-4">Navegador</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($submission['user_agent']) ?></dd>
                    </dl>
                </div>
                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-sm-4">Visitante</dt>
                        <dd class="col-sm-8">
                            <?php if ($submission['visitor_id']): ?>
                                <a href="<?= url('/visitors/' . $submission['visitor_id']) ?>" class="text-decoration-none">
                                    <?= htmlspecialchars($submission['visitor_name']) ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">Não vinculado</span>
                                <?php 
                                $submissionData = json_decode($submission['data'], true);
                                if (!empty($submissionData['email']) || !empty($submissionData['phone'])): 
                                ?>
                                    <button type="button"
                                            class="btn btn-sm btn-outline-success ms-2"
                                            onclick="createVisitor(<?= $submission['id'] ?>, <?= json_encode($submissionData) ?>)"
                                            title="Criar Visitante">
                                        <i class="fas fa-user-plus me-1"></i>Criar Visitante
                                    </button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Dados do Formulário -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="card-title mb-0">Dados do Formulário</h5>
        </div>
        <div class="card-body">
            <?php
            $submissionData = json_decode($submission['data'], true);
            ?>
            <dl class="row">
                <?php foreach ($fields as $field): ?>
                    <dt class="col-sm-3"><?= htmlspecialchars($field['field_label']) ?></dt>
                    <dd class="col-sm-9">
                        <?php
                        $value = $submissionData[$field['field_name']] ?? '';
                        if (is_array($value)) {
                            echo implode(', ', array_map('htmlspecialchars', $value));
                        } else {
                            echo nl2br(htmlspecialchars($value));
                        }
                        ?>
                    </dd>
                <?php endforeach; ?>
            </dl>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir esta submissão? Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="<?= url('/visitor-forms/' . $form['id'] . '/submissions/' . $submission['id'] . '/delete') ?>" method="post" class="d-inline">
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function createVisitor(submissionId, submissionData) {
    // Implementar a criação de visitante
    alert('Funcionalidade em desenvolvimento');
}
</script>
