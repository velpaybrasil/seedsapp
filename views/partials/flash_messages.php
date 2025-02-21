<?php
use App\Core\View;

// Mensagem flash única
$flashMessage = View::getFlash();
if ($flashMessage): ?>
    <div class="alert alert-<?= $flashMessage['type'] ?> alert-dismissible fade show" role="alert">
        <?= $flashMessage['message'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif;

// Múltiplas mensagens flash
$flashMessages = View::getFlashMessages();
if (!empty($flashMessages)): ?>
    <?php foreach ($flashMessages as $message): ?>
        <div class="alert alert-<?= $message['type'] ?> alert-dismissible fade show" role="alert">
            <?= $message['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
