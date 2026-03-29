# 📍 Bonnes Adresses Bénin — Guide d'installation

## Prérequis
- PHP 8.2+
- Composer
- MySQL
- Laragon (recommandé sous Windows)

---

## Installation

### 1. Créer le projet Laravel
```bash
composer create-project laravel/laravel bonnes-adresses
cd bonnes-adresses
```

### 2. Copier les fichiers du MVP
Copier tous les fichiers fournis dans les dossiers correspondants de votre projet.

### 3. Configurer .env
```env
APP_NAME="Bonnes Adresses Bénin"
APP_URL=http://bonnes-adresses.test

DB_DATABASE=bonnes_adresses
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Enregistrer le middleware CheckRole
Dans `bootstrap/app.php` (Laravel 11) :
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\CheckRole::class,
    ]);
})
```

### 5. Créer la base de données et migrer
```bash
# Créer la base dans MySQL
mysql -u root -e "CREATE DATABASE bonnes_adresses;"

# Lancer les migrations
php artisan migrate

# Insérer les données initiales
php artisan db:seed
```

### 6. Créer le lien de stockage (pour les photos)
```bash
php artisan storage:link
```

### 7. Lancer le serveur
```bash
php artisan serve
```

---

## Comptes par défaut (après seed)

| Rôle  | Email                          | Mot de passe  |
|-------|--------------------------------|---------------|
| Admin | admin@bonnesadresses.bj        | Admin@2025!   |

> ⚠️ Changer le mot de passe admin en production !

---

## Accès aux espaces

| Espace        | URL                  |
|---------------|----------------------|
| Site public   | /                    |
| Connexion     | /connexion           |
| Inscription   | /inscription         |
| Propriétaire  | /mon-espace          |
| Admin         | /admin               |

---

## Structure des fichiers

```
app/
  Models/
    Ville.php
    Categorie.php
    User.php
    Etablissement.php
    Photo.php
    Service.php
  Http/
    Controllers/
      VisiteurController.php
      Auth/AuthController.php
      Proprietaire/EtablissementController.php
      Admin/AdminController.php
    Middleware/
      CheckRole.php

database/
  migrations/         ← 5 fichiers de migration
  seeders/
    DatabaseSeeder.php

routes/
  web.php
```

---

## Prochaines étapes (après MVP)

- [ ] Vues Blade (layouts, visitor, proprietaire, admin)
- [ ] Upload multiple de photos
- [ ] Intégration Google Maps
- [ ] PWA (manifest.json + service worker)
- [ ] Déploiement sur Railway.app
