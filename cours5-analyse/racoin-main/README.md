# Racoin

Application web legacy de petites annonces.

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
- Lance un conteneur PHP (`app`)
- Lance une base MariaDB (`db`)
- Installe automatiquement les dépendances Composer au démarrage
- Initialise automatiquement la base avec:
  - `bdd.sql`
  - `insert.sql`
  - `apikey.sql`

## Variables d'environnement utiles
Valeurs par défaut définies dans `docker-compose.yml`.

- `APP_PORT` (défaut: `8080`)
- `MYSQL_PORT` (défaut: `3307`)
- `DB_DATABASE` (défaut: `racoin`)
- `DB_USERNAME` (défaut: `racoin`)
- `DB_PASSWORD` (défaut: `racoin`)
- `DB_ROOT_PASSWORD` (défaut: `root`)

## Notes
- Le fichier `note.md` contient les réponses de l'analyse et les actions de maintenance.
