FROM node:22.9 as nodebuilder

WORKDIR /app
COPY . .
RUN rm -rf node_modules storage/app vendor composer.lock .env package-lock.json public/hot

RUN npm install
RUN npm run build

FROM serversideup/php:8.3-fpm-nginx as runner

COPY --from=nodebuilder /app/ /var/www/html/

USER root

RUN install-php-extensions intl &&\
    apt update && apt install ffmpeg -y &&\
    docker-php-serversideup-set-id www-data 33:33 && \
    chown -R 33:33 .


RUN composer install

ENV AUTORUN_ENABLED=true
