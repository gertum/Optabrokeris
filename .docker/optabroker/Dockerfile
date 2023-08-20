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
#COPY --from=builder /var/www/html/ ./
COPY --from=builder /var/www/html/app ./app
COPY --from=builder /var/www/html/bootstrap ./bootstrap
COPY --from=builder /var/www/html/config ./config
COPY --from=builder /var/www/html/database ./database
COPY --from=builder /var/www/html/public ./public
COPY --from=builder /var/www/html/resources ./resources
COPY --from=builder /var/www/html/routes ./routes
COPY --from=builder /var/www/html/storage ./storage
COPY --from=builder /var/www/html/vendor ./vendor
