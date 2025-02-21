<?php
/**
 * @var array $request
 * @var array $statusOptions
 */
?>
<div class="card mb-3 prayer-request-card" data-request-id="<?= $request['id'] ?>">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <h6 class="card-subtitle text-muted"><?= htmlspecialchars($request['visitor_name']) ?></h6>
            <small class="text-muted">
                <?= date('d/m/Y H:i', strtotime($request['created_at'])) ?>
            </small>
        </div>
        <p class="card-text"><?= nl2br(htmlspecialchars($request['request'])) ?></p>
        <div class="text-muted small">
            <i class="fas fa-grip-vertical me-2"></i>Arraste para mover
        </div>
    </div>
</div>
