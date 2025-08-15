FROM php:8.4-apache

# Enable Apache mods
RUN a2enmod rewrite

# Install system packages + Imagick dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev gnupg \
    libmagickwand-dev --no-install-recommends \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath gd \
    && pecl install imagick \
    && docker-php-ext-enable imagick

# Install Node.js (current LTS or specific version)
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm@latest

# Copy virtual hosts
COPY ./vhosts/*.conf /etc/apache2/sites-available/
RUN a2ensite blog.conf

# Set working directory
WORKDIR /var/www

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Expose port
EXPOSE 8080
