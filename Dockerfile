# Utiliser l'image PHP officielle avec Apache
FROM php:8.2-apache

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier les fichiers de dépendances
COPY composer.json composer.lock ./

# Installer les dépendances PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copier le reste des fichiers de l'application
COPY . .

# Créer le répertoire storage et définir les permissions
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && mkdir -p storage/logs \
    && mkdir -p bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# Configurer Apache
RUN a2enmod rewrite

# Créer la configuration Apache
RUN echo '<VirtualHost *:80>\n\
    ServerAdmin webmaster@localhost\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Exposer le port
EXPOSE 80

# Script de démarrage
RUN echo '#!/bin/bash\n\
echo "🚀 Démarrage de l'\''application Laravel..."\n\
if [ -z "$APP_KEY" ]; then\n\
    echo "⚠️  APP_KEY non définie, génération d'\''une nouvelle clé..."\n\
    php artisan key:generate --no-interaction || echo "⚠️  Impossible de générer la clé"\n\
fi\n\
if [ "$RUN_MIGRATIONS" = "true" ]; then\n\
    echo "📊 Exécution des migrations..."\n\
    php artisan migrate --force || echo "⚠️  Impossible d'\''exécuter les migrations"\n\
fi\n\
echo "⚡ Optimisation de l'\''application..."\n\
php artisan config:cache || echo "⚠️  Impossible de mettre en cache la configuration"\n\
php artisan route:cache || echo "⚠️  Impossible de mettre en cache les routes"\n\
php artisan view:cache || echo "⚠️  Impossible de mettre en cache les vues"\n\
echo "🌐 Démarrage d'\''Apache..."\n\
apache2-foreground' > /start.sh && chmod +x /start.sh

CMD ["/start.sh"] 