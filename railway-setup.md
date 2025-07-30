# 🚀 Guide de déploiement Laravel sur Railway

## 📋 Prérequis

1. **Compte GitHub** avec votre code Laravel
2. **Compte Railway** (https://railway.app)
3. **Base de données** (MySQL/PostgreSQL)

## 🔧 Configuration de l'application

### 1. Variables d'environnement à configurer sur Railway :

```env
APP_NAME="Gestion Événements"
APP_ENV=production
APP_KEY=base64:VOTRE_CLE_GENEREE
APP_DEBUG=false
APP_URL=https://votre-app.railway.app

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=YOUR_DB_HOST
DB_PORT=3306
DB_DATABASE=YOUR_DB_NAME
DB_USERNAME=YOUR_DB_USER
DB_PASSWORD=YOUR_DB_PASSWORD

CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

## 🚀 Étapes de déploiement

### 1. Connecter votre repository GitHub

1. Allez sur https://railway.app
2. Cliquez sur "New Project"
3. Sélectionnez "Deploy from GitHub repo"
4. Choisissez votre repository Laravel

### 2. Configurer les variables d'environnement

1. Dans votre projet Railway, allez dans "Variables"
2. Ajoutez toutes les variables d'environnement ci-dessus
3. **Important** : Générez une nouvelle APP_KEY avec :
   ```bash
   php artisan key:generate --show
   ```

### 3. Configurer la base de données

1. Dans Railway, cliquez sur "New Service"
2. Sélectionnez "Database" → "MySQL"
3. Copiez les variables de connexion DB_* dans vos variables d'environnement

### 4. Déployer l'application

1. Railway détectera automatiquement que c'est une app Laravel
2. Le déploiement se fera automatiquement
3. Vérifiez les logs pour s'assurer que tout fonctionne

## 🔄 Commandes de déploiement

Railway exécutera automatiquement ces commandes :

```bash
# Installation des dépendances
composer install --no-dev --optimize-autoloader

# Génération de la clé d'application (si pas définie)
php artisan key:generate

# Optimisation pour la production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Migration de la base de données
php artisan migrate --force

# Démarrage du serveur
php artisan serve --host=0.0.0.0 --port=$PORT
```

## 📁 Fichiers de configuration créés

- `Procfile` : Configuration du serveur web
- `railway.json` : Configuration Railway spécifique

## 🛠️ Résolution des problèmes

### Problème de permissions storage
```bash
chmod -R 775 storage bootstrap/cache
```

### Problème de base de données
- Vérifiez que les variables DB_* sont correctes
- Testez la connexion depuis les logs Railway

### Problème de cache
```bash
php artisan config:clear
php artisan cache:clear
```

## 🌐 URLs de votre application

Une fois déployée, votre app sera accessible sur :
- **Production** : https://votre-app.railway.app
- **Preview** : https://votre-app-preview.railway.app (pour les PR)

## 📊 Monitoring

- **Logs** : Disponibles dans l'interface Railway
- **Métriques** : CPU, RAM, requêtes
- **Health checks** : Vérification automatique de la santé de l'app

## 🔒 Sécurité

- Toutes les variables sensibles dans les variables d'environnement
- APP_DEBUG=false en production
- Logs d'erreur activés pour le debugging 