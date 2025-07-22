# Configuration du Système de Messagerie en Temps Réel

## Vue d'ensemble

Ce système permet aux utilisateurs qui ont créé leur carte de visite de discuter en temps réel avec d'autres utilisateurs qui ont également créé leur carte de visite.

## Fonctionnalités

- ✅ Messagerie en temps réel entre utilisateurs avec cartes de visite
- ✅ Notifications push en temps réel
- ✅ Interface de chat moderne et responsive
- ✅ Indicateurs de lecture des messages
- ✅ Liste des conversations
- ✅ Validation des permissions (seuls les utilisateurs avec cartes de visite peuvent discuter)

## Configuration requise

### 1. Base de données

Assurez-vous que les tables suivantes existent :
- `users` (table standard Laravel)
- `business_cards` (pour les cartes de visite)
- `messages` (pour les messages)

### 2. Configuration Pusher

Pour les notifications en temps réel, configurez Pusher dans votre fichier `.env` :

```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1
```

### 3. Installation des dépendances

```bash
composer install
npm install
```

### 4. Migration de la base de données

```bash
php artisan migrate
```

## Utilisation

### Pour les utilisateurs

1. **Créer une carte de visite** : Les utilisateurs doivent d'abord créer leur carte de visite
2. **Voir les cartes des autres** : Naviguer vers `/business-cards` pour voir les cartes des autres utilisateurs
3. **Commencer une conversation** : Cliquer sur "Discuter" sur une carte de visite
4. **Gérer les conversations** : Accéder à `/messages` pour voir toutes les conversations

### Pour les développeurs

#### Routes disponibles

- `GET /messages` - Liste des conversations
- `GET /messages/{user}` - Conversation avec un utilisateur spécifique
- `POST /messages/{user}` - Envoyer un message
- `GET /messages/{user}/updates` - Mises à jour en temps réel
- `GET /messages/unread/count` - Nombre de messages non lus

#### Middleware

- `has.business.card` - Vérifie que l'utilisateur a une carte de visite

#### Événements

- `App\Events\NewMessage` - Déclenché quand un nouveau message est envoyé

## Structure des fichiers

```
app/
├── Events/
│   └── NewMessage.php
├── Http/
│   ├── Controllers/
│   │   └── MessageController.php
│   └── Middleware/
│       └── HasBusinessCard.php
├── Models/
│   ├── Message.php
│   ├── User.php
│   └── BusinessCard.php
resources/
└── views/
    ├── messages/
    │   ├── index.blade.php
    │   └── show.blade.php
    └── business-cards/
        └── show.blade.php
routes/
├── web.php
└── channels.php
```

## Sécurité

- Seuls les utilisateurs authentifiés avec une carte de visite peuvent envoyer des messages
- Les utilisateurs ne peuvent pas s'envoyer des messages à eux-mêmes
- Les messages sont privés entre les deux utilisateurs
- Validation des permissions via middleware

## Personnalisation

### Modifier l'interface

Les vues se trouvent dans `resources/views/messages/` :
- `index.blade.php` - Liste des conversations
- `show.blade.php` - Interface de chat

### Modifier le style

Ajoutez vos styles CSS dans les sections `<style>` des vues ou créez des fichiers CSS séparés.

### Modifier la logique

- Contrôleur : `app/Http/Controllers/MessageController.php`
- Modèle : `app/Models/Message.php`
- Middleware : `app/Http/Middleware/HasBusinessCard.php`

## Dépannage

### Messages ne s'affichent pas en temps réel

1. Vérifiez la configuration Pusher
2. Assurez-vous que `BROADCAST_DRIVER=pusher` dans `.env`
3. Vérifiez les logs Laravel pour les erreurs

### Erreur "Vous devez avoir une carte de visite"

L'utilisateur doit créer une carte de visite avant de pouvoir utiliser la messagerie.

### Erreur de connexion à la base de données

Vérifiez votre configuration de base de données dans `.env` et assurez-vous que le serveur MySQL/MariaDB est en cours d'exécution.

## Support

Pour toute question ou problème, consultez la documentation Laravel ou créez une issue dans le repository. 