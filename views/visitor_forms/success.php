<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário Enviado com Sucesso - <?= htmlspecialchars($form['title']) ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="<?= url('https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css') ?>" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="<?= url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css') ?>" rel="stylesheet">
    
    <style>
        :root {
            --theme-color: <?= $form['theme_color'] ?>;
        }
        
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .success-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            margin: 2rem auto;
            max-width: 600px;
            padding: 2rem;
            text-align: center;
        }
        
        .success-icon {
            color: var(--theme-color);
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        .success-title {
            color: var(--theme-color);
            margin-bottom: 1rem;
        }
        
        .success-message {
            color: #6c757d;
            margin-bottom: 2rem;
        }
        
        .btn-primary {
            background-color: var(--theme-color);
            border-color: var(--theme-color);
        }
        
        .btn-primary:hover {
            background-color: var(--theme-color);
            border-color: var(--theme-color);
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-container">
            <i class="fas fa-check-circle success-icon"></i>
            <h1 class="success-title">Formulário Enviado com Sucesso!</h1>
            <p class="success-message">
                Agradecemos por preencher o formulário. Em breve entraremos em contato.
            </p>
            
            <?php if ($form['footer_text']): ?>
                <div class="text-muted">
                    <?= nl2br(htmlspecialchars($form['footer_text'])) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="<?= url('https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
