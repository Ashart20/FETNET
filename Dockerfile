# Gunakan PHP 8.2 dengan FPM
FROM php:8.2-fpm

# Tambahkan command untuk membuat symlink saat container start
RUN echo "#!/bin/sh\n\
mkdir -p /var/www/watcher \n\
ln -sf /mnt/fet-results /var/www/watcher/fet-results \n\
exec php-fpm" > /usr/local/bin/startup
RUN chmod +x /usr/local/bin/startup

ENTRYPOINT ["startup"]

# Install dependensi yang diperlukan
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo pdo_mysql mbstring xml zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory ke /var/www
WORKDIR /var/www/html
COPY . .
# Berikan permission agar storage dan bootstrap/cache bisa diakses Laravel
RUN mkdir -p /var/www/storage /var/www/bootstrap/cache \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

RUN mkdir -p /var/www/html/storage/app/fet-results && \
    chown -R www-data:www-data /var/www/html/storage/app/fet-results && \
    chmod -R 775 /var/www/html/storage/app/fet-results

# Set permission untuk www-data
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

# Expose port untuk Nginx
EXPOSE 9000

CMD ["php-fpm"]
