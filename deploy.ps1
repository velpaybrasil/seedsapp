# Configurações do FTP
$ftpHost = "ftp.alfadev.online"
$ftpUser = "u315624178.gcmanager"
$ftpPass = "gugaLima8*"
$ftpPath = "/public_html"

# Converte a senha para formato seguro
$securePass = ConvertTo-SecureString $ftpPass -AsPlainText -Force
$credentials = New-Object System.Management.Automation.PSCredential($ftpUser, $securePass)

Write-Host "Iniciando deploy..." -ForegroundColor Yellow

# Lista de arquivos e diretórios para excluir do upload
$excludeList = @(
    ".git",
    ".gitignore",
    "*.log",
    "*.tmp",
    "*.temp",
    "*.swp",
    "*.bak",
    "*.old",
    "deploy.ps1",
    "deploy.sh",
    "README.md",
    "vendor",
    "node_modules",
    ".env",
    ".env.*",
    ".idea",
    ".vscode"
)

# Cria uma sessão FTP
$ftpUrl = "ftp://$ftpHost$ftpPath"
$webclient = New-Object System.Net.WebClient
$webclient.Credentials = $credentials

# Função para fazer upload de um arquivo
function Upload-File {
    param (
        [string]$localPath,
        [string]$remotePath
    )
    
    try {
        Write-Host "Enviando $localPath..." -ForegroundColor Cyan
        $webclient.UploadFile("$ftpUrl/$remotePath", $localPath)
        Write-Host "Enviado com sucesso!" -ForegroundColor Green
    }
    catch {
        Write-Host "Erro ao enviar $localPath : $_" -ForegroundColor Red
    }
}

# Função para verificar se um arquivo deve ser excluído
function Should-Exclude {
    param (
        [string]$path
    )
    
    foreach ($pattern in $excludeList) {
        if ($path -like $pattern) {
            return $true
        }
    }
    return $false
}

# Função recursiva para fazer upload de diretórios
function Upload-Directory {
    param (
        [string]$localPath,
        [string]$remotePath
    )
    
    # Cria o diretório remoto se não existir
    try {
        $request = [System.Net.FtpWebRequest]::Create("$ftpUrl/$remotePath")
        $request.Method = [System.Net.WebRequestMethods+Ftp]::MakeDirectory
        $request.Credentials = $credentials
        $request.GetResponse()
    }
    catch { }
    
    # Faz upload dos arquivos no diretório
    Get-ChildItem $localPath | ForEach-Object {
        $newLocalPath = $_.FullName
        $newRemotePath = "$remotePath/$($_.Name)"
        
        if (-not (Should-Exclude $_.Name)) {
            if ($_.PSIsContainer) {
                Upload-Directory $newLocalPath $newRemotePath
            }
            else {
                Upload-File $newLocalPath $newRemotePath
            }
        }
    }
}

# Inicia o upload
try {
    Write-Host "Conectando ao servidor FTP..." -ForegroundColor Yellow
    Upload-Directory (Get-Location) ""
    Write-Host "Deploy concluído com sucesso!" -ForegroundColor Green
}
catch {
    Write-Host "Erro durante o deploy: $_" -ForegroundColor Red
}
finally {
    $webclient.Dispose()
}
