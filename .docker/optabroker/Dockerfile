FROM registry.gitlab.com/inkodus-opta/docker-base-images/composer:latest as builder

WORKDIR /var/www/html
COPY . .

ARG COMPOSER_AUTH
ENV COMPOSER_AUTH=$COMPOSER_AUTH
ENV COMPOSER_ALLOW_SUPERUSER=1

# Build project
RUN composer install && \
    npm install && \
    npm run build

# Clean image
FROM registry.gitlab.com/inkodus-opta/docker-base-images/fpm:latest
WORKDIR /var/www/html
#COPY --from=builder /var/www/html/app /var/www/html/backup /var/www/html/bootstrap /var/www/html/config /var/www/html/data /var/www/html/database /var/www/html/public /var/www/html/resources /var/www/html/routes /var/www/html/shared /var/www/html/storage /var/www/html/vendor /var/www/html/.env /var/www/html/.env.dist /var/www/html/artisan /var/www/html/phpunit.xml /var/www/html/postcss.config.js /var/www/html/tailwind.config.js /var/www/html/vite.config.js ./
COPY --from=builder /var/www/html/ ./