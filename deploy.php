<?php

class Deployer {
    private string $repoUrl = 'https://github.com/seu-usuario/seedsapp.git';
    private string $branch = 'main';
    private string $deployPath;
    private array $excludedFiles = [
        '.git',
        '.env',
        'deploy.php',
        'README.md',
        'composer.lock',
        'node_modules',
        'tests',
        '.gitignore'
    ];
    
    public function __construct() {
        $this->deployPath = dirname(__FILE__);
    }
    
    public function deploy() {
        echo "Iniciando deploy...\n";
        
        // 1. Criar estrutura de pastas necessária
        $this->createDirectoryStructure();
        
        // 2. Copiar arquivos para a pasta pública
        $this->copyPublicFiles();
        
        // 3. Configurar .htaccess na raiz
        $this->setupRootHtaccess();
        
        echo "Deploy concluído com sucesso!\n";
    }
    
    private function createDirectoryStructure() {
        echo "Criando estrutura de diretórios...\n";
        
        // Garantir que as pastas necessárias existam
        $directories = [
            'app',
            'bootstrap',
            'config',
            'public',
            'resources',
            'routes',
            'storage/logs',
            'storage/cache',
            'vendor'
        ];
        
        foreach ($directories as $dir) {
            $path = $this->deployPath . '/' . $dir;
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
                echo "Diretório criado: $dir\n";
            }
        }
    }
    
    private function copyPublicFiles() {
        echo "Copiando arquivos públicos...\n";
        
        // Copiar conteúdo da pasta public para public_html
        $publicPath = $this->deployPath . '/public';
        $publicHtmlPath = dirname($this->deployPath) . '/public_html';
        
        if (!file_exists($publicHtmlPath)) {
            mkdir($publicHtmlPath, 0755, true);
        }
        
        // Copiar arquivos da pasta public
        $this->recursiveCopy($publicPath, $publicHtmlPath);
        
        // Atualizar index.php para apontar para o diretório correto
        $indexContent = file_get_contents($publicHtmlPath . '/index.php');
        $indexContent = str_replace(
            "define('ROOT_PATH', dirname(__DIR__))",
            "define('ROOT_PATH', dirname(__DIR__) . '/seedsapp')",
            $indexContent
        );
        file_put_contents($publicHtmlPath . '/index.php', $indexContent);
        
        echo "Arquivos públicos copiados com sucesso!\n";
    }
    
    private function setupRootHtaccess() {
        echo "Configurando .htaccess na raiz...\n";
        
        $htaccessContent = "
# Proteger arquivos e diretórios
<FilesMatch \"^\.">
    Order allow,deny
    Deny from all
</FilesMatch>

Options -Indexes

# Redirecionar tudo para public_html
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^$ public_html/ [L]
    RewriteRule (.*) public_html/$1 [L]
</IfModule>
        ";
        
        file_put_contents($this->deployPath . '/.htaccess', $htaccessContent);
        echo ".htaccess configurado com sucesso!\n";
    }
    
    private function recursiveCopy($src, $dst) {
        $dir = opendir($src);
        @mkdir($dst);
        
        while (($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->recursiveCopy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        
        closedir($dir);
    }
}

// Executar deploy
try {
    $deployer = new Deployer();
    $deployer->deploy();
    echo "Deploy concluído com sucesso!\n";
} catch (Exception $e) {
    echo "Erro durante o deploy: " . $e->getMessage() . "\n";
    exit(1);
}
