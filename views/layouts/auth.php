<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Sistema de Gestão para Igrejas">
    <meta name="theme-color" content="#0D6EFD">
    <title><?= $pageTitle ?? 'SeedsApp' ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= asset('img/favicon.png') ?>">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet" crossorigin="anonymous">
    
    <style>
        :root {
            --primary-color: #0D6EFD;
            --primary-dark: #0957cc;
            --primary-light: #3d8bfd;
            --text-color: #ffffff;
            --text-muted: rgba(255, 255, 255, 0.7);
            --bg-gradient: linear-gradient(135deg, #0D6EFD, #0957cc);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            min-height: 100vh;
            background: var(--primary-color);
            background: var(--bg-gradient);
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 0% 0%, rgba(255,255,255,0.1) 0%, transparent 50%),
                        radial-gradient(circle at 100% 0%, rgba(255,255,255,0.1) 0%, transparent 50%),
                        radial-gradient(circle at 100% 100%, rgba(255,255,255,0.1) 0%, transparent 50%),
                        radial-gradient(circle at 0% 100%, rgba(255,255,255,0.1) 0%, transparent 50%);
            z-index: 0;
        }

        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            position: relative;
            z-index: 1;
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 1.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 420px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .auth-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }

        .auth-header {
            text-align: center;
            padding: 2.5rem 2rem 1.5rem;
        }

        .auth-logo {
            max-height: 80px;
            margin-bottom: 1.5rem;
            filter: brightness(0) invert(1);
            transition: transform 0.3s ease;
        }

        .auth-logo:hover {
            transform: scale(1.05);
        }

        .auth-form {
            padding: 2rem;
        }

        .form-label {
            color: var(--text-color);
            font-weight: 500;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            color: var(--text-color);
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
            box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.1);
            color: var(--text-color);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .input-group-text {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 0.75rem;
            color: var(--text-color);
        }

        .btn {
            border-radius: 0.75rem;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #ffffff;
            border-color: #ffffff;
            color: var(--primary-color);
        }

        .btn-primary:hover {
            background: rgba(255, 255, 255, 0.9);
            border-color: rgba(255, 255, 255, 0.9);
            color: var(--primary-color);
            transform: translateY(-2px);
        }

        .btn-outline-secondary {
            color: var(--text-color);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .btn-outline-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.3);
            color: var(--text-color);
        }

        .form-check-label {
            color: var(--text-color);
        }

        .form-check-input {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .form-check-input:checked {
            background-color: #ffffff;
            border-color: #ffffff;
        }

        .alert {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: var(--text-color);
        }

        .text-primary {
            color: var(--text-color) !important;
        }

        .text-muted {
            color: var(--text-muted) !important;
        }

        a {
            color: var(--text-color);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        a:hover {
            color: #ffffff;
            text-decoration: none;
        }

        @media (max-width: 576px) {
            .auth-card {
                margin: 1rem;
            }

            .auth-form {
                padding: 1.5rem;
            }
        }

        /* Suporte para navegadores mais antigos */
        @supports not (backdrop-filter: blur(10px)) {
            .auth-card {
                background: rgba(13, 110, 253, 0.95);
            }
        }

        /* Animações */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .auth-card {
            animation: fadeIn 0.6s ease-out;
        }

        .floating {
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
    </style>
</head>

<body>
    <div class="auth-container">
        <?= $content ?>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    
    <?php if (isset($scripts)): ?>
        <?php foreach ($scripts as $script): ?>
            <script src="<?= BASE_URL . $script ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
