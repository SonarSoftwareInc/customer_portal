# Use the official PHP 8.2 FPM image as the base image
FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    libgmp-dev \
    libsqlite3-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    zlib1g-dev \
    libssl-dev \
    zip \
    unzip

# Step 2: Install PHP extensions
RUN docker-php-ext-install \
    bcmath \
    curl \
    gmp \
    mbstring \
    pdo \
    pdo_mysql \
    pdo_sqlite \
    zip \
    dom \
    opcache

# Step 3: Install Redis and Xdebug via PECL
RUN pecl install \
    redis \
    xdebug \
    && docker-php-ext-enable \
    redis \
    xdebug

# Step 4: Clean up to reduce image size
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Step 5: Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Expose the default PHP-FPM port
EXPOSE 9000

# Command to start PHP-FPM
CMD ["php-fpm"]