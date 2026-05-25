FROM dunglas/frankenphp:php8.4

RUN install-php-extensions \
    pdo_mysql \
    mbstring \
    bcmath \
    curl \
    dom \
    fileinfo \
    xml \
    zip

WORKDIR /app

COPY . .

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --optimize-autoloader --no-interaction

EXPOSE 80

CMD sh -c "php artisan migrate --force && frankenphp php-server -r public/ --listen 0.0.0.0:80"