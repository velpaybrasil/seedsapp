<div class="container-fluid">
    <!-- Page Title -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Preferências</h1>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-lg-8">
            <!-- Preferences Form -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Configurações Gerais</h6>
                </div>
                <div class="card-body">
                    <form action="/settings/update-preferences" method="POST">
                        <?php
                        $preferences = json_decode($profile['preferences'] ?? '{}', true) ?: $defaultPreferences;
                        ?>
                        
                        <!-- Interface -->
                        <h5 class="heading-small text-muted mb-4">Interface</h5>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="theme" class="form-label">Tema</label>
                                    <select class="form-select" id="theme" name="theme">
                                        <option value="light" <?= $preferences['theme'] === 'light' ? 'selected' : '' ?>>
                                            Claro
                                        </option>
                                        <option value="dark" <?= $preferences['theme'] === 'dark' ? 'selected' : '' ?>>
                                            Escuro
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="language" class="form-label">Idioma</label>
                                    <select class="form-select" id="language" name="language">
                                        <option value="pt_BR" <?= $preferences['language'] === 'pt_BR' ? 'selected' : '' ?>>
                                            Português (Brasil)
                                        </option>
                                        <option value="en" <?= $preferences['language'] === 'en' ? 'selected' : '' ?>>
                                            English
                                        </option>
                                        <option value="es" <?= $preferences['language'] === 'es' ? 'selected' : '' ?>>
                                            Español
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="sidebar_collapsed" 
                                       name="sidebar_collapsed" 
                                       <?= $preferences['sidebar_collapsed'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="sidebar_collapsed">
                                    Menu lateral recolhido por padrão
                                </label>
                            </div>
                        </div>

                        <!-- Date and Time -->
                        <h5 class="heading-small text-muted mb-4">Data e Hora</h5>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="timezone" class="form-label">Fuso Horário</label>
                                    <select class="form-select" id="timezone" name="timezone">
                                        <?php
                                        $timezones = [
                                            'America/Sao_Paulo' => '(GMT-3) São Paulo',
                                            'America/Manaus' => '(GMT-4) Manaus',
                                            'America/Belem' => '(GMT-3) Belém',
                                            'America/Fortaleza' => '(GMT-3) Fortaleza',
                                            'America/Recife' => '(GMT-3) Recife',
                                            'America/Noronha' => '(GMT-2) Fernando de Noronha'
                                        ];
                                        
                                        foreach ($timezones as $value => $label):
                                        ?>
                                        <option value="<?= $value ?>" <?= $preferences['timezone'] === $value ? 'selected' : '' ?>>
                                            <?= $label ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="date_format" class="form-label">Formato de Data</label>
                                    <select class="form-select" id="date_format" name="date_format">
                                        <?php
                                        $dateFormats = [
                                            'd/m/Y' => date('d/m/Y'),
                                            'Y-m-d' => date('Y-m-d'),
                                            'd.m.Y' => date('d.m.Y'),
                                            'd/m/y' => date('d/m/y')
                                        ];
                                        
                                        foreach ($dateFormats as $format => $example):
                                        ?>
                                        <option value="<?= $format ?>" <?= $preferences['date_format'] === $format ? 'selected' : '' ?>>
                                            <?= $example ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="time_format" class="form-label">Formato de Hora</label>
                            <select class="form-select" id="time_format" name="time_format">
                                <?php
                                $timeFormats = [
                                    'H:i' => date('H:i'),
                                    'h:i A' => date('h:i A'),
                                    'H:i:s' => date('H:i:s'),
                                    'h:i:s A' => date('h:i:s A')
                                ];
                                
                                foreach ($timeFormats as $format => $example):
                                ?>
                                <option value="<?= $format ?>" <?= $preferences['time_format'] === $format ? 'selected' : '' ?>>
                                    <?= $example ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Notifications -->
                        <h5 class="heading-small text-muted mb-4">Notificações</h5>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="notifications_email" 
                                       name="notifications[email]" 
                                       <?= ($preferences['notifications']['email'] ?? true) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="notifications_email">
                                    Receber notificações por e-mail
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="notifications_browser" 
                                       name="notifications[browser]" 
                                       <?= ($preferences['notifications']['browser'] ?? true) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="notifications_browser">
                                    Receber notificações no navegador
                                </label>
                            </div>
                        </div>

                        <!-- Financial -->
                        <h5 class="heading-small text-muted mb-4">Financeiro</h5>
                        <div class="mb-3">
                            <label for="currency_format" class="form-label">Formato de Moeda</label>
                            <select class="form-select" id="currency_format" name="currency_format">
                                <?php
                                $amount = 1234.56;
                                $currencyFormats = [
                                    ['format' => 'R$ #.###,##', 'example' => number_format($amount, 2, ',', '.')],
                                    ['format' => '$ #,###.##', 'example' => number_format($amount, 2, '.', ',')]
                                ];
                                
                                foreach ($currencyFormats as $item):
                                ?>
                                <option value="<?= $item['format'] ?>" 
                                        <?= ($preferences['currency_format'] ?? 'R$ #.###,##') === $item['format'] ? 'selected' : '' ?>>
                                    <?= $item['example'] ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Export -->
                        <h5 class="heading-small text-muted mb-4">Exportação</h5>
                        <div class="mb-3">
                            <label for="export_format" class="form-label">Formato Padrão de Exportação</label>
                            <select class="form-select" id="export_format" name="export_format">
                                <?php
                                $exportFormats = [
                                    'excel' => 'Microsoft Excel (.xlsx)',
                                    'pdf' => 'PDF (.pdf)',
                                    'csv' => 'CSV (.csv)'
                                ];
                                
                                foreach ($exportFormats as $value => $label):
                                ?>
                                <option value="<?= $value ?>" 
                                        <?= ($preferences['export_format'] ?? 'excel') === $value ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Salvar Preferências
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Help Section -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ajuda</h6>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="mb-2">Tema</h6>
                        <p class="text-muted small">
                            Escolha entre o tema claro ou escuro para melhor visualização do sistema.
                            O tema escuro pode ajudar a reduzir o cansaço visual em ambientes com pouca luz.
                        </p>
                    </div>

                    <div class="mb-4">
                        <h6 class="mb-2">Fuso Horário</h6>
                        <p class="text-muted small">
                            Defina seu fuso horário para garantir que todas as datas e horários sejam exibidos corretamente
                            de acordo com sua localização.
                        </p>
                    </div>

                    <div class="mb-4">
                        <h6 class="mb-2">Notificações</h6>
                        <p class="text-muted small">
                            Configure como deseja receber notificações do sistema. Você pode optar por receber por e-mail,
                            no navegador, ou ambos.
                        </p>
                    </div>

                    <div class="mb-4">
                        <h6 class="mb-2">Formato de Moeda</h6>
                        <p class="text-muted small">
                            Escolha como os valores monetários devem ser exibidos. Isso afeta apenas a visualização,
                            não a forma como os valores são armazenados.
                        </p>
                    </div>

                    <div class="mb-4">
                        <h6 class="mb-2">Formato de Exportação</h6>
                        <p class="text-muted small">
                            Defina o formato padrão para exportação de relatórios e listagens.
                            Você ainda poderá escolher outros formatos ao exportar.
                        </p>
                    </div>

                    <hr>

                    <div class="d-grid gap-2">
                        <button type="button" 
                                class="btn btn-outline-primary"
                                onclick="resetPreferences()">
                            <i class="bi bi-arrow-counterclockwise"></i> Restaurar Padrões
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
async function resetPreferences() {
    if (!confirm('Tem certeza que deseja restaurar todas as preferências para os valores padrão?')) {
        return;
    }
    
    try {
        const response = await fetch('/settings/reset-preferences', {
            method: 'POST'
        });
        
        if (response.ok) {
            location.reload();
        } else {
            throw new Error('Erro ao restaurar preferências');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Ocorreu um erro ao restaurar as preferências');
    }
}

// Preview theme changes
$('#theme').change(function() {
    const theme = $(this).val();
    $('body').attr('data-bs-theme', theme);
});

// Preview date/time format changes
$('#date_format, #time_format').change(function() {
    const dateFormat = $('#date_format').val();
    const timeFormat = $('#time_format').val();
    
    // Update preview using moment.js
    $('.datetime-preview').each(function() {
        const timestamp = $(this).data('timestamp');
        $(this).text(moment(timestamp).format(`${dateFormat} ${timeFormat}`));
    });
});
</script>
