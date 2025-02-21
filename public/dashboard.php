<?php
require_once '/home/u315624178/domains/alfadev.online/public_html/gcmanager/config/config.php';

// Verifica se está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . APP_URL . '/login.php');
    exit;
}

// Conecta ao banco
try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", 
                  DB_USER, 
                  DB_PASS, 
                  [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    
    // Busca estatísticas básicas
    $stats = [
        'visitors' => $db->query("SELECT COUNT(*) FROM visitors")->fetchColumn(),
        'total_groups' => $db->query("SELECT COUNT(*) FROM growth_groups")->fetchColumn(),
        'active_groups' => $db->query("SELECT COUNT(*) FROM growth_groups WHERE status = 'active'")->fetchColumn()
    ];

    // Busca os últimos 5 grupos ativos criados
    $latest_groups = $db->query("
        SELECT name, meeting_day, meeting_time, neighborhood, created_at 
        FROM growth_groups 
        WHERE status = 'active'
        ORDER BY created_at DESC 
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Busca os últimos 5 visitantes
    $latest_visitors = $db->query("
        SELECT v.name, v.phone, v.created_at, g.name as group_name
        FROM visitors v
        LEFT JOIN growth_groups g ON v.group_id = g.id AND g.status = 'active'
        ORDER BY v.created_at DESC 
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    die('Erro ao conectar ao banco de dados');
}

// Função para formatar o dia da semana
function formatDayOfWeek($day) {
    $days = [
        'sunday' => 'Domingo',
        'monday' => 'Segunda',
        'tuesday' => 'Terça',
        'wednesday' => 'Quarta',
        'thursday' => 'Quinta',
        'friday' => 'Sexta',
        'saturday' => 'Sábado'
    ];
    return $days[$day] ?? $day;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .stat-card {
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            font-size: 2rem;
            opacity: 0.7;
        }
        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container py-4">
        <h1 class="mb-4">Dashboard</h1>
        
        <!-- Cards de Estatísticas -->
        <div class="row g-4 mb-4">
            <!-- Grupos -->
            <div class="col-md-6">
                <div class="card stat-card h-100 border-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="card-subtitle mb-2 text-muted">Grupos de Crescimento</h6>
                                <h2 class="card-title mb-0"><?= $stats['active_groups'] ?></h2>
                                <p class="card-text">
                                    <small class="text-muted">
                                        Grupos Ativos (Total: <?= $stats['total_groups'] ?>)
                                    </small>
                                </p>
                            </div>
                            <i class="bi bi-people-fill stat-icon text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Visitantes -->
            <div class="col-md-6">
                <div class="card stat-card h-100 border-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="card-subtitle mb-2 text-muted">Visitantes</h6>
                                <h2 class="card-title mb-0"><?= $stats['visitors'] ?></h2>
                            </div>
                            <i class="bi bi-person-plus-fill stat-icon text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Últimos Registros -->
        <div class="row g-4">
            <!-- Últimos Grupos -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-people-fill me-2"></i>
                            Últimos Grupos Ativos
                        </h5>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Dia/Horário</th>
                                    <th>Bairro</th>
                                    <th>Criado em</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($latest_groups as $group): ?>
                                <tr>
                                    <td><?= htmlspecialchars($group['name']) ?></td>
                                    <td>
                                        <?= formatDayOfWeek($group['meeting_day']) ?>
                                        <?= date('H:i', strtotime($group['meeting_time'])) ?>
                                    </td>
                                    <td><?= htmlspecialchars($group['neighborhood']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($group['created_at'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Últimos Visitantes -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-person-plus-fill me-2"></i>
                            Últimos Visitantes
                        </h5>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Telefone</th>
                                    <th>Grupo</th>
                                    <th>Visitou em</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($latest_visitors as $visitor): ?>
                                <tr>
                                    <td><?= htmlspecialchars($visitor['name']) ?></td>
                                    <td><?= htmlspecialchars($visitor['phone']) ?></td>
                                    <td><?= $visitor['group_name'] ? htmlspecialchars($visitor['group_name']) : '<span class="text-muted">Não definido</span>' ?></td>
                                    <td><?= date('d/m/Y', strtotime($visitor['created_at'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
