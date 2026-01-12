# Slim 4 API

API Slim 4 + MySQL, prête à tourner en conteneurs Docker.

## Prérequis
- Docker + Docker Compose
- Ports libres : `SERVER_PORT` (app, défaut 8080) et `MYSQL_PORT` (MySQL, défaut 3306)

## Démarrage rapide
1) Configurer l'environnement (`.env` fourni, adaptez les valeurs si besoin) :
   - `ENV_MODE` : `dev` ou `prod` pour les messages d'erreur
   - `SERVER_PORT`, `MYSQL_*`
   - Variables S3 si vous gérez les images (facultatif)
2) Lancer les conteneurs :
```bash
docker compose up -d --build
```
3) Vérifier que l'API répond :
```bash
curl http://localhost:8080/
# {"message":"Hello World!"}
```

Les données MySQL sont persistées dans `docker/db_data`.

## Dépendances & commandes utiles
- Installer les dépendances PHP (en local) : `composer install`
- Démarrer en local (hors Docker) : `php -S 0.0.0.0:8080 -t public public/index.php`

## Configuration de l'API
Variables attendues (voir `.env.exemple`) :
- `ENV_MODE` : niveau de verbosité des erreurs
- `SERVER_PORT` : port exposé par l'API
- `MYSQL_HOST`, `MYSQL_DATABASE`, `MYSQL_USER`, `MYSQL_PASSWORD`, `MYSQL_PORT`, `MYSQL_ROOT_PASSWORD`
- `S3_ENDPOINT`, `S3_PUBLIC_ENDPOINT`, `S3_BUCKET`, `S3_ACCESS_KEY`, `S3_SECRET_KEY` (optionnel, stockage d'images)

## Routes disponibles
- `GET /` : Hello World!
- `GET /api/property` et `GET /api/option` + routes CRUD associées
