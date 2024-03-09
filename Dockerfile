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
    libzip-dev \
    libicu-dev \
    libhiredis-dev \
    g++ \
    supervisor \
    libgmp-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip opcache intl gmp
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Install Soap extension
RUN apt-get update && apt-get install -y \
    libxml2-dev \
    && docker-php-ext-install soap \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_mysql zip pcntl
RUN pecl install redis && docker-php-ext-enable redis

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN rm -rf /var/lib/apt/lists/*

RUN apt-get update && apt-get install -y curl xz-utils \
    && curl -O https://nodejs.org/dist/v16.17.0/node-v16.17.0-linux-x64.tar.xz \
    && tar -xf node-v16.17.0-linux-x64.tar.xz \
    && mv node-v16.17.0-linux-x64 /opt/ \
    && ln -s /opt/node-v16.17.0-linux-x64/bin/node /usr/local/bin/ \
    && ln -s /opt/node-v16.17.0-linux-x64/bin/npm /usr/local/bin/

WORKDIR /var/www/html
COPY . .

RUN npm install

RUN composer install

COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN sed -i 's/;clear_env = no/clear_env = no/' /usr/local/etc/php-fpm.d/www.conf

EXPOSE 9000
CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
