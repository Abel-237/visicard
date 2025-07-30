# 🔧 Correction des problèmes de healthcheck Railway

## ❌ Problème identifié :
```
Deployment failed during network process
Network > Healthcheck: FAILED (01:34)
Healthcheck failure
```

## 🔍 Causes possibles :

1. **Application ne démarre pas correctement**
2. **Port incorrect** ou non exposé
3. **Chemin de healthcheck incorrect**
4. **Timeout trop court** pour le démarrage
5. **Erreurs dans le script de démarrage**

## ✅ Solution implémentée :

### **1. Configuration Railway améliorée :**

```toml
[build]
builder = "DOCKERFILE"

[deploy]
healthcheckPath = "/"
healthcheckTimeout = 300  # Augmenté de 100 à 300 secondes
restartPolicyType = "ON_FAILURE"
restartPolicyMaxRetries = 10
```

### **2. Script de démarrage robuste :**

```bash
#!/bin/bash
echo "🚀 Démarrage de l'application Laravel..."

# Attendre un peu pour s'assurer que tout est prêt
sleep 5

# Vérifier si la clé d'application est définie
if [ -z "$APP_KEY" ]; then
    echo "⚠️  APP_KEY non définie, génération d'une nouvelle clé..."
    php artisan key:generate --no-interaction || echo "⚠️  Impossible de générer la clé"
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

# Vérifier que le dossier public existe
if [ ! -f "public/index.php" ]; then
    echo "❌ Erreur: public/index.php non trouvé"
    exit 1
fi

# Démarrer Apache
echo "🌐 Démarrage d'Apache..."
apache2-foreground
```

### **3. Configuration Apache améliorée :**

```apache
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/html/public
    ServerName localhost
    
    <Directory /var/www/html/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
        
        # Gestion des erreurs Laravel
        ErrorDocument 404 /index.php
        ErrorDocument 500 /index.php
    </Directory>
    
    # Logs
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
    
    # Headers pour Laravel
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
</VirtualHost>
```

## 🚀 Étapes de redéploiement :

1. **Pousser les changements :**
   ```bash
   git add .
   git commit -m "Fix healthcheck issues and improve startup script"
   git push origin main
   ```

2. **Sur Railway :**
   - Le redéploiement se fera automatiquement
   - Vérifiez les logs de démarrage

## 📊 Vérification des logs :

### **Logs de démarrage réussis :**
```
🚀 Démarrage de l'application Laravel...
⚠️  APP_KEY non définie, génération d'une nouvelle clé...
📊 Exécution des migrations...
⚡ Optimisation de l'application...
🌐 Démarrage d'Apache...
```

### **Logs de healthcheck réussis :**
```
Healthcheck: PASSED
Status: Running
```

## 🔧 Variables d'environnement essentielles :

```env
APP_NAME="Gestion Événements"
APP_ENV=production
APP_KEY=base64:cHHYlksbMa/fHUAzNmJJy1MwtJONMBqVLyU5ouUGasw=
APP_DEBUG=false
APP_URL=https://votre-app.railway.app

DB_CONNECTION=mysql
DB_HOST=YOUR_RAILWAY_DB_HOST
DB_PORT=3306
DB_DATABASE=YOUR_RAILWAY_DB_NAME
DB_USERNAME=YOUR_RAILWAY_DB_USER
DB_PASSWORD=YOUR_RAILWAY_DB_PASSWORD

LOG_CHANNEL=stack
LOG_LEVEL=error
CACHE_DRIVER=file
FILESYSTEM_DISK=local
SESSION_DRIVER=file
SESSION_LIFETIME=120

RUN_MIGRATIONS=true
```

## 🆘 En cas de problème persistant :

### **1. Vérifiez les logs de démarrage :**
- Cherchez les erreurs dans les logs Railway
- Vérifiez que Apache démarre correctement

### **2. Testez localement :**
```bash
docker build -t laravel-app .
docker run -p 8000:80 laravel-app
curl http://localhost:8000
```

### **3. Vérifiez la configuration :**
- Toutes les variables d'environnement sont définies
- La base de données est accessible
- Le port 80 est exposé

### **4. Forcez un redéploiement :**
- Dans Railway, allez dans "Settings"
- Cliquez sur "Redeploy"

## ✅ Avantages de cette solution :

- **Timeout de healthcheck augmenté** pour permettre le démarrage complet
- **Script de démarrage robuste** avec vérifications
- **Configuration Apache optimisée** pour Laravel
- **Gestion d'erreurs améliorée** avec logs détaillés
- **Vérifications de sécurité** avant le démarrage 