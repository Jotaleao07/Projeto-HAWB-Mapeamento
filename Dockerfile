FROM php:8.4-apache

# Extensões PHP usadas pela aplicação (banco SQLite, mbstring, opcache)
RUN apt-get update && apt-get install -y --no-install-recommends \
        libonig-dev \
        libsqlite3-dev \
        unzip \
    && docker-php-ext-install -j"$(nproc)" pdo_sqlite mbstring opcache \
    && rm -rf /var/lib/apt/lists/*

# Composer 2
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Código da aplicação
COPY . .

# Dependências de produção
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Apache: DocumentRoot explícito em /public do Laravel (substitui o vhost padrão)
COPY docker-vhost.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

COPY docker-entrypoint.sh /usr/local/bin/hawb-entrypoint
RUN chmod +x /usr/local/bin/hawb-entrypoint

# Porta padrão do Render (o entrypoint ajusta para $PORT)
EXPOSE 10000

ENTRYPOINT ["hawb-entrypoint"]
CMD ["apache2-foreground"]
