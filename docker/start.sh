#!/bin/bash

# Attendre que la base de données soit prête (si nécessaire)
echo "🚀 Démarrage de l'application Laravel..."

# Vérifier si la clé d'application est définie
if [ -z "$APP_KEY" ]; then
    echo "⚠️  APP_KEY non définie, génération d'une nouvelle clé..."
    php artisan key:generate --no-interaction
fi

# Exécuter les migrations si nécessaire
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "📊 Exécution des migrations..."
    php artisan migrate --force
fi

# Optimiser l'application
echo "⚡ Optimisation de l'application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Démarrer Apache
echo "🌐 Démarrage d'Apache..."
apache2-foreground 