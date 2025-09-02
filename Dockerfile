# Use official PHP CLI image
FROM php:8.2-cli

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    unzip \
    git \
    zip \
    curl \
    npm \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy project files
COPY . .

# Install PHP dependencies without dev packages and optimize autoloader
RUN composer install --no-dev --optimize-autoloader

# Make sure the SQLite file exists
RUN touch database/database.sqlite

# Run migrations
RUN php artisan migrate --force

# Install Node dependencies and build assets
RUN npm install && npm run build

# Expose the port Render will use
EXPOSE 8080

# Start Laravel built-in server
CMD php -S 0.0.0.0:8080 -t public