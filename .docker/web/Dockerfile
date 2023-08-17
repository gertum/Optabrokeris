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

# Clean webserver image
FROM nginx:1.25.1
WORKDIR /var/www/html
COPY --from=builder /var/www/html/ ./
