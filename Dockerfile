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

# Copier tous les fichiers de l'application d'abord
COPY . .

# Installer les dépendances PHP (après avoir copié artisan)
RUN composer install --no-dev --optimize-autoloader --no-interaction

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
    ServerName localhost\n\
    \n\
    <Directory /var/www/html/public>\n\
        Options Indexes FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
        \n\
        # Gestion des erreurs Laravel\n\
        ErrorDocument 404 /index.php\n\
        ErrorDocument 500 /index.php\n\
    </Directory>\n\
    \n\
    # Logs\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
    \n\
    # Headers pour Laravel\n\
    Header always set X-Content-Type-Options nosniff\n\
    Header always set X-Frame-Options DENY\n\
    Header always set X-XSS-Protection "1; mode=block"\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Exposer le port
EXPOSE 80

# Script de démarrage
RUN echo '#!/bin/bash\n\
echo "🚀 Démarrage de l'\''application Laravel..."\n\
\n\
# Attendre un peu pour s'\''assurer que tout est prêt\n\
sleep 5\n\
\n\
# Vérifier si la clé d'\''application est définie\n\
if [ -z "$APP_KEY" ]; then\n\
    echo "⚠️  APP_KEY non définie, génération d'\''une nouvelle clé..."\n\
    php artisan key:generate --no-interaction || echo "⚠️  Impossible de générer la clé"\n\
fi\n\
\n\
# Exécuter les migrations si nécessaire\n\
if [ "$RUN_MIGRATIONS" = "true" ]; then\n\
    echo "📊 Exécution des migrations..."\n\
    php artisan migrate --force || echo "⚠️  Impossible d'\''exécuter les migrations"\n\
fi\n\
\n\
# Optimiser l'\''application\n\
echo "⚡ Optimisation de l'\''application..."\n\
php artisan config:cache || echo "⚠️  Impossible de mettre en cache la configuration"\n\
php artisan route:cache || echo "⚠️  Impossible de mettre en cache les routes"\n\
php artisan view:cache || echo "⚠️  Impossible de mettre en cache les vues"\n\
\n\
# Vérifier que le dossier public existe\n\
if [ ! -f "public/index.php" ]; then\n\
    echo "❌ Erreur: public/index.php non trouvé"\n\
    exit 1\n\
fi\n\
\n\
# Démarrer Apache en arrière-plan\n\
echo "🌐 Démarrage d'\''Apache..."\n\
apache2-foreground' > /start.sh && chmod +x /start.sh

CMD ["/start.sh"] 