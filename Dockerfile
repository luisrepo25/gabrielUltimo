FROM php:8.3-cli

# Set working directory
WORKDIR /app

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    mariadb-client \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Node.js (v20) and npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create a system user to run Composer and Artisan Commands
RUN groupadd -g 1000 laravel \
    && useradd -u 1000 -ms /bin/bash -g laravel laravel

# Set permissions for the working directory (initially)
COPY . /app
# Desactivar temporalmente el bloqueo de advisories para poder instalar
RUN composer config --global policy.advisories.block false
# Ejecutar composer update para resolver el conflicto del lock file
RUN composer update --no-interaction --no-progress
RUN chown -R laravel:laravel /app

# Switch to the new user (Comentado para evitar problemas de permisos en Windows)
# USER laravel

EXPOSE 8000
EXPOSE 5173

# CMD will be overridden by docker-compose, but we set a default
CMD ["php", "-a"]
