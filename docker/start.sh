#!/bin/bash

# Attendre que la base de données soit prête (si nécessaire)
echo "🚀 Démarrage de l'application Laravel..."

# Vérifier si la clé d'application est définie
if [ -z "$APP_KEY" ]; then
    echo "⚠️  APP_KEY non définie, génération d'une nouvelle clé..."
    php artisan key:generate --no-interaction || echo "⚠️  Impossible de générer la clé, utilisation de la clé par défaut"
fi

# Exécuter les migrations si nécessaire
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "📊 Exécution des migrations..."
    php artisan migrate --force || echo "⚠️  Impossible d'exécuter les migrations"
fi

# Optimiser l'application
echo "⚡ Optimisation de l'application..."
php artisan config:cache || echo "⚠️  Impossible de mettre en cache la configuration"
php artisan route:cache || echo "⚠️  Impossible de mettre en cache les routes"
php artisan view:cache || echo "⚠️  Impossible de mettre en cache les vues"

# Démarrer Apache
echo "🌐 Démarrage d'Apache..."
apache2-foreground 