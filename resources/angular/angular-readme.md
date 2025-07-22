# Intégration d'Angular avec Laravel pour le système de gestion d'événements

Ce document explique comment installer et configurer une application Angular pour améliorer l'ergonomie du système de gestion d'événements de l'entreprise actuellement développé avec Laravel.

## Prérequis

- Node.js (v14.0 ou supérieur)
- NPM (v6.0 ou supérieur)
- Angular CLI (v12.0 ou supérieur)
- Laravel API configurée (déjà en place)

## Installation d'Angular

1. Installer Angular CLI globalement (si ce n'est pas déjà fait) :
   ```bash
   npm install -g @angular/cli
   ```

2. Créer une nouvelle application Angular dans un dossier séparé (par exemple `angular-frontend`) :
   ```bash
   ng new angular-frontend --routing --style=scss
   cd angular-frontend
   ```

3. Installer les dépendances utiles :
   ```bash
   npm install @angular/material @angular/cdk @angular/animations
   npm install chart.js ng2-charts
   npm install @auth0/angular-jwt
   ```

## Configuration de l'application Angular

### 1. Configuration des environnements

Modifier le fichier `src/environments/environment.ts` pour pointer vers l'API Laravel :

```typescript
export const environment = {
  production: false,
  apiUrl: 'http://localhost:8000/api'
};
```

Et `src/environments/environment.prod.ts` :

```typescript
export const environment = {
  production: true,
  apiUrl: '/api' // URL relative en production
};
```

### 2. Configuration CORS dans Laravel

Dans le fichier `config/cors.php` de Laravel, assurez-vous que les origines Angular sont autorisées :

```php
return [
    // ...
    'allowed_origins' => ['http://localhost:4200'], // Origine Angular en développement
    'allowed_methods' => ['*'],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true, // Important pour l'authentification
];
```

### 3. Structure proposée de l'application Angular

```
src/
├── app/
│   ├── core/              # Services de base, intercepteurs, guards
│   │   ├── auth/          # Authentification
│   │   ├── http/          # Intercepteurs HTTP
│   │   └── services/      # Services API
│   ├── shared/            # Composants, directives et pipes partagés
│   ├── features/          # Fonctionnalités de l'application
│   │   ├── events/        # Gestion des événements
│   │   ├── admin/         # Module d'administration 
│   │   ├── reports/       # Génération de rapports
│   │   └── user/          # Profil utilisateur
│   └── layouts/           # Composants de mise en page
├── assets/                # Ressources statiques
└── environments/          # Configuration d'environnement
```

## Développement des composants principaux

### 1. Services API

Créer des services pour interagir avec l'API Laravel :

```bash
ng generate service core/services/auth
ng generate service core/services/event
ng generate service core/services/category
ng generate service core/services/report
```

### 2. Composants principaux

```bash
# Layouts
ng generate component layouts/header
ng generate component layouts/footer
ng generate component layouts/sidebar

# Pages principales
ng generate component features/home
ng generate component features/event-list
ng generate component features/event-detail
ng generate component features/event-form

# Administration
ng generate component features/admin/dashboard
ng generate component features/admin/user-management
ng generate component features/admin/report-management

# Authentification
ng generate component core/auth/login
ng generate component core/auth/register
```

## Déploiement

Pour un déploiement intégré avec Laravel :

1. Compiler l'application Angular en mode production :
   ```bash
   ng build --prod
   ```

2. Copier le contenu du dossier `dist/angular-frontend` dans le dossier `public` de Laravel ou configurer un serveur web pour servir les deux applications.

3. Configuration de la redirection dans Laravel pour les routes Angular (dans `routes/web.php`) :
   ```php
   Route::get('/app/{any}', function () {
       return view('angular-app');
   })->where('any', '.*');
   ```

4. Créer une vue `resources/views/angular-app.blade.php` qui inclut les fichiers Angular compilés.

## Fonctionnalités améliorées avec Angular

L'utilisation d'Angular permet d'améliorer significativement l'ergonomie du système :

1. **Interface utilisateur réactive** : Navigation sans rechargement de page
2. **Visualisations interactives** : Graphiques et tableaux de bord dynamiques avec Chart.js
3. **Formulaires améliorés** : Validation côté client, auto-complétion et interface intuitive
4. **Expérience administrateur optimisée** : Tableaux de bord en temps réel avec mises à jour dynamiques
5. **Réutilisation des composants** : Structure modulaire pour une maintenance facilitée
6. **Performance améliorée** : Chargement plus rapide après le chargement initial
7. **Mode hors ligne** : Possibilité d'implémenter des fonctionnalités limitées en mode hors ligne 