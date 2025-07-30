#!/bin/bash

# 🚀 Script de déploiement Laravel sur Railway
echo "🚀 Déploiement Laravel sur Railway..."

# 1. Vérifier que tous les fichiers sont commités
echo "📝 Vérification du statut Git..."
if [ -n "$(git status --porcelain)" ]; then
    echo "❌ Il y a des fichiers non commités. Veuillez les commiter d'abord."
    git status
    exit 1
fi

# 2. Pousser vers GitHub
echo "📤 Push vers GitHub..."
git push origin main

echo "✅ Code poussé vers GitHub !"
echo ""
echo "🔧 Étapes suivantes sur Railway :"
echo "1. Allez sur https://railway.app"
echo "2. Créez un nouveau projet"
echo "3. Connectez votre repository GitHub"
echo "4. Configurez les variables d'environnement :"
echo ""
echo "APP_NAME=\"Gestion Événements\""
echo "APP_ENV=production"
echo "APP_KEY=base64:cHHYlksbMa/fHUAzNmJJy1MwtJONMBqVLyU5ouUGasw="
echo "APP_DEBUG=false"
echo "APP_URL=https://votre-app.railway.app"
echo ""
echo "5. Ajoutez une base de données MySQL"
echo "6. Copiez les variables DB_* dans vos variables d'environnement"
echo ""
echo "🌐 Votre app sera accessible sur : https://votre-app.railway.app" 