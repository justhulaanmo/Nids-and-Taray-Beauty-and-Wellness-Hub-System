FROM php:8.3-cli-alpine

# Install system dependencies & PostgreSQL dev libraries
RUN apk add --no-cache \
    postgresql-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    bash

# Install PHP extensions for Laravel and Supabase Postgres
RUN docker-php-ext-install pdo pdo_pgsql bcmath opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy all project files
COPY . .

# Install PHP dependencies without dev packages
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set correct permissions
RUN chmod -R 777 storage bootstrap/cache

# Render provides the $PORT environment variable (default 10000)
ENV PORT=10000
EXPOSE 10000

# Start Laravel built-in web server binding to $PORT
CMD ["sh", "-c", "php artisan config:clear && php artisan view:cache && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"]
