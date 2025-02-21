<?php

$phpini = 'C:\php\php.ini';
$backup = 'C:\php\php.ini.bak';

// Fazer backup do php.ini
if (!copy($phpini, $backup)) {
    die("Erro ao fazer backup do php.ini\n");
}

echo "Backup criado: {$backup}\n";

// Ler o conteúdo do php.ini
$content = file_get_contents($phpini);
if ($content === false) {
    die("Erro ao ler php.ini\n");
}

// Descomentar a extensão pdo_mysql
$content = str_replace(';extension=pdo_mysql', 'extension=pdo_mysql', $content);

// Salvar as alterações
if (file_put_contents($phpini, $content) === false) {
    die("Erro ao salvar php.ini\n");
}

echo "Extensão pdo_mysql habilitada com sucesso!\n";
echo "Por favor, reinicie o servidor web para aplicar as alterações.\n";
