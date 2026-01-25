# 1. Build Frontend Assets
FROM node:20 AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# 2. Production Image
FROM serversideup/php:8.3-fpm-nginx AS production

ENV PHP_OPCACHE_ENABLE=1
ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS=0

WORKDIR /var/www/html

# Switch to root to install extensions if needed (uncomment if necessary)
# USER root
# RUN install-php-extensions bcmath intl
# USER webuser

# Copy Composer dependencies
COPY --chown=webuser:webuser composer.json composer.lock ./

# Install dependencies (no-dev for production)
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Copy the rest of the application
COPY --chown=webuser:webuser . .

# Copy built frontend assets from the frontend stage
COPY --from=frontend --chown=webuser:webuser /app/public/build ./public/build

# Ensure storage permissions (if needed, though webuser owns it now)
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 80
