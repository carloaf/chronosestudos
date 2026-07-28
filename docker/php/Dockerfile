FROM php:8.2-apache

# ── System dependencies ────────────────────────────
RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

# ── PHP extensions ─────────────────────────────────
RUN docker-php-ext-install pdo_mysql zip

# ── Apache modules ─────────────────────────────────
RUN a2enmod rewrite

# ── Custom Apache VirtualHost ──────────────────────
COPY docker/php/apache.conf /etc/apache2/sites-available/000-default.conf

# ── Custom php.ini ─────────────────────────────────
COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-chronos.ini

# ── Composer ───────────────────────────────────────
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# ── Permissions (storage & bootstrap/cache) ────────
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

WORKDIR /var/www/html
EXPOSE 80
