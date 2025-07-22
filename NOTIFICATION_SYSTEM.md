# Système de Notifications en Temps Réel

## Vue d'ensemble

Le système de notifications permet aux utilisateurs de recevoir des alertes en temps réel quand ils reçoivent des messages ou d'autres types de notifications importantes.

## Fonctionnalités

### ✅ Notifications en base de données
- Toutes les notifications sont stockées dans la table `notifications`
- Types de notifications supportés : `message`, `event`, `welcome`
- Statut de lecture (`is_read`) pour suivre les notifications non lues

### ✅ Notifications en temps réel
- Notifications push du navigateur
- Mise à jour automatique des compteurs
- Redirection intelligente selon le type de notification

### ✅ Gestion automatique
- Marquage automatique des notifications comme lues
- Nettoyage automatique des anciennes notifications
- Observateurs pour synchroniser les états

## Structure de la base de données

### Table `notifications`
```sql
CREATE TABLE notifications (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type VARCHAR(50) NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    event_id BIGINT UNSIGNED NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);
```

### Table `messages`
```sql
CREATE TABLE messages (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    sender_id BIGINT UNSIGNED NOT NULL,
    receiver_id BIGINT UNSIGNED NOT NULL,
    content TEXT NOT NULL,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
);
```

## Flux de fonctionnement

### 1. Envoi d'un message
```
Utilisateur A envoie un message → 
Message sauvegardé en base → 
Notification créée en base → 
Événement NewMessage diffusé → 
Notification push affichée à l'utilisateur B
```

### 2. Réception d'une notification
```
Notification reçue → 
Compteur mis à jour → 
Notification push affichée → 
Clic sur notification → 
Redirection vers la page appropriée
```

### 3. Marquage comme lu
```
Utilisateur ouvre conversation → 
Messages marqués comme lus → 
Notifications correspondantes marquées comme lues → 
Compteur mis à jour
```

## Configuration

### Variables d'environnement requises
```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1
```

### Installation
```bash
# Installer les dépendances
composer install
npm install

# Exécuter les migrations
php artisan migrate

# Seeder les données de test (optionnel)
php artisan db:seed --class=NotificationSeeder
```

## Utilisation

### Pour les développeurs

#### Créer une notification
```php
use App\Models\Notification;

Notification::create([
    'title' => 'Titre de la notification',
    'message' => 'Contenu de la notification',
    'type' => 'message', // ou 'event', 'welcome'
    'user_id' => $userId,
    'is_read' => false,
]);
```

#### Diffuser un événement
```php
use App\Events\MessageNotification;

event(new MessageNotification($notification));
```

#### Marquer comme lu
```php
$notification->markAsRead();
```

### Commandes Artisan

#### Nettoyer les anciennes notifications
```bash
php artisan notifications:cleanup --days=30
```

#### Tester le système
```bash
php artisan test --filter=MessageNotificationTest
```

## Événements disponibles

### NewMessage
- **Déclenché** : Quand un nouveau message est envoyé
- **Données** : Message complet avec expéditeur
- **Canal** : `messages.{receiver_id}`

### MessageNotification
- **Déclenché** : Quand une notification de message est créée
- **Données** : Notification avec compteur non lus
- **Canal** : `notifications.{user_id}`

## Sécurité

- ✅ Validation des permissions avant envoi de messages
- ✅ Vérification que l'utilisateur a une carte de visite
- ✅ Canaux privés pour les notifications
- ✅ Protection CSRF sur toutes les routes
- ✅ Validation des données d'entrée

## Performance

- ✅ Index sur les colonnes fréquemment utilisées
- ✅ Nettoyage automatique des anciennes notifications
- ✅ Pagination des notifications
- ✅ Cache des compteurs de notifications

## Dépannage

### Notifications ne s'affichent pas
1. Vérifier la configuration Pusher
2. Vérifier les permissions du navigateur
3. Vérifier les logs Laravel

### Messages non sauvegardés
1. Vérifier la connexion à la base de données
2. Vérifier les permissions d'écriture
3. Vérifier les contraintes de clés étrangères

### Compteurs incorrects
1. Vider le cache : `php artisan cache:clear`
2. Vérifier les observateurs
3. Vérifier les événements

## Tests

Le système inclut des tests automatisés pour vérifier :
- ✅ Création de notifications lors de l'envoi de messages
- ✅ Marquage automatique comme lu
- ✅ Validation des permissions
- ✅ Intégrité des données

Pour exécuter les tests :
```bash
php artisan test --filter=MessageNotificationTest
```

## Support

Pour toute question ou problème :
1. Consulter les logs Laravel (`storage/logs/laravel.log`)
2. Vérifier la configuration Pusher
3. Tester avec les données de test fournies
4. Consulter la documentation Laravel Broadcasting 