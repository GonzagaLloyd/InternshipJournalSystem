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
    git \
    libssl-dev \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 2. Install Node.js (LTS version) for frontend build
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# 3. Clean up apt cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

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

# 8. Install Composer dependencies
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_MEMORY_LIMIT=-1
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs --no-scripts --ignore-platform-req=ext-mongodb


# 9. Build Frontend Assets
RUN npm install
RUN npm run build

# 10. Set permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 11. Configure PHP for Low Memory (512MB Limit)
RUN echo "memory_limit = 128M" > /usr/local/etc/php/conf.d/memory-limit.ini
RUN echo "opcache.enable=1" > /usr/local/etc/php/conf.d/opcache.ini
RUN echo "opcache.memory_consumption=32" >> /usr/local/etc/php/conf.d/opcache.ini
RUN echo "opcache.interned_strings_buffer=4" >> /usr/local/etc/php/conf.d/opcache.ini
RUN echo "opcache.max_accelerated_files=2000" >> /usr/local/etc/php/conf.d/opcache.ini

# 12. Handle Render's dynamic PORT & Start Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
CMD sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf && php artisan config:cache && php artisan route:cache && php artisan view:cache && exec apache2-foreground
