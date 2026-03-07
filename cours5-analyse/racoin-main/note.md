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

## Actions appliquées pour fiabiliser le démarrage local
- Ajout d'un `docker-compose.yml` complet (app + MariaDB).
- Initialisation automatique de la base via les scripts SQL existants.
- Ajout d'un fallback de config DB via variables d'environnement dans `db/connection.php` (plus de blocage si `config.ini` absent).
- Ajout d'un script d'entrée Docker PHP qui installe Composer automatiquement si nécessaire.
- Ajout de `config/config.ini.example`.
- Correction du schéma SQL: ajout de la table `sous_categorie` manquante.
- Ajustement des données `insert.sql` pour renseigner `id_categorie`.

## Étape 2 - Statut
- Application lancée localement via Docker (`http://localhost:8080`).
- Process de démarrage documenté dans `README.md`.
- `docker-compose.yml` prêt à l'emploi avec base MariaDB initialisée automatiquement.

## Étape 3 - Préparer la maintenance

### Langages / frameworks obsolètes
- **PHP runtime**: `7.4.33` (très ancien, hors cycle moderne de maintenance).
- **Slim**: `2.6.3` (direct) alors que `composer outdated --direct` indique `4.15.1`.
- **Twig**: `1.44.8` (direct) alors que `composer outdated --direct` indique `3.11.3`.
- **Illuminate Database (Eloquent)**: `4.2.9` (direct) alors que `composer outdated --direct` indique `8.83.27`.
- **jQuery**: `1.11.1` (dans `js/jquery-1.11.1.min.js`), branche legacy très ancienne.

### Dépendances non maintenues / obsolètes
- Pas de package marqué `abandoned` par Composer, mais plusieurs dépendances sont legacy.
- `composer audit --no-dev` remonte **3 vulnérabilités**:
  - `nesbot/carbon` `1.39.1` (CVE-2025-22145).
  - `twig/twig` `1.44.8` (CVE-2024-51754).
  - `twig/twig` `1.44.8` (CVE-2024-51755).
- Les branches lockées sont majoritairement anciennes:
  - `illuminate/database` (version `4.2.9`, datée 2014 dans `composer.lock`).
  - `slim/slim` (version `2.6.3`, datée 2017 dans `composer.lock`).

### Todo list maintenance (priorisée)
- `1) Corriger les vulnérabilités Composer (Twig/Carbon)`: Temps **4/10** - Impact **10/10**.
- `2) Migrer Slim 2 vers Slim 4 (routing, middlewares, bootstrap)`: Temps **9/10** - Impact **9/10**.
- `3) Migrer Twig 1 vers Twig 3`: Temps **6/10** - Impact **8/10**.
- `4) Migrer Eloquent/Illuminate 4.2 vers version supportée`: Temps **8/10** - Impact **8/10**.
- `5) Monter le runtime PHP vers une version actuelle (8.2/8.3)`: Temps **7/10** - Impact **9/10**.
- `6) Réactiver une vraie protection CSRF (actuellement commentée)`: Temps **3/10** - Impact **8/10**.
- `7) Renforcer validation/sanitization des entrées + hashing cohérent`: Temps **5/10** - Impact **8/10**.
- `8) Passer le schéma MySQL en InnoDB + clés étrangères`: Temps **5/10** - Impact **7/10**.
- `9) Ajouter une CI minimale (lint PHP + smoke tests + composer audit)`: Temps **4/10** - Impact **7/10**.
- `10) Ajouter des tests fonctionnels sur routes critiques`: Temps **6/10** - Impact **7/10**.

### Données de référence utilisées pour l'étape 3
- `docker compose run --rm --entrypoint composer app outdated --direct`
- `docker compose run --rm --entrypoint composer app audit --no-dev`
- `docker compose run --rm --entrypoint composer app show --locked --format=json`
- `composer.lock` (versions et dates des packages)
