FROM php:8.2-apache

# 1. Install system dependencies, CA certificates, and PHP extensions
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
    && update-ca-certificates \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 2. Fix OpenSSL 3.0 TLS compatibility with MongoDB Atlas
# Debian Bookworm uses SECLEVEL=2 which rejects some MongoDB Atlas TLS handshakes
RUN sed -i 's/CipherString = DEFAULT:@SECLEVEL=2/CipherString = DEFAULT:@SECLEVEL=1/' /etc/ssl/openssl.cnf || true

# 3. Install Node.js (LTS version) for frontend build
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# 4. Enable Apache mod_rewrite
RUN a2enmod rewrite

# 5. Configure Apache DocumentRoot to point to /public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 6. Set working directory
WORKDIR /var/www/html

# 7. Copy application code
COPY . /var/www/html

# 8. Install Composer dependencies (scripts needed for package:discover)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_MEMORY_LIMIT=-1
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# 9. Ensure storage directories exist
RUN mkdir -p storage/framework/sessions \
    && mkdir -p storage/framework/views \
    && mkdir -p storage/framework/cache/data \
    && mkdir -p storage/logs \
    && touch storage/logs/laravel.log

# 10. Create a placeholder .env so artisan commands don't fail
RUN touch .env

# 11. Build Frontend Assets then remove Node.js to save runtime memory
RUN npm install && npm run build \
    && rm -rf node_modules \
    && apt-get purge -y nodejs \
    && apt-get autoremove -y \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# 12. Set permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 13. Configure PHP for Low Memory (512MB Render Limit)
RUN echo "memory_limit = 64M" > /usr/local/etc/php/conf.d/memory-limit.ini \
    && echo "opcache.enable=1" > /usr/local/etc/php/conf.d/opcache-settings.ini \
    && echo "opcache.memory_consumption=16" >> /usr/local/etc/php/conf.d/opcache-settings.ini \
    && echo "opcache.interned_strings_buffer=4" >> /usr/local/etc/php/conf.d/opcache-settings.ini \
    && echo "opcache.max_accelerated_files=2000" >> /usr/local/etc/php/conf.d/opcache-settings.ini

# 14. Limit Apache workers to save RAM
RUN echo "StartServers 2" > /etc/apache2/conf-available/mpm-tuning.conf \
    && echo "MinSpareServers 2" >> /etc/apache2/conf-available/mpm-tuning.conf \
    && echo "MaxSpareServers 4" >> /etc/apache2/conf-available/mpm-tuning.conf \
    && echo "MaxRequestWorkers 20" >> /etc/apache2/conf-available/mpm-tuning.conf \
    && echo "ServerLimit 20" >> /etc/apache2/conf-available/mpm-tuning.conf \
    && a2enconf mpm-tuning

# 15. Configure Apache to listen on 0.0.0.0 and handle Render's dynamic PORT
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Use a startup script to properly configure the port and start Apache
RUN printf '#!/bin/bash\nset -e\necho "Listen 0.0.0.0:${PORT}" > /etc/apache2/ports.conf\nsed -i "s/<VirtualHost \\*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf\nphp artisan config:cache\nphp artisan route:cache\nexec apache2-foreground\n' > /usr/local/bin/start.sh \
    && chmod +x /usr/local/bin/start.sh

CMD ["bash", "/usr/local/bin/start.sh"]
