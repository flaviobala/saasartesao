#!/bin/bash

# Script de deploy - SaaS Artesão
# Uso: bash deploy.sh

set -e

APP_DIR="/var/www/saasartesao"
WEB_USER="www-data"
DEPLOY_USER="flavioadm"

echo "🚀 Iniciando deploy..."

cd "$APP_DIR"

# 1. Atualizar código
echo "📦 Atualizando código..."
git pull

# 2. Instalar dependências PHP (sem dev)
echo "🔧 Instalando dependências PHP..."
composer install --no-dev --optimize-autoloader --no-interaction

# 3. Build dos assets front-end
echo "🎨 Compilando assets..."
npm install --silent && npm run build

# 4. Limpar caches
echo "🧹 Limpando caches..."
sudo -u "$WEB_USER" php artisan config:clear
sudo -u "$WEB_USER" php artisan route:clear
sudo -u "$WEB_USER" php artisan view:clear
sudo -u "$WEB_USER" php artisan cache:clear

# 5. Rodar migrações
echo "🗄️  Rodando migrações..."
sudo -u "$WEB_USER" php artisan migrate --force

# 6. Recriar caches otimizados
echo "⚡ Recriando caches..."
sudo -u "$WEB_USER" php artisan config:cache
sudo -u "$WEB_USER" php artisan route:cache
sudo -u "$WEB_USER" php artisan view:cache

# 7. Corrigir permissões
echo "🔐 Corrigindo permissões..."
sudo chown -R "$DEPLOY_USER":"$WEB_USER" "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" "$APP_DIR/database"
sudo chmod -R 775 "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" "$APP_DIR/database"
sudo chmod 664 "$APP_DIR/database/database.sqlite"
sudo chmod g+s "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" "$APP_DIR/database"

echo "✅ Deploy concluído com sucesso!"
