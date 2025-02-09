#!/bin/bash

# Configurações do FTP
FTP_HOST="ftp.alfadev.online"
FTP_USER="u315624178.gcmanager"
FTP_PASS="gugaLima8*"
FTP_PATH="/public_html"

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${YELLOW}Iniciando deploy...${NC}"

# Verifica se o lftp está instalado
if ! command -v lftp &> /dev/null; then
    echo -e "${RED}lftp não está instalado. Por favor, instale primeiro.${NC}"
    exit 1
fi

# Lista de arquivos e diretórios para excluir do upload
EXCLUDE_LIST=(
    ".git/"
    ".gitignore"
    "*.log"
    "*.tmp"
    "*.temp"
    "*.swp"
    "*.bak"
    "*.old"
    "deploy.sh"
    "README.md"
    "vendor/"
    "node_modules/"
    ".env"
    ".env.*"
    ".idea/"
    ".vscode/"
)

# Cria o arquivo de exclusão
EXCLUDE_FILE=$(mktemp)
for item in "${EXCLUDE_LIST[@]}"; do
    echo "$item" >> "$EXCLUDE_FILE"
done

echo -e "${YELLOW}Conectando ao servidor FTP...${NC}"

# Comando LFTP para upload
lftp -c "
open $FTP_HOST
user $FTP_USER $FTP_PASS
lcd $(pwd)
cd $FTP_PATH
mirror --reverse --delete --verbose --exclude-glob-from=$EXCLUDE_FILE
bye
"

# Remove o arquivo temporário
rm "$EXCLUDE_FILE"

echo -e "${GREEN}Deploy concluído com sucesso!${NC}"
