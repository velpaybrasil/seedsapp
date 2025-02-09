<?php
// Habilita exibição de erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Informações sobre o ambiente
echo "<h1>Teste do Ambiente PHP</h1>";
echo "<h2>Informações do Servidor</h2>";
echo "<pre>";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "\n";
echo "PHP_SELF: " . $_SERVER['PHP_SELF'] . "\n";
echo "DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "</pre>";

// Teste de conexão com o banco de dados
echo "<h2>Teste de Conexão com o Banco de Dados</h2>";
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=u315624178_gcmanager",
        "root",
        ""
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>Conexão com o banco de dados estabelecida com sucesso!</p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>Erro na conexão com o banco de dados: " . $e->getMessage() . "</p>";
}

// Teste de diretórios
echo "<h2>Teste de Diretórios</h2>";
echo "<pre>";
echo "Diretório Atual: " . getcwd() . "\n";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Permissões do diretório atual: " . substr(sprintf('%o', fileperms(getcwd())), -4) . "\n";
echo "</pre>";
