#!/bin/sh
set -e

# Render define PORT=10000 (ajusta o Apache para a porta correta)
PORT="${PORT:-10000}"
sed -i "s/^Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf

# Banco SQLite: cria se não existir e garante dono www-data
[ -f database/database.sqlite ] || touch database/database.sqlite
chown -R www-data:www-data database storage bootstrap/cache

# Migra e semeia a cada inicialização (seeders idempotentes)
php artisan migrate --force
php artisan db:seed --force

exec "$@"
