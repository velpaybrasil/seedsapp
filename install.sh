#!/bin/bash

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Função para exibir mensagens
log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')] $1${NC}"
}

error() {
    echo -e "${RED}[$(date +'%Y-%m-%d %H:%M:%S')] ERROR: $1${NC}"
}

warning() {
    echo -e "${YELLOW}[$(date +'%Y-%m-%d %H:%M:%S')] WARNING: $1${NC}"
}

# Verificar se está rodando como root
if [ "$EUID" -ne 0 ]; then
    error "Este script precisa ser executado como root"
    exit 1
fi

# Atualizar sistema
log "Atualizando sistema..."
apt update && apt upgrade -y

# Instalar dependências
log "Instalando dependências..."
apt install -y \
    apache2 \
    php8.1 \
    php8.1-fpm \
    php8.1-mysql \
    php8.1-xml \
    php8.1-mbstring \
    php8.1-gd \
    php8.1-curl \
    php8.1-zip \
    php8.1-intl \
    mariadb-server \
    certbot \
    python3-certbot-apache \
    git \
    composer \
    npm \
    unzip

# Configurar PHP
log "Configurando PHP..."
sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 10M/' /etc/php/8.1/fpm/php.ini
sed -i 's/post_max_size = 8M/post_max_size = 10M/' /etc/php/8.1/fpm/php.ini
sed -i 's/memory_limit = 128M/memory_limit = 256M/' /etc/php/8.1/fpm/php.ini
systemctl restart php8.1-fpm

# Configurar Apache
log "Configurando Apache..."
a2enmod rewrite
a2enmod ssl
a2enmod headers
a2enmod proxy_fcgi
a2enconf php8.1-fpm

# Criar diretório do projeto
log "Configurando diretório do projeto..."
mkdir -p /var/www/gcmanager
chown -R www-data:www-data /var/www/gcmanager
chmod -R 755 /var/www/gcmanager

# Copiar arquivos do projeto
log "Copiando arquivos do projeto..."
cp -r ./* /var/www/gcmanager/
cd /var/www/gcmanager

# Configurar banco de dados
log "Configurando banco de dados..."
mysql -e "CREATE DATABASE IF NOT EXISTS gcmanager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER IF NOT EXISTS 'gcmanager'@'localhost' IDENTIFIED BY 'gugaLima8*';"
mysql -e "GRANT ALL PRIVILEGES ON gcmanager.* TO 'gcmanager'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

# Importar estrutura do banco
log "Importando estrutura do banco de dados..."
mysql gcmanager < database/schema.sql

# Instalar dependências do projeto
log "Instalando dependências do projeto..."
composer install --no-dev --optimize-autoloader
npm install --production
npm run build

# Configurar arquivo .env
log "Configurando arquivo .env..."
cp .env.example .env
sed -i 's/DB_NAME=.*/DB_NAME=gcmanager/' .env
sed -i 's/DB_USER=.*/DB_USER=gcmanager/' .env
sed -i 's/DB_PASS=.*/DB_PASS=gugaLima8*/' .env

# Configurar virtual host
log "Configurando virtual host..."
cp apache.conf /etc/apache2/sites-available/gcmanager.conf
a2ensite gcmanager.conf
a2dissite 000-default.conf

# Obter certificado SSL
log "Obtendo certificado SSL..."
certbot --apache -d gcmanager.alfadev.online -d www.gcmanager.alfadev.online --non-interactive --agree-tos --email ceo@alfadev.online

# Configurar permissões
log "Configurando permissões..."
chown -R www-data:www-data /var/www/gcmanager
find /var/www/gcmanager -type f -exec chmod 644 {} \;
find /var/www/gcmanager -type d -exec chmod 755 {} \;
chmod -R 777 /var/www/gcmanager/public/uploads
chmod -R 777 /var/www/gcmanager/cache
chmod -R 777 /var/www/gcmanager/logs

# Reiniciar serviços
log "Reiniciando serviços..."
systemctl restart apache2
systemctl restart php8.1-fpm
systemctl restart mariadb

# Configurar backup automático
log "Configurando backup automático..."
cat > /etc/cron.daily/gcmanager-backup << 'EOF'
#!/bin/bash
BACKUP_DIR="/var/backups/gcmanager"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
mkdir -p "$BACKUP_DIR"

# Backup do banco de dados
mysqldump gcmanager > "$BACKUP_DIR/db_$TIMESTAMP.sql"

# Backup dos arquivos
tar -czf "$BACKUP_DIR/files_$TIMESTAMP.tar.gz" -C /var/www gcmanager

# Manter apenas os últimos 7 backups
find "$BACKUP_DIR" -name "db_*" -mtime +7 -delete
find "$BACKUP_DIR" -name "files_*" -mtime +7 -delete
EOF

chmod +x /etc/cron.daily/gcmanager-backup

# Configurar monitoramento
log "Configurando monitoramento..."
cat > /etc/cron.hourly/gcmanager-monitor << 'EOF'
#!/bin/bash
CHECK_URL="https://gcmanager.alfadev.online"
ADMIN_EMAIL="ceo@alfadev.online"

response=$(curl -sL -w "%{http_code}" "$CHECK_URL" -o /dev/null)
if [ "$response" != "200" ]; then
    echo "O site está fora do ar (HTTP $response)" | mail -s "ALERTA: GCManager está offline" "$ADMIN_EMAIL"
fi

# Verificar uso de disco
disk_usage=$(df -h / | awk 'NR==2 {print $5}' | sed 's/%//')
if [ "$disk_usage" -gt 90 ]; then
    echo "Uso de disco está em $disk_usage%" | mail -s "ALERTA: Disco quase cheio no GCManager" "$ADMIN_EMAIL"
fi

# Verificar uso de memória
free_mem=$(free | awk '/Mem:/ {print $4/$2 * 100.0}')
if [ "${free_mem%.*}" -lt 10 ]; then
    echo "Memória livre está em $free_mem%" | mail -s "ALERTA: Pouca memória no GCManager" "$ADMIN_EMAIL"
fi
EOF

chmod +x /etc/cron.hourly/gcmanager-monitor

log "Instalação concluída com sucesso!"
log "O sistema está disponível em: https://gcmanager.alfadev.online"
