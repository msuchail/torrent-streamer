FROM node:22.9 as nodebuilder

WORKDIR /app
COPY . .
RUN rm -rf node_modules storage/app vendor composer.lock .env package-lock.json public/hot

RUN npm install
RUN npm run build

FROM php:8.3-fpm-bullseye as runner

WORKDIR /var/www/html/app
COPY --from=nodebuilder /app .

RUN chown -R www-data:www-data .

# Install Nginx
RUN apt update && apt install -y nginx
# Copy nginx configuration
COPY docker/nginx.conf /etc/nginx/sites-available/default

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHP extensions
RUN apt update && apt install -y \
    screen \
    default-mysql-client \
    systemctl \
    libicu-dev \
    libpq-dev \
    libzip-dev \
    && docker-php-ext-install pdo_mysql intl zip

RUN docker-php-ext-install pdo_mysql intl

RUN composer install --no-dev

CMD service nginx start && screen -d -m php artisan queue:work --timeout=3600 && php-fpm
