FROM php:8.2-apache

# Set working directory early
WORKDIR /var/www/html

# Install only necessary system packages
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
    libonig-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    zip unzip \
    && rm -rf /var/lib/apt/lists/*


# Install Python + scientific libraries + MySQL connector
RUN apt-get update && apt-get install -y --no-install-recommends \
    python3 \
    python3-pip \
    python3-dev \
    build-essential \
    && pip3 install --no-cache-dir --break-system-packages \
    numpy \
    pandas \
    requests \
    seaborn \
    matplotlib \
    scikit-learn \
    mysql-connector-python \
    && rm -rf /var/lib/apt/lists/*

# Install Node.js and npm for Tailwind CSS
RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    gnupg \
    ca-certificates \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# Configure and install PHP extensions
RUN docker-php-ext-configure gd \
    --with-freetype=/usr/lib/x86_64-linux-gnu/ \
    --with-jpeg=/usr/lib/x86_64-linux-gnu/ \
    && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    bcmath \
    gd

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy composer binary from composer image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy Apache configuration
COPY ./docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Create Laravel directories if they don't exist and set permissions
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache