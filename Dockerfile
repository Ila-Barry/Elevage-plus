# Dockerfile - Version finale optimisée
FROM php:8.2-apache

# Installation des dépendances système (Ajout de ffmpeg obligatoire pour php-ffmpeg)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm \
    ca-certificates \
    ffmpeg \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Installation des extensions PHP
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configuration d'Apache
RUN a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Copier le script de démarrage en amont
COPY start.sh /start.sh
RUN chmod +x /start.sh

# Copier TOUT le projet
COPY . /var/www/html/

# Forcer l'utilisation de HTTP/1.1 via curl pour tout le build Docker (Règle l'erreur HTTP/2 400 de Render)
ENV CURL_HTTP_VERSION=3

# Installer les dépendances PHP de production proprement
RUN composer install --no-interaction --optimize-autoloader --no-dev --no-scripts --prefer-dist

# Exécuter les scripts post-installation
RUN composer run-script post-autoload-dump

# Configuration des permissions pour Apache et Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Installation et build des assets Node.js (Vite / Mix)
RUN npm install && npm run build

# Optimiser Laravel (Mise en cache de la configuration et des routes)
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan event:cache

# Gestion du cache des vues
RUN if [ -d "/var/www/html/resources/views" ]; then \
        php artisan view:cache || echo "⚠️ View cache skipped"; \
    else \
        echo "⚠️ No views found, skipping view cache"; \
    fi

EXPOSE 80
EXPOSE 8000
CMD ["/start.sh"]