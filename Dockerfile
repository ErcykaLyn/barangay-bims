FROM php:8.2-fpm-alpine

# ---------- System packages + PHP extensions ----------
# gd = kailangan ng PhpSpreadsheet / Maatwebsite Excel (import & export)
RUN apk add --no-cache nginx nodejs npm bash supervisor git unzip \
        postgresql-dev libzip-dev icu-dev oniguruma-dev \
        freetype-dev libjpeg-turbo-dev libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" pdo_pgsql pdo_mysql zip intl opcache gd

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# ---------- Siguraduhing ang routes/config ay galing sa code na nasa GitHub ----------
# (huwag gamitin ang naka-cache na routes/config na maaaring kasama sa repo)
RUN rm -f bootstrap/cache/*.php

# ---------- PHP dependencies ----------
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --no-scripts \
    && (php artisan package:discover --ansi || true)

# ---------- Frontend build ----------
RUN npm install @tailwindcss/oxide-linux-x64-musl --save-optional
RUN npm install && npm run build && rm -rf node_modules

# ---------- PHP settings (import ng Excel hanggang 20MB) ----------
RUN printf '%s\n' \
        'upload_max_filesize=20M' \
        'post_max_size=25M' \
        'memory_limit=512M' \
        'max_execution_time=300' \
        > /usr/local/etc/php/conf.d/zz-app.ini

# ---------- PHP-FPM pool ----------
RUN printf '%s\n' \
        '[global]' \
        'daemonize = no' \
        'error_log = /proc/self/fd/2' \
        '' \
        '[www]' \
        'listen = 127.0.0.1:9000' \
        'pm = dynamic' \
        'pm.max_children = 5' \
        'pm.start_servers = 2' \
        'pm.min_spare_servers = 1' \
        'pm.max_spare_servers = 3' \
        'catch_workers_output = yes' \
        'decorate_workers_output = no' \
        'clear_env = no' \
        > /usr/local/etc/php-fpm.d/zz-docker.conf

# ---------- Nginx (ang __PORT__ ay pinapalitan ng $PORT ng Render sa startup) ----------
RUN rm -f /etc/nginx/http.d/default.conf \
    && mkdir -p /run/nginx /var/log/nginx \
    && ln -sf /dev/stdout /var/log/nginx/access.log \
    && ln -sf /dev/stderr /var/log/nginx/error.log \
    && printf '%s\n' \
        'server {' \
        '    listen __PORT__;' \
        '    server_name _;' \
        '    root /var/www/html/public;' \
        '    index index.php;' \
        '    client_max_body_size 25M;' \
        '    location / { try_files $uri $uri/ /index.php?$query_string; }' \
        '    location ~ \.php$ {' \
        '        fastcgi_pass 127.0.0.1:9000;' \
        '        fastcgi_index index.php;' \
        '        include fastcgi_params;' \
        '        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;' \
        '        fastcgi_read_timeout 300;' \
        '    }' \
        '    location ~ /\.(?!well-known) { deny all; }' \
        '}' \
        > /etc/nginx/default.conf.template

# ---------- Supervisor ----------
RUN printf '%s\n' \
        '[supervisord]' \
        'nodaemon=true' \
        'user=root' \
        'logfile=/dev/null' \
        'logfile_maxbytes=0' \
        'pidfile=/run/supervisord.pid' \
        '' \
        '[program:php-fpm]' \
        'command=php-fpm -F' \
        'autostart=true' \
        'autorestart=true' \
        'stdout_logfile=/dev/stdout' \
        'stdout_logfile_maxbytes=0' \
        'stderr_logfile=/dev/stderr' \
        'stderr_logfile_maxbytes=0' \
        '' \
        '[program:nginx]' \
        'command=nginx -g "daemon off;"' \
        'autostart=true' \
        'autorestart=true' \
        'stdout_logfile=/dev/stdout' \
        'stdout_logfile_maxbytes=0' \
        'stderr_logfile=/dev/stderr' \
        'stderr_logfile_maxbytes=0' \
        > /etc/supervisord.conf

# ---------- Startup script ----------
RUN printf '%s\n' \
        '#!/bin/sh' \
        'cd /var/www/html || exit 1' \
        '' \
        '# Gamitin ang PORT ng Render (default 10000)' \
        'sed "s/__PORT__/${PORT:-80}/" /etc/nginx/default.conf.template > /etc/nginx/http.d/default.conf' \
        '' \
        '# Laging basahin ang pinakabagong routes/config/views mula sa na-deploy na code' \
        'mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache' \
        'php artisan config:clear || true' \
        'php artisan route:clear || true' \
        'php artisan view:clear || true' \
        'php artisan storage:link || true' \
        '' \
        '# I-set ang RUN_MIGRATIONS=true sa Render environment para mag-migrate tuwing start' \
        'if [ "$RUN_MIGRATIONS" = "true" ]; then' \
        '  echo "Running migrations..."' \
        '  php artisan migrate --force || echo "!!! MIGRATION FAILED !!!"' \
        'fi' \
        '' \
        '# I-set ang RUN_SEEDERS=true sa Render environment para mag-seed tuwing start' \
        'if [ "$RUN_SEEDERS" = "true" ]; then' \
        '  echo "Running database seeders..."' \
        '  php artisan db:seed --force || echo "!!! SEEDER FAILED !!!"' \
        'fi' \
        '' \
        '# Dapat nakasulat ng PHP-FPM worker (www-data) ang storage at bootstrap/cache' \
        'chown -R www-data:www-data storage bootstrap/cache' \
        '' \
        'exec supervisord -c /etc/supervisord.conf' \
        > /start.sh \
    && chmod +x /start.sh

EXPOSE 10000
CMD ["/start.sh"]