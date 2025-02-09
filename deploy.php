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
    
    public function deploy(): void {
        try {
            $this->checkRequirements();
            $this->backup();
            $this->pullChanges();
            $this->updateDependencies();
            $this->clearCache();
            $this->setPermissions();
            $this->runMigrations();
            
            $this->log('Deploy realizado com sucesso!');
        } catch (\Exception $e) {
            $this->log('Erro no deploy: ' . $e->getMessage(), 'ERROR');
            throw $e;
        }
    }
    
    private function checkRequirements(): void {
        // Verificar PHP version
        if (version_compare(PHP_VERSION, '8.0.0', '<')) {
            throw new \Exception('PHP 8.0 ou superior é necessário');
        }
        
        // Verificar extensões necessárias
        $requiredExtensions = ['pdo', 'pdo_mysql', 'mbstring', 'xml', 'gd'];
        foreach ($requiredExtensions as $ext) {
            if (!extension_loaded($ext)) {
                throw new \Exception("Extensão PHP '$ext' não está instalada");
            }
        }
        
        // Verificar se o Composer está instalado
        exec('composer --version', $output, $returnVar);
        if ($returnVar !== 0) {
            throw new \Exception('Composer não está instalado');
        }
    }
    
    private function backup(): void {
        $backupDir = $this->deployPath . '/backups';
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        
        $timestamp = date('Y-m-d_H-i-s');
        $backupFile = "$backupDir/backup_$timestamp.zip";
        
        $zip = new \ZipArchive();
        if ($zip->open($backupFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            $this->addFolderToZip($this->deployPath, $zip);
            $zip->close();
            
            // Manter apenas os últimos 5 backups
            $backups = glob("$backupDir/backup_*.zip");
            if (count($backups) > 5) {
                array_map('unlink', array_slice($backups, 0, -5));
            }
        } else {
            throw new \Exception('Não foi possível criar o backup');
        }
    }
    
    private function addFolderToZip(string $folder, \ZipArchive $zip, string $subfolder = ''): void {
        $handle = opendir($folder);
        while (false !== ($entry = readdir($handle))) {
            if ($entry === '.' || $entry === '..' || in_array($entry, $this->excludedFiles)) {
                continue;
            }
            
            $filePath = "$folder/$entry";
            $zipPath = $subfolder ? "$subfolder/$entry" : $entry;
            
            if (is_file($filePath)) {
                $zip->addFile($filePath, $zipPath);
            } elseif (is_dir($filePath)) {
                $zip->addEmptyDir($zipPath);
                $this->addFolderToZip($filePath, $zip, $zipPath);
            }
        }
        closedir($handle);
    }
    
    private function pullChanges(): void {
        // Verificar se é um repositório git
        if (!is_dir($this->deployPath . '/.git')) {
            exec("git clone -b {$this->branch} {$this->repoUrl} .");
        } else {
            exec('git fetch origin');
            exec("git checkout {$this->branch}");
            exec('git pull origin ' . $this->branch);
        }
    }
    
    private function updateDependencies(): void {
        // Atualizar dependências do Composer
        exec('composer install --no-dev --optimize-autoloader');
        
        // Atualizar dependências do npm se houver
        if (file_exists($this->deployPath . '/package.json')) {
            exec('npm install --production');
            exec('npm run build');
        }
    }
    
    private function clearCache(): void {
        $cacheDirs = [
            $this->deployPath . '/cache',
            $this->deployPath . '/public/cache'
        ];
        
        foreach ($cacheDirs as $dir) {
            if (is_dir($dir)) {
                $this->removeDirectory($dir);
                mkdir($dir, 0755);
            }
        }
    }
    
    private function removeDirectory(string $dir): void {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object !== '.' && $object !== '..') {
                    $path = $dir . '/' . $object;
                    if (is_dir($path)) {
                        $this->removeDirectory($path);
                    } else {
                        unlink($path);
                    }
                }
            }
            rmdir($dir);
        }
    }
    
    private function setPermissions(): void {
        $dirs = [
            $this->deployPath . '/public' => 0755,
            $this->deployPath . '/cache' => 0755,
            $this->deployPath . '/logs' => 0755,
            $this->deployPath . '/uploads' => 0755
        ];
        
        foreach ($dirs as $dir => $perm) {
            if (!is_dir($dir)) {
                mkdir($dir, $perm, true);
            } else {
                chmod($dir, $perm);
            }
        }
    }
    
    private function runMigrations(): void {
        try {
            $this->log('Executando migrações...');
            
            // Conectar ao banco de dados
            $db = new \PDO(
                "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=utf8mb4",
                $_ENV['DB_USER'],
                $_ENV['DB_PASS'],
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                ]
            );

            // Obter lista de migrações
            $migrations = glob($this->deployPath . '/database/migrations/*.sql');
            sort($migrations); // Garantir ordem de execução
            
            foreach ($migrations as $migration) {
                $sql = file_get_contents($migration);
                
                // Dividir o SQL em statements individuais
                $statements = array_filter(
                    array_map('trim', explode(';', $sql)),
                    function($statement) {
                        return !empty($statement);
                    }
                );
                
                // Executar cada statement separadamente
                foreach ($statements as $statement) {
                    $db->query($statement);
                }
                
                $this->log('Migração executada: ' . basename($migration));
            }
        } catch (\PDOException $e) {
            throw new \Exception('Erro ao executar migrações: ' . $e->getMessage());
        }
    }
    
    private function log(string $message, string $level = 'INFO'): void {
        $logFile = $this->deployPath . '/logs/deploy.log';
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] [$level] $message\n";
        
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0755, true);
        }
        
        file_put_contents($logFile, $logMessage, FILE_APPEND);
        echo $logMessage;
    }
}

// Executar deploy
try {
    $deployer = new Deployer();
    $deployer->deploy();
} catch (\Exception $e) {
    die("Erro no deploy: " . $e->getMessage() . "\n");
}
