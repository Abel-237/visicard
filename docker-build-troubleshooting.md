# 🔧 Dépannage des erreurs de build Docker sur Railway

## ❌ Erreur : "Failed to build an image"

### 🔍 Causes possibles :

1. **Fichiers manquants** dans le repository
2. **Permissions** sur les fichiers
3. **Dépendances** PHP manquantes
4. **Configuration Apache** incorrecte
5. **Script de démarrage** avec erreurs

## ✅ Solution implémentée

### **Dockerfile simplifié et robuste :**

```dockerfile
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

# Créer la configuration Apache inline
RUN echo '<VirtualHost *:80>...' > /etc/apache2/sites-available/000-default.conf

# Script de démarrage inline
RUN echo '#!/bin/bash...' > /start.sh && chmod +x /start.sh

CMD ["/start.sh"]
```

## 🚀 Étapes de redéploiement

1. **Pousser les changements :**
   ```bash
   git add .
   git commit -m "Simplify Dockerfile for Railway build"
   git push origin main
   ```

2. **Sur Railway :**
   - Le redéploiement se fera automatiquement
   - Vérifiez les logs de build

## 📊 Vérification des logs

### **Logs de build réussis :**
```
Step 1/10 : FROM php:8.2-apache
Step 2/10 : RUN apt-get update && apt-get install -y...
Step 3/10 : COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
...
Successfully built [image-id]
```

### **Logs de démarrage réussis :**
```
🚀 Démarrage de l'application Laravel...
⚡ Optimisation de l'application...
🌐 Démarrage d'Apache...
```

## 🔧 Variables d'environnement essentielles

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

## 🆘 En cas d'échec persistant

### **1. Vérifiez les fichiers requis :**
- ✅ `composer.json` et `composer.lock` présents
- ✅ `public/index.php` présent
- ✅ `.env.example` présent

### **2. Testez localement :**
```bash
docker build -t laravel-app .
docker run -p 8000:80 laravel-app
```

### **3. Vérifiez les permissions :**
```bash
chmod +x docker/start.sh
```

### **4. Contactez le support Railway :**
- Fournissez les logs de build complets
- Mentionnez que vous utilisez Docker au lieu de Nixpacks

## ✅ Avantages de cette solution

- **Dockerfile autonome** sans dépendances externes
- **Configuration Apache inline** pour éviter les erreurs de copie
- **Script de démarrage inline** pour éviter les problèmes de permissions
- **Gestion d'erreurs robuste** dans le script de démarrage
- **Build plus simple** et moins susceptible d'échec 