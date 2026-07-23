# ============================================
# Stage 1: Build frontend assets
# ============================================
FROM node:20-alpine AS frontend-builder

WORKDIR /app

# Copy package files first for better layer caching
COPY package.json package-lock.json* ./

# Install npm dependencies (with caching)
RUN npm ci --no-audit --no-fund --prefer-offline

# Copy source code
COPY resources/ resources/
COPY vite.config.js ./
COPY public/ public/

# Build assets
RUN npm run build

# ============================================
# Stage 2: PHP application (production)
# ============================================
FROM php:8.4-cli AS production

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    unzip \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set PHP production settings
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && sed -i 's/upload_max_filesize = .*/upload_max_filesize = 20M/' "$PHP_INI_DIR/php.ini" \
    && sed -i 's/post_max_size = .*/post_max_size = 25M/' "$PHP_INI_DIR/php.ini" \
    && sed -i 's/memory_limit = .*/memory_limit = 256M/' "$PHP_INI_DIR/php.ini"

WORKDIR /app

# ============================================
# Stage 3: Install dependencies & optimize
# ============================================

# Copy composer files first for better layer caching
COPY composer.json composer.lock ./

# Install production dependencies (no scripts yet)
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

# Copy application code
COPY . .

# Copy built frontend assets from builder stage
COPY --from=frontend-builder /app/public/build /app/public/build

# ============================================
# Stage 4: Run scripts & optimize
# ============================================

# Create required directories
RUN mkdir -p \
    storage/logs \
    storage/framework/cache/data \
    storage/framework/cache/streams \
    storage/framework/sessions \
    storage/framework/views \
    storage/app/public \
    bootstrap/cache

# Set permissions
RUN chmod -R 775 storage bootstrap/cache

# Run composer post-autoload-dump (package:discover)
RUN composer dump-autoload --optimize --no-dev

# Optimize Laravel
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan event:cache

# Clean up cached configs that need runtime env
RUN rm -f bootstrap/cache/config.php bootstrap/cache/routes.php

EXPOSE 8000

# Startup command
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
