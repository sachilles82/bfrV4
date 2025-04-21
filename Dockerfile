FROM php:8.2-fpm

# System-Abhängigkeiten installieren
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev

# PHP-Erweiterungen installieren
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip sockets

# Composer installieren
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Neueste RoadRunner-Version installieren (2023.x)
RUN curl -L https://github.com/roadrunner-server/roadrunner/releases/download/v2023.3.12/roadrunner-2023.3.12-linux-amd64.tar.gz | tar xz \
    && mv roadrunner-2023.3.12-linux-amd64/rr /usr/local/bin/rr \
    && chmod +x /usr/local/bin/rr

WORKDIR /var/www/html

# Git-Sicherheitsproblem beheben
RUN git config --global --add safe.directory /var/www/html

COPY . .

RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Storage-Link im Container erstellen
RUN php artisan storage:link || true

EXPOSE 8000

# Start-Skript erstellen für besseres Daemon-Verhalten
RUN echo '#!/bin/sh\nexec php artisan octane:start --server=roadrunner --host=0.0.0.0' > /usr/local/bin/start.sh \
    && chmod +x /usr/local/bin/start.sh

CMD ["/usr/local/bin/start.sh"]
