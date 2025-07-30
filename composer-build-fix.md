# 🔧 Correction de l'erreur Composer dans le build Docker

## ❌ Erreur identifiée :
```
Could not open input file: artisan
Script @php artisan package:discover --ansi handling the post-autoload-dump event returned with error code 1
```

## 🔍 Cause du problème :
- Composer essaie d'exécuter `php artisan package:discover` pendant l'installation
- Le fichier `artisan` n'est pas encore copié dans le conteneur
- L'ordre des opérations COPY dans le Dockerfile était incorrect

## ✅ Solution implémentée :

### **Dockerfile corrigé :**

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

# Copier tous les fichiers de l'application D'ABORD
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

# Créer la configuration Apache inline
RUN echo '<VirtualHost *:80>...' > /etc/apache2/sites-available/000-default.conf

# Script de démarrage inline
RUN echo '#!/bin/bash...' > /start.sh && chmod +x /start.sh

CMD ["/start.sh"]
```

## 🔄 Changements apportés :

### **1. Ordre des opérations COPY :**
- **Avant** : `COPY composer.json composer.lock ./` puis `composer install` puis `COPY . .`
- **Après** : `COPY . .` puis `composer install`

### **2. Fichiers exclus du build :**
- Ajout de `docker/` dans `.dockerignore`
- Exclusion des fichiers de documentation Railway
- Réduction de la taille de l'image

## 🚀 Étapes de redéploiement :

1. **Pousser les changements :**
   ```bash
   git add .
   git commit -m "Fix Composer artisan error in Docker build"
   git push origin main
   ```

2. **Sur Railway :**
   - Le redéploiement se fera automatiquement
   - Vérifiez les logs de build

## 📊 Vérification des logs :

### **Logs de build réussis :**
```
Step 1/10 : FROM php:8.2-apache
Step 2/10 : RUN apt-get update && apt-get install -y...
Step 3/10 : COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
Step 4/10 : WORKDIR /var/www/html
Step 5/10 : COPY . .
Step 6/10 : RUN composer install --no-dev --optimize-autoloader --no-interaction
  - Installing laravel/framework (v8.x.x): Extracting archive
  - Installing laravel/ui (v3.x.x): Extracting archive
  ...
  > Illuminate\Foundation\ComposerScripts::postAutoloadDump
  > @php artisan package:discover --ansi
  ✓ Package manifest generated successfully.
  ...
Successfully built [image-id]
```

## ✅ Avantages de cette solution :

- **Résout l'erreur artisan** en copiant les fichiers dans le bon ordre
- **Build plus rapide** en excluant les fichiers inutiles
- **Image Docker plus légère** avec moins de fichiers
- **Gestion d'erreurs robuste** dans le script de démarrage

## 🆘 En cas de problème persistant :

1. **Vérifiez que le fichier artisan existe :**
   ```bash
   ls -la artisan
   ```

2. **Testez localement :**
   ```bash
   docker build -t laravel-app .
   docker run -p 8000:80 laravel-app
   ```

3. **Vérifiez les permissions :**
   ```bash
   chmod +x artisan
   ```

4. **Consultez les logs complets** dans Railway pour d'autres erreurs 