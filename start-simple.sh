#!/bin/bash

echo "🚀 Démarrage simple de l'application Laravel..."

# Vérifier que les fichiers essentiels existent
if [ ! -f "public/index.php" ]; then
    echo "❌ Erreur: public/index.php non trouvé"
    exit 1
fi

if [ ! -f "vendor/autoload.php" ]; then
    echo "❌ Erreur: vendor/autoload.php non trouvé"
    exit 1
fi

# Générer la clé si nécessaire
if [ -z "$APP_KEY" ]; then
    echo "⚠️  Génération de la clé d'application..."
    php artisan key:generate --no-interaction || echo "⚠️  Impossible de générer la clé"
fi

# Démarrer Apache
echo "🌐 Démarrage d'Apache..."
exec apache2-foreground 