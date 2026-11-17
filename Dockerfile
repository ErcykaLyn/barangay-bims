FROM richarvey/nginx-php-fpm:3.1.6

# Kopyahin lahat ng files ng project
COPY . .

# Environment variables para sa Laravel at Nginx
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr
ENV COMPOSER_ALLOW_SUPERUSER 1

# I-install ang Node.js 20 (required ng Vite 7)
RUN apk add --no-cache nodejs-current npm

# I-force install ang tamang Tailwind native binding para sa Alpine Linux
RUN npm install @tailwindcss/oxide-linux-x64-musl --save-optional

# I-compile ang Vite/Tailwind assets
RUN npm install && npm run build

# Simulan ang server
CMD ["/start.sh"]