FROM php:8.5-fpm

RUN apt-get update && apt-get install -y \
    curl \
    git \
    unzip \
    ca-certificates \
    gnupg \
    default-mysql-client \
    && curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-install pdo_mysql pcntl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html

COPY . .

RUN mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    bootstrap/cache

RUN composer install \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --ignore-platform-req=ext-zip \
    --ignore-platform-req=ext-gd \
    --ignore-platform-req=ext-redis

RUN if [ -f package-lock.json ]; then npm ci; else npm install; fi \
    && npm run build

RUN cp -a public /tmp/public

COPY ./scripts/php-fpm-entrypoint /usr/local/bin/php-fpm-entrypoint

RUN chmod +x /usr/local/bin/php-fpm-entrypoint \
    && chown -R www-data:www-data storage bootstrap/cache

ENTRYPOINT ["php-fpm-entrypoint"]

CMD ["php-fpm"]