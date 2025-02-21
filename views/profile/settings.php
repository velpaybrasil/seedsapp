<?php
use App\Core\View;

View::extends('app');

View::section('content'); ?>
<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $title ?></h1>
    
    <?php View::renderPartial('partials/flash_messages') ?>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-cog me-1"></i>
            Configurações do Sistema
        </div>
        <div class="card-body">
            <form method="POST" action="<?= View::url('settings') ?>">
                <?= View::csrf() ?>
                <input type="hidden" name="_method" value="PUT">

                <div class="mb-3">
                    <label class="form-label">Tema</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="theme" id="theme_light" value="light" 
                                   <?= ($user['theme'] ?? 'light') === 'light' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="theme_light">
                                <i class="fas fa-sun me-1"></i> Claro
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="theme" id="theme_dark" value="dark"
                                   <?= ($user['theme'] ?? 'light') === 'dark' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="theme_dark">
                                <i class="fas fa-moon me-1"></i> Escuro
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Notificações</label>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="notifications_enabled" name="notifications_enabled" value="1"
                               <?= ($user['notifications_enabled'] ?? 1) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="notifications_enabled">
                            <i class="fas fa-bell me-1"></i> Ativar notificações no sistema
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" value="1"
                               <?= ($user['email_notifications'] ?? 1) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="email_notifications">
                            <i class="fas fa-envelope me-1"></i> Receber notificações por e-mail
                        </label>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Salvar Configurações</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php View::endSection(); ?>
