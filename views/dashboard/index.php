<?php
use App\Core\View;

View::extends('app');
View::section('content');
?>

<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 text-gray-800">Dashboard</h1>
        <div class="d-flex gap-3">
            <a href="<?= url('/groups/create') ?>" class="btn btn-primary btn-sm shadow-sm animate-btn">
                <i class="fa-solid fa-plus"></i> Novo Grupo
            </a>
            <a href="<?= url('/visitors/create') ?>" class="btn btn-success btn-sm shadow-sm animate-btn">
                <i class="fa-solid fa-user-plus"></i> Novo Visitante
            </a>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row g-4">
        <?php
        $cards = [
            [
                'title' => 'Total de Usuários',
                'value' => $stats['users']['total'] ?? 0,
                'icon' => 'fa-users',
                'gradient' => 'background-color: #4158D0;background-image: linear-gradient(43deg, #4158D0 0%, #C850C0 46%, #FFCC70 100%);',
                'subtitle' => 'Usuários cadastrados'
            ],
            [
                'title' => 'Usuários Ativos',
                'value' => $stats['users']['active'] ?? 0,
                'icon' => 'fa-user-check',
                'gradient' => 'background-color: #0093E9;background-image: linear-gradient(160deg, #0093E9 0%, #80D0C7 100%);',
                'subtitle' => 'Ativos no sistema'
            ],
            [
                'title' => 'Grupos Ativos',
                'value' => $stats['groups']['active'] ?? 0,
                'icon' => 'fa-user-group',
                'gradient' => 'background-color: #8EC5FC;background-image: linear-gradient(62deg, #8EC5FC 0%, #E0C3FC 100%);',
                'subtitle' => 'Total: ' . ($stats['groups']['total'] ?? 0)
            ],
            [
                'title' => 'Total de Visitantes',
                'value' => $stats['visitors']['total'] ?? 0,
                'icon' => 'fa-user-plus',
                'gradient' => 'background-color: #FBAB7E;background-image: linear-gradient(62deg, #FBAB7E 0%, #F7CE68 100%);',
                'subtitle' => 'Visitantes registrados'
            ]
        ];

        foreach ($cards as $card): ?>
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs font-weight-bold text-uppercase mb-1"><?= $card['title'] ?></div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $card['value'] ?></div>
                                <div class="text-xs text-muted mt-1"><?= $card['subtitle'] ?></div>
                            </div>
                            <div class="rounded-circle p-3" style="<?= $card['gradient'] ?>">
                                <i class="fa-solid <?= $card['icon'] ?> fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Tabelas -->
    <div class="row g-4 mt-2">
        <?php
        $tables = [
            [
                'title' => 'Últimos Grupos',
                'items' => $latestGroups,
                'url' => '/groups',
                'empty' => 'Nenhum grupo cadastrado',
                'icon' => 'fa-user-group',
                'headers' => ['Nome', 'Líder', 'Status', 'Criado em'],
                'rows' => function($item) {
                    return [
                        $item['name'],
                        $item['leader_name'] ?? 'Não definido',
                        '<span class="badge ' . ($item['status'] === 'Ativo' ? 'bg-success' : 'bg-secondary') . '">' . $item['status'] . '</span>',
                        $item['created_at']
                    ];
                }
            ],
            [
                'title' => 'Últimos Visitantes',
                'items' => $latestVisitors,
                'url' => '/visitors',
                'empty' => 'Nenhum visitante cadastrado',
                'icon' => 'fa-user-plus',
                'headers' => ['Nome', 'Telefone', 'Grupo', 'Visitou em'],
                'rows' => function($item) {
                    return [
                        $item['name'],
                        $item['phone'],
                        $item['group_name'] ?? 'Não definido',
                        date('d/m/Y', strtotime($item['created_at']))
                    ];
                }
            ]
        ];

        foreach ($tables as $table): ?>
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fa-solid <?= $table['icon'] ?> me-2"></i>
                            <?= $table['title'] ?>
                        </h6>
                        <a href="<?= url($table['url']) ?>" class="btn btn-sm btn-primary animate-btn">
                            Ver todos
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($table['items'])): ?>
                            <p class="text-center text-muted my-3"><?= $table['empty'] ?></p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <?php foreach ($table['headers'] as $header): ?>
                                                <th><?= $header ?></th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($table['items'] as $item): 
                                            $rows = $table['rows']($item);
                                        ?>
                                            <tr>
                                                <?php foreach ($rows as $cell): ?>
                                                    <td><?= $cell ?></td>
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
        <?php endforeach; ?>
    </div>
</div>

<style>
.animate-btn {
    transition: all 0.2s;
}
.animate-btn:hover {
    transform: scale(1.05);
}
.card {
    transition: all 0.2s;
}
.card:hover {
    transform: translateY(-5px);
}
</style>

<?php View::endSection(); ?>