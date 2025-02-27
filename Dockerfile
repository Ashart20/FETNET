# Gunakan PHP 8.2 dengan FPM
FROM php:8.2-fpm

# Install dependensi yang diperlukan
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libonig-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo pdo_mysql
RUN docker-php-ext-install pdo pdo_mysql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory ke /var/www
WORKDIR /var/www

# Berikan permission agar storage dan bootstrap/cache bisa diakses Laravel
RUN mkdir -p /var/www/storage /var/www/bootstrap/cache && \
    chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Expose port untuk Nginx
EXPOSE 9000

CMD ["php-fpm"]
