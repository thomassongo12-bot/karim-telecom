# Karim Telecom — Site e-commerce PHP

Boutique de téléphones à Douala, Cameroun. PHP + MySQL, sans framework.

## Installation locale (XAMPP)

```bash
# 1. Cloner dans htdocs
git clone https://github.com/thomassongo12-bot/karim-telecom.git C:/xampp/htdocs/Karim_Telecom

# 2. Créer la config DB
cp config/db.example.php config/db.php
# Éditez config/db.php avec vos identifiants

# 3. Importer la base de données dans phpMyAdmin
# Importer : database/karim_telecom.sql
# Puis si nécessaire : database/add_categories_table.sql

# 4. Accéder au site
http://localhost/Karim_Telecom/
```

## Déploiement sur Hostinger

1. Dans Hostinger hPanel → **Git** → connecter ce dépôt
2. Branche : `main`
3. Créer `config/db.php` depuis `config/db.example.php` avec les identifiants Hostinger
4. Dans `config/db.php`, mettre `define('BASE_URL', '');`
5. Créer le dossier `uploads/` manuellement (non versionné)
6. Importer `database/karim_telecom.sql` via phpMyAdmin Hostinger

## Structure

```
/admin          → Interface d'administration
/assets         → CSS, JS
/config         → Configuration DB et site
/database       → Scripts SQL
/images         → Images hero slider
/includes       → Header, footer, icons, tracker
/uploads        → Médias uploadés (non versionné)
index.php       → Accueil
catalogue.php   → Shop / catalogue
produit.php     → Page produit
sitemap.php     → Sitemap XML dynamique
robots.txt      → Directives robots
```

## Sécurité

- Mot de passe admin hashé en bcrypt
- Protection CSRF sur tous les formulaires
- Anti-brute-force (blocage 5 min après 5 tentatives)
- Timeout de session (2h)
- `uploads/` et `config/db.php` exclus du dépôt public
