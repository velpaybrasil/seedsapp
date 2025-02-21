<?php

echo "=== Informações do PHP ===\n";
echo "Versão do PHP: " . PHP_VERSION . "\n";
echo "Extensões carregadas:\n";
print_r(get_loaded_extensions());

echo "\n=== Configuração do PHP ===\n";
echo "extension_dir: " . ini_get('extension_dir') . "\n";
echo "display_errors: " . ini_get('display_errors') . "\n";
echo "error_reporting: " . ini_get('error_reporting') . "\n";
echo "error_log: " . ini_get('error_log') . "\n";

echo "\n=== PDO Drivers ===\n";
if (class_exists('PDO')) {
    echo "PDO está instalado\n";
    echo "Drivers disponíveis:\n";
    print_r(PDO::getAvailableDrivers());
} else {
    echo "PDO não está instalado\n";
}

echo "\n=== MySQL ===\n";
if (extension_loaded('mysql')) {
    echo "Extensão mysql está carregada\n";
} else {
    echo "Extensão mysql não está carregada\n";
}

if (extension_loaded('mysqli')) {
    echo "Extensão mysqli está carregada\n";
} else {
    echo "Extensão mysqli não está carregada\n";
}

if (extension_loaded('pdo_mysql')) {
    echo "Extensão pdo_mysql está carregada\n";
} else {
    echo "Extensão pdo_mysql não está carregada\n";
}
