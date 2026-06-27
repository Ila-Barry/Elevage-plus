# Dockerfile - Version finale
FROM php:8.2-apache

# Installation des dépendances système
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

# Copier TOUT le projet
COPY . /var/www/html/

# Installer les dépendances
RUN composer install --no-interaction --optimize-autoloader --no-dev --no-scripts || \
    (echo "⚠️ Tentative d'installation alternative..." && \
     composer config --global --no-plugins allow-plugins true && \
     composer install --no-interaction --optimize-autoloader --no-dev --no-scripts --ignore-platform-req=php)

# Exécuter les scripts
RUN composer run-script post-autoload-dump

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Node.js
RUN npm install && npm run build

# Optimiser Laravel - ignorer view:cache si les vues n'existent pas
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan event:cache

# ✅ Forcer la création du cache des vues si le dossier existe
RUN if [ -d "/var/www/html/resources/views" ]; then \
        php artisan view:cache || echo "⚠️ View cache skipped"; \
    else \
        echo "⚠️ No views found, skipping view cache"; \
    fi

COPY start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80
EXPOSE 8000

CMD ["/start.sh"]