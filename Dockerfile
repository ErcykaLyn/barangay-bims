FROM richarvey/nginx-php-fpm:3.1.6

COPY . .

ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr
ENV COMPOSER_ALLOW_SUPERUSER 1

# I-install ang Node.js at i-build ang Vite/Tailwind assets
RUN apk add --no-cache nodejs-current npm
RUN npm install @tailwindcss/oxide-linux-x64-musl --save-optional
RUN npm install && npm run build

# MAGIC FIX: Ituro ang Nginx sa public folder (isa lang sa Alpine)
RUN sed -i 's|/var/www/html;|/var/www/html/public;|g' /etc/nginx/http.d/default.conf

CMD ["/start.sh"]