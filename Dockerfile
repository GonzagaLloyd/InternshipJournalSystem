FROM php:8.1-apache-bullseye

# 1. Install system dependencies and CA certificates
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libssl-dev \
    ca-certificates \
    && update-ca-certificates

# 2. Install the CORRECT version of MongoDB PHP extension (1.x, not 2.x)
# composer.lock requires ext-mongodb ^1.21.0 - version 2.x is incompatible
RUN pecl install mongodb-1.21.1 \
    && docker-php-ext-enable mongodb

# 3. Install other PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 4. Install Node.js (LTS version) for frontend build
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# 5. Enable Apache mod_rewrite
RUN a2enmod rewrite

# 6. Configure Apache DocumentRoot to point to /public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 7. Set working directory
WORKDIR /var/www/html

# 8. Copy application code
COPY . /var/www/html

# 9. Install Composer dependencies
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_MEMORY_LIMIT=-1
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# 11. Ensure storage directories exist
RUN mkdir -p storage/framework/sessions \
    && mkdir -p storage/framework/views \
    && mkdir -p storage/framework/cache/data \
    && mkdir -p storage/logs \
    && touch storage/logs/laravel.log

# 12. Create a placeholder .env so artisan commands don't fail
RUN touch .env

# 13. Build Frontend Assets then remove Node.js to save runtime memory
RUN npm install && npm run build \
    && rm -rf node_modules \
    && apt-get purge -y nodejs \
    && apt-get autoremove -y \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# 14. Set permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 15. Configure PHP for Low Memory (512MB Render Limit)
RUN echo "memory_limit = 64M" > /usr/local/etc/php/conf.d/memory-limit.ini \
    && echo "opcache.enable=1" > /usr/local/etc/php/conf.d/opcache-settings.ini \
    && echo "opcache.memory_consumption=16" >> /usr/local/etc/php/conf.d/opcache-settings.ini \
    && echo "opcache.interned_strings_buffer=4" >> /usr/local/etc/php/conf.d/opcache-settings.ini \
    && echo "opcache.max_accelerated_files=2000" >> /usr/local/etc/php/conf.d/opcache-settings.ini

# 16. Limit Apache workers to save RAM
RUN echo "StartServers 2" > /etc/apache2/conf-available/mpm-tuning.conf \
    && echo "MinSpareServers 2" >> /etc/apache2/conf-available/mpm-tuning.conf \
    && echo "MaxSpareServers 4" >> /etc/apache2/conf-available/mpm-tuning.conf \
    && echo "MaxRequestWorkers 25" >> /etc/apache2/conf-available/mpm-tuning.conf \
    && echo "ServerLimit 25" >> /etc/apache2/conf-available/mpm-tuning.conf \
    && a2enconf mpm-tuning

# 17. Configure Apache and create startup script
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Create startup script with proper line endings
RUN echo '#!/bin/bash' > /usr/local/bin/start.sh && \
    echo 'set -e' >> /usr/local/bin/start.sh && \
    echo 'echo "Listen 0.0.0.0:${PORT}" > /etc/apache2/ports.conf' >> /usr/local/bin/start.sh && \
    echo 'sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf' >> /usr/local/bin/start.sh && \
    echo 'php artisan config:cache' >> /usr/local/bin/start.sh && \
    echo 'php artisan route:cache' >> /usr/local/bin/start.sh && \
    echo 'exec apache2-foreground' >> /usr/local/bin/start.sh && \
    chmod +x /usr/local/bin/start.sh

CMD ["bash", "/usr/local/bin/start.sh"]
