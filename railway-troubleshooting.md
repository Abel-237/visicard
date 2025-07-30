# 🔧 Guide de dépannage Railway

## ❌ Problème : Erreur Nixpacks

**Erreur :** `nix-env -if .nixpacks/nixpkgs-...` failed

**Solution :** Utiliser Docker au lieu de Nixpacks
- ✅ Dockerfile créé
- ✅ Configuration Apache ajoutée
- ✅ Script de démarrage configuré

## 🔧 Variables d'environnement essentielles

```env
# Application
APP_NAME="Gestion Événements"
APP_ENV=production
APP_KEY=base64:cHHYlksbMa/fHUAzNmJJy1MwtJONMBqVLyU5ouUGasw=
APP_DEBUG=false
APP_URL=https://votre-app.railway.app

# Base de données (à remplacer par les vraies valeurs Railway)
DB_CONNECTION=mysql
DB_HOST=YOUR_RAILWAY_DB_HOST
DB_PORT=3306
DB_DATABASE=YOUR_RAILWAY_DB_NAME
DB_USERNAME=YOUR_RAILWAY_DB_USER
DB_PASSWORD=YOUR_RAILWAY_DB_PASSWORD

# Logs et cache
LOG_CHANNEL=stack
LOG_LEVEL=error
CACHE_DRIVER=file
FILESYSTEM_DISK=local
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Migrations (optionnel)
RUN_MIGRATIONS=true
```

## 🚀 Étapes de redéploiement

1. **Pousser les changements vers GitHub**
   ```bash
   git add .
   git commit -m "Fix Railway deployment with Docker"
   git push origin main
   ```

2. **Sur Railway :**
   - Le redéploiement se fera automatiquement
   - Vérifiez les logs pour s'assurer que le build Docker fonctionne

## 📊 Vérification des logs

Dans l'interface Railway :
1. Allez dans votre service
2. Cliquez sur "Logs"
3. Vérifiez que vous voyez :
   ```
   🚀 Démarrage de l'application Laravel...
   ⚡ Optimisation de l'application...
   🌐 Démarrage d'Apache...
   ```

## 🔍 Problèmes courants

### Problème de permissions storage
```bash
# Dans les logs Railway, vous devriez voir :
chmod -R 775 storage bootstrap/cache
```

### Problème de base de données
- Vérifiez que les variables DB_* sont correctes
- Testez la connexion depuis les logs

### Problème de cache
- Les caches sont automatiquement régénérés au démarrage

## ✅ Vérification finale

Une fois déployé, testez :
- ✅ Page d'accueil : `https://votre-app.railway.app`
- ✅ Page de connexion : `https://votre-app.railway.app/login`
- ✅ Page d'inscription : `https://votre-app.railway.app/register`
- ✅ Carte de visite : `https://votre-app.railway.app/business-card`

## 🆘 En cas d'échec

1. **Vérifiez les logs Railway** pour les erreurs spécifiques
2. **Testez localement** avec Docker :
   ```bash
   docker build -t laravel-app .
   docker run -p 8000:80 laravel-app
   ```
3. **Contactez le support Railway** si le problème persiste 