#!/bin/sh
set -e

# Render define PORT=10000 (ajusta o Apache: porta de escuta E vhost)
# Sem isso o vhost fica em *:80, o trafego chega em :10000 e o Apache
# cai na config padrao servindo /var/www/html (erro AH01276).
PORT="${PORT:-10000}"
sed -i "s/^Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s|<VirtualHost \*:80>|<VirtualHost *:${PORT}>|" /etc/apache2/sites-available/000-default.conf

# Diagnóstico: mostra a config efetiva nos logs do Render
echo "--- Config Apache ---"
grep -n "Listen\|VirtualHost\|DocumentRoot" /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf 2>/dev/null || true
echo "----------------------"

# Banco SQLite: cria se não existir e garante dono www-data
[ -f database/database.sqlite ] || touch database/database.sqlite
chown -R www-data:www-data database storage bootstrap/cache

# Migra e semeia a cada inicialização (seeders idempotentes)
php artisan migrate --force
php artisan db:seed --force

exec "$@"
