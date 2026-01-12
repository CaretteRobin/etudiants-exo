# Immo Client Vue

Frontend Vue 3 + Vite pour l'exercice.

## Prerequis

- Docker + Docker Compose

## Demarrage avec Docker

1. Installer les dependances dans le container :

```bash
docker compose run --rm app npm install
```

2. Demarrer le serveur Vite :

```bash
docker compose up
```

3. Ouvrir l'application :

```
http://localhost:5173
```

4. Arreter les containers :

```bash
docker compose down
```

## Commandes NPM dans le container

Si besoin d'executer des commandes NPM manuellement :

```bash
docker compose run --rm app npm install
docker compose run --rm app npm run dev -- --host 0.0.0.0 --port 5173
```

## Developpement local (optionnel)

```bash
npm install
npm run dev
```
