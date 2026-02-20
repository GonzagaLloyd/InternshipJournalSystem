FROM php:8.2-apache

# 1. Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libssl-dev \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 2. Install Node.js (LTS version) for frontend build
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# 3. Enable Apache mod_rewrite
RUN a2enmod rewrite

# 4. Configure Apache DocumentRoot to point to /public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 5. Set working directory
WORKDIR /var/www/html

# 6. Copy application code
COPY . /var/www/html

# 7. Install Composer dependencies
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_MEMORY_LIMIT=-1
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs --no-scripts

# 8. Build Frontend Assets then remove Node.js to save runtime memory
RUN npm install && npm run build \
    && rm -rf node_modules \
    && apt-get purge -y nodejs \
    && apt-get autoremove -y \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# 9. Set permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 10. Configure PHP for Low Memory (512MB Render Limit)
RUN echo "memory_limit = 64M" > /usr/local/etc/php/conf.d/memory-limit.ini \
    && echo "opcache.enable=1" > /usr/local/etc/php/conf.d/opcache-settings.ini \
    && echo "opcache.memory_consumption=16" >> /usr/local/etc/php/conf.d/opcache-settings.ini \
    && echo "opcache.interned_strings_buffer=4" >> /usr/local/etc/php/conf.d/opcache-settings.ini \
    && echo "opcache.max_accelerated_files=2000" >> /usr/local/etc/php/conf.d/opcache-settings.ini

# 11. Limit Apache to minimal workers to save RAM
RUN echo "StartServers 1" > /etc/apache2/conf-available/mpm-tuning.conf \
    && echo "MinSpareServers 1" >> /etc/apache2/conf-available/mpm-tuning.conf \
    && echo "MaxSpareServers 2" >> /etc/apache2/conf-available/mpm-tuning.conf \
    && echo "MaxRequestWorkers 5" >> /etc/apache2/conf-available/mpm-tuning.conf \
    && echo "ServerLimit 5" >> /etc/apache2/conf-available/mpm-tuning.conf \
    && a2enconf mpm-tuning

# 12. Handle Render's dynamic PORT & Start Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
CMD sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf && php artisan config:cache && php artisan route:cache && exec apache2-foreground
