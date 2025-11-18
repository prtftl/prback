# Laravel + Nova Dockerfile for Railway

FROM php:8.2-cli-alpine AS builder

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm \
    oniguruma-dev \
    postgresql-dev \
    mysql-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libwebp-dev

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp && \
    docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_pgsql \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy composer files
COPY composer.json composer.lock ./

# Setup Nova authentication from build args
ARG COMPOSER_AUTH
ARG COMPOSER_AUTH_NOVA_USERNAME
ARG COMPOSER_AUTH_NOVA_PASSWORD

# Create auth.json if credentials are provided
RUN if [ -n "$COMPOSER_AUTH" ]; then \
        echo "$COMPOSER_AUTH" > auth.json; \
    elif [ -n "$COMPOSER_AUTH_NOVA_USERNAME" ] && [ -n "$COMPOSER_AUTH_NOVA_PASSWORD" ]; then \
        echo "{\"http-basic\":{\"nova.laravel.com\":{\"username\":\"$COMPOSER_AUTH_NOVA_USERNAME\",\"password\":\"$COMPOSER_AUTH_NOVA_PASSWORD\"}}}" > auth.json; \
    fi

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-interaction --ignore-platform-req=ext-zip --no-dev || \
    (rm -f auth.json && composer install --optimize-autoloader --no-interaction --ignore-platform-req=ext-zip --no-dev)

# Copy package files
COPY package.json package-lock.json* ./

# Install Node dependencies (if package-lock.json exists)
RUN if [ -f package-lock.json ]; then npm ci --only=production; else npm install --only=production || true; fi

# Copy application files
COPY . .

# Build frontend assets
RUN npm run build || true

# Install Nova assets (will be skipped if Nova not installed)
RUN php artisan nova:install || true

# Remove auth.json (security)
RUN rm -f auth.json

# Set permissions
RUN chown -R www-data:www-data /app \
    && chmod -R 755 /app/storage \
    && chmod -R 755 /app/bootstrap/cache

# Production stage
FROM php:8.2-cli-alpine

# Install system dependencies (runtime + build deps for extensions)
RUN apk add --no-cache \
    libpng \
    libzip \
    oniguruma \
    postgresql-libs \
    libpng-dev \
    libzip-dev \
    oniguruma-dev \
    postgresql-dev \
    mysql-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libwebp-dev

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp && \
    docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_pgsql \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# Clean up build dependencies
RUN apk del --no-cache \
    libpng-dev \
    libzip-dev \
    oniguruma-dev \
    postgresql-dev \
    mysql-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libwebp-dev

# Copy application from builder
COPY --from=builder /app /app

# Set working directory
WORKDIR /app

# Set permissions
RUN chown -R www-data:www-data /app \
    && chmod -R 755 /app/storage \
    && chmod -R 755 /app/bootstrap/cache

# Expose port (Railway will set PORT env var)
EXPOSE 8000

# Start Laravel server
# Railway sets PORT environment variable
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
