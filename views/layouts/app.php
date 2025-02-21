<?php
use App\Core\View;

$flashMessage = View::getFlash();
$flashMessages = View::getFlashMessages();
unset($_SESSION['flash'], $_SESSION['flash_messages']);
?>
<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="<?= $user['theme'] ?? 'light' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= csrf_meta() ?>
    <meta name="theme-color" content="#0d6efd">
    <title><?= defined('CHURCH_NAME') ? CHURCH_NAME : 'SeedsApp' ?> - <?= $title ?? 'Gestão de Igreja' ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= View::asset('icone.png') ?>">
    <link rel="apple-touch-icon" href="<?= View::asset('icone.png') ?>">
    <meta name="msapplication-TileImage" content="<?= View::asset('icone.png') ?>">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="<?= View::asset('css/styles.css') ?>" rel="stylesheet">
    
    <?php View::renderSection('styles') ?>
</head>
<body>
    <?php if ($flashMessage): ?>
        <div class="alert alert-<?= $flashMessage['type'] ?> alert-dismissible fade show m-3" role="alert">
            <?= $flashMessage['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($flashMessages)): ?>
        <?php foreach ($flashMessages as $message): ?>
            <div class="alert alert-<?= $message['type'] ?> alert-dismissible fade show m-3" role="alert">
                <?= $message['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php require_once VIEWS_PATH . '/partials/navbar.php'; ?>

    <div class="main-content content-spacing">
        <div class="container-fluid">
            <?php View::renderSection('content') ?>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= View::asset('js/theme.js') ?>"></script>
    <script src="<?= asset('js/csrf.js') ?>"></script>
    <script src="<?= asset('js/app.js') ?>"></script>
    
    <!-- Custom Scripts -->
    <script>
        // Initialize Select2
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5'
            });

            // Initialize Flatpickr
            flatpickr(".datepicker", {
                dateFormat: "Y-m-d",
                locale: "pt"
            });

            flatpickr(".timepicker", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true
            });

            // Flash message auto-hide
            setTimeout(function() {
                $('.alert-dismissible').fadeOut('slow');
            }, 5000);
        });
    </script>
    
    <?php View::renderSection('scripts') ?>
</body>
</html>
