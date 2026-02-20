FROM php:8.3-apache

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
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# 9. Build Frontend Assets
RUN npm install
RUN npm run build

# 10. Set permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 11. Handle Render's dynamic PORT
# Render provides a $PORT environment variable. Apache must listen on it.
# We create a script to update ports.conf at runtime.
RUN echo '#!/bin/bash\n\
sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf\n\
php artisan config:cache\n\
php artisan route:cache\n\
php artisan view:cache\n\
exec apache2-foreground' > /usr/local/bin/start-container.sh
RUN chmod +x /usr/local/bin/start-container.sh

# 12. Define Entrypoint
CMD ["/usr/local/bin/start-container.sh"]
