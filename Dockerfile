FROM php:8.2-fpm-alpine

# 1. Install system packages and PHP extensions
RUN apk add --no-cache nginx nodejs npm bash supervisor postgresql-dev libzip-dev icu-dev oniguruma-dev \
    && docker-php-ext-install pdo_pgsql pdo_mysql zip intl opcache

# 2. Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 3. Set working directory
WORKDIR /var/www/html
COPY . .

# 4. Build frontend assets
RUN npm install @tailwindcss/oxide-linux-x64-musl --save-optional
RUN npm install && npm run build

# 5. Setup Nginx
RUN rm -f /etc/nginx/http.d/default.conf
RUN echo 'server { \
    listen 80; \
    root /var/www/html/public; \
    index index.php index.html; \
    location / { try_files $uri $uri/ /index.php?$query_string; } \
    location ~ \.php$ { \
        fastcgi_pass 127.0.0.1:9000; \
        fastcgi_index index.php; \
        include fastcgi_params; \
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; \
    } \
}' > /etc/nginx/http.d/default.conf

# 6. Setup PHP-FPM
RUN echo '[global]' > /usr/local/etc/php-fpm.conf \
    && echo 'error_log = /proc/self/fd/2' >> /usr/local/etc/php-fpm.conf \
    && echo '[www]' > /usr/local/etc/php-fpm.d/zz-docker.conf \
    && echo 'user = root' >> /usr/local/etc/php-fpm.d/zz-docker.conf \
    && echo 'listen = 9000' >> /usr/local/etc/php-fpm.d/zz-docker.conf \
    && echo 'pm = dynamic' >> /usr/local/etc/php-fpm.d/zz-docker.conf \
    && echo 'pm.max_children = 5' >> /usr/local/etc/php-fpm.d/zz-docker.conf \
    && echo 'pm.start_servers = 2' >> /usr/local/etc/php-fpm.d/zz-docker.conf \
    && echo 'pm.min_spare_servers = 1' >> /usr/local/etc/php-fpm.d/zz-docker.conf \
    && echo 'pm.max_spare_servers = 3' >> /usr/local/etc/php-fpm.d/zz-docker.conf \
    && echo 'catch_workers_output = yes' >> /usr/local/etc/php-fpm.d/zz-docker.conf

# 7. Setup Supervisor (FIXED: idinagdag ang files path)
RUN mkdir -p /etc/supervisor.d && echo '[supervisord]' > /etc/supervisord.conf \
    && echo 'nodaemon=true' >> /etc/supervisord.conf \
    && echo 'user=root' >> /etc/supervisord.conf \
    && echo 'files = /etc/supervisor.d/*.ini' >> /etc/supervisord.conf \
    && echo '[program:php-fpm]' > /etc/supervisor.d/php-fpm.ini \
    && echo 'command=php-fpm -F' >> /etc/supervisor.d/php-fpm.ini \
    && echo 'autostart=true' >> /etc/supervisor.d/php-fpm.ini \
    && echo 'autorestart=true' >> /etc/supervisor.d/php-fpm.ini \
    && echo '[program:nginx]' > /etc/supervisor.d/nginx.ini \
    && echo 'command=nginx -g "daemon off;"' >> /etc/supervisor.d/nginx.ini \
    && echo 'autostart=true' >> /etc/supervisor.d/nginx.ini \
    && echo 'autorestart=true' >> /etc/supervisor.d/nginx.ini

EXPOSE 80

CMD ["supervisord", "-c", "/etc/supervisord.conf"]