# Use PHP 8.2
FROM php:8.2-cli

WORKDIR /var/www/html

# Install dependencies
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

# Install PHP deps
RUN composer install --no-dev --optimize-autoloader

# Ensure SQLite exists
RUN touch database/database.sqlite

# Run migrations during build
# (⚠️ You might want to run these in CMD or entrypoint instead)
RUN php artisan migrate --force

# Build frontend assets
RUN npm install && npm run build

EXPOSE 8080

# Start Laravel
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]
