# Bayizone Full Stack Application
FROM php:8.3-apache

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
    nodejs \
    npm \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy backend
COPY bayi-backend/ /var/www/html/

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy frontend applications
COPY bayi-provider-panel/ /var/www/html/public/provider/
COPY bayi-store/ /var/www/html/public/store/
COPY bayi-client-panel/ /var/www/html/public/app/

# Build frontend applications
RUN cd /var/www/html/public/provider && npm install && npm run build
RUN cd /var/www/html/public/store && npm install && npm run build
RUN cd /var/www/html/public/app && npm install && npm run build

# Copy Apache configuration
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# Enable Apache modules
RUN a2enmod rewrite

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Expose port
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"] 