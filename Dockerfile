FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    nginx \
    supervisor \
    && docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd

# Install Node.js for building assets
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy existing application directory contents
COPY . /var/www/html

# Install PHP dependencies
RUN composer install --no-interaction --no-dev --optimize-autoloader

# Install JS dependencies and build assets
RUN npm install && npm run build

# Remove node_modules after build to save space
RUN rm -rf node_modules

# Create system user to run Composer and Artisan Commands
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Nginx Configuration
RUN echo "server { \n\
    listen 80; \n\
    index index.php index.html; \n\
    root /var/www/html/public; \n\
    client_max_body_size 100M; \n\
    location / { \n\
        try_files \$uri \$uri/ /index.php?\$query_string; \n\
    } \n\
    location ~ \.php$ { \n\
        fastcgi_pass 127.0.0.1:9000; \n\
        fastcgi_index index.php; \n\
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name; \n\
        include fastcgi_params; \n\
    } \n\
}" > /etc/nginx/sites-available/default

# Supervisor Configuration
RUN echo "[supervisord] \n\
nodaemon=true \n\
\n\
[program:nginx] \n\
command=nginx -g 'daemon off;' \n\
stdout_logfile=/dev/stdout \n\
stdout_logfile_maxbytes=0 \n\
stderr_logfile=/dev/stderr \n\
stderr_logfile_maxbytes=0 \n\
\n\
[program:php-fpm] \n\
command=docker-php-entrypoint php-fpm \n\
stdout_logfile=/dev/stdout \n\
stdout_logfile_maxbytes=0 \n\
stderr_logfile=/dev/stderr \n\
stderr_logfile_maxbytes=0" > /etc/supervisor/conf.d/supervisord.conf

# Expose port 80
EXPOSE 80

# Entrypoint script to handle migrations and caching
COPY deployment/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]