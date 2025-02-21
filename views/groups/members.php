<?php
use App\Core\View;

$title = 'Membros do Grupo';
?>

<?php View::section('content') ?>
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="fas fa-users"></i> Membros do Grupo: <?= $group['name'] ?></h2>
                <a href="<?= View::url('/groups') ?>" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Adicionar Membro</h5>
                    <form action="<?= View::url("/groups/{$group['id']}/members/add") ?>" method="POST" class="row g-3">
                        <?= csrf_field() ?>
                        
                        <div class="col-md-10">
                            <select class="form-select select2" name="user_id" required>
                                <option value="">Selecione um usuário...</option>
                                <?php foreach ($users as $user): ?>
                                    <?php if (!in_array($user['id'], array_column($members, 'user_id'))): ?>
                                        <option value="<?= $user['id'] ?>"><?= $user['name'] ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-plus"></i> Adicionar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Lista de Membros</h5>
                    
                    <?php if (empty($members)): ?>
                        <div class="alert alert-info">
                            Nenhum membro encontrado.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Data de Entrada</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($members as $member): ?>
                                        <tr>
                                            <td><?= $member['name'] ?></td>
                                            <td><?= $member['email'] ?></td>
                                            <td>
                                                <span class="badge bg-<?= $member['status'] === 'active' ? 'success' : 'danger' ?>">
                                                    <?= $member['status'] === 'active' ? 'Ativo' : 'Inativo' ?>
                                                </span>
                                            </td>
                                            <td><?= date('d/m/Y H:i', strtotime($member['joined_at'])) ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <form action="<?= View::url("/groups/{$group['id']}/members/status") ?>" method="POST" class="d-inline">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="user_id" value="<?= $member['user_id'] ?>">
                                                        <input type="hidden" name="status" value="<?= $member['status'] === 'active' ? 'inactive' : 'active' ?>">
                                                        <button type="submit" class="btn btn-sm btn-<?= $member['status'] === 'active' ? 'warning' : 'success' ?>" title="<?= $member['status'] === 'active' ? 'Desativar' : 'Ativar' ?>">
                                                            <i class="fas fa-<?= $member['status'] === 'active' ? 'ban' : 'check' ?>"></i>
                                                        </button>
                                                    </form>
                                                    
                                                    <form action="<?= View::url("/groups/{$group['id']}/members/remove") ?>" method="POST" class="d-inline">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="user_id" value="<?= $member['user_id'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Remover" onclick="return confirm('Tem certeza que deseja remover este membro?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php View::endSection() ?>

<?php View::section('scripts') ?>
<script>
$(document).ready(function() {
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });
});
</script>
<?php View::endSection() ?>

<?php require_once VIEWS_PATH . '/layouts/app.php' ?>
