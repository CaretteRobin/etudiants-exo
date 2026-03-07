# Notes de maintenance - Racoin

## Fiche d'identification rapide
- **Nom du projet** : Racoin
- **Type d'application** : application web de petites annonces
- **Architecture** : PHP monolithique (front + routes + templates + accès BDD)
- **Entrée principale** : `index.php`

## Partie 1 - Analyse théorique (sans lancer le projet)

### 1) Langages utilisés
- **PHP** (back-end)
- **SQL** (schéma + données initiales)
- **Twig/HTML** (templates)
- **CSS / SCSS**
- **JavaScript** (scripts front)

### 2) Frameworks / libs principaux utilisés
- **Slim 2** (`slim/slim` 2.*)
- **Twig 1** (`twig/twig` ~1.0)
- **Eloquent ORM / Illuminate Database** (`illuminate/database` 4.2.9)

### 3) But général de l'application
- Simuler un site de petites annonces type LeBonCoin:
- lister des annonces,
- afficher le détail,
- filtrer/rechercher,
- ajouter/modifier/supprimer une annonce,
- exposer quelques endpoints API (`/api/...`).

### 4) Première estimation pour démarrer l'application
- Un runtime **PHP compatible legacy**.
- Une base **MySQL/MariaDB** avec les tables et données d'initialisation (`bdd.sql`, `insert.sql`, `apikey.sql`).
- Les dépendances Composer à installer (`vendor/` absent au clone).
- Une configuration DB (`config/config.ini` ou variables d'environnement).
- Un serveur HTTP local (`php -S` peut suffire).
