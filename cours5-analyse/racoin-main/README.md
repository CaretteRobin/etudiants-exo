# Racoin

Application web de petites annonces maintenue avec:
- PHP 8.3
- Slim 4
- Twig 3
- Eloquent (Illuminate Database) 10
- MariaDB 10.11

## Démarrage rapide (Docker)

### Prérequis
- Docker
- Docker Compose (`docker compose`)

### Lancer l'application
Optionnel: personnaliser les ports/identifiants via `.env`.
```bash
cp .env.example .env
```

Puis lancer:
```bash
docker compose up --build
```

L'application sera disponible sur:
- `http://localhost:8080`

### Arrêter
```bash
docker compose down
```

Pour supprimer aussi le volume de base de données:
```bash
docker compose down -v
```

## Ce que fait l'environnement Docker
- Lance un conteneur PHP 8.3 (`app`)
- Lance une base MariaDB (`db`)
- Installe automatiquement les dépendances Composer au démarrage
- Initialise automatiquement la base avec:
  - `bdd.sql`
  - `insert.sql`
  - `apikey.sql`

## Variables d'environnement utiles
Valeurs par défaut déjà définies dans `docker-compose.yml`.

- `APP_PORT` (défaut: `8080`)
- `MYSQL_PORT` (défaut: `3307`)
- `DB_DATABASE` (défaut: `racoin`)
- `DB_USERNAME` (défaut: `racoin`)
- `DB_PASSWORD` (défaut: `racoin`)
- `DB_ROOT_PASSWORD` (défaut: `root`)

## Lancer sans Docker (optionnel)
Le mode recommandé reste Docker (plus reproductible).

### Prérequis
- PHP 8.2+
- Composer
- MySQL ou MariaDB

### Étapes
1. Installer les dépendances:
```bash
composer install
```
2. Créer la base `racoin` et importer:
```bash
mysql -u root -p racoin < bdd.sql
mysql -u root -p racoin < insert.sql
mysql -u root -p racoin < apikey.sql
```
3. Configurer la connexion DB:
- soit en copiant `config/config.ini.example` vers `config/config.ini`
- soit via variables d'environnement (`DB_HOST`, `DB_DATABASE`, etc.)
4. Démarrer le serveur:
```bash
php -S 0.0.0.0:8080 -t . router.php
```

## Vérification rapide
- Page d'accueil: `http://localhost:8080/`
- Recherche: `http://localhost:8080/search/`
- API annonces: `http://localhost:8080/api/annonces/`
- API catégories: `http://localhost:8080/api/categories/`

## Notes
- `note.md` contient les réponses d'analyse théorique et les actions de maintenance réalisées.
