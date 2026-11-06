#!/bin/sh
set -e

# Render define PORT=10000 (ajusta o Apache para a porta correta)
PORT="${PORT:-10000}"
sed -i "s/^Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf

# Diagnóstico: mostra o DocumentRoot efetivo nos logs do Render
echo "--- Config Apache ---"
grep -rn "DocumentRoot" /etc/apache2/sites-available/ /etc/apache2/sites-enabled/ 2>/dev/null || true
echo "----------------------"

# Banco SQLite: cria se não existir e garante dono www-data
[ -f database/database.sqlite ] || touch database/database.sqlite
chown -R www-data:www-data database storage bootstrap/cache

# Migra e semeia a cada inicialização (seeders idempotentes)
php artisan migrate --force
php artisan db:seed --force

exec "$@"
