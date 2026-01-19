# Annuaire d'entreprise

Ce projet est un petit annuaire d'entreprises.  
Il permet de lister plusieurs entreprises, et d'en voir leur bureaux et employées.

## Technologies utilisées
- PHP 8.4
- MariaDB 11
- Slim 4
- Eloquent 11
- Vite 5
- SCSS

## Préréquis pour une installation local
- Docker
- Docker compose
- Git
- Node.js 18+

## Installation local
1) Cloner le projet

2) Copier le fichier .env.example en .env, et l'alimenter 
```bash
cp .env.example .env
```

3) Installer les dépendances  
```bash
docker compose run --rm php composer install
```

4) Installer les dépendances front
```bash
npm install
```

5) Lancer le container  
```bash
docker compose up
```

## Assets front (Vite)
En développement, positionner `ENV=dev` dans `.env`, puis lancer :  
```bash
npm run dev
```

Pour générer les assets de production :  
```bash
npm run build
```
Les fichiers sont générés dans `public/build` et chargés automatiquement via le manifest.

## (re)Créer et alimenter la base de données
Il faut que le container database soit lancé pour effectuer ces commandes.
 
**Supprimer et re-créer la base de données**  
```bash
docker compose exec php bin/console db:create
```

**Alimenter la base de données**  
```bash
docker compose exec php bin/console db:populate 
```

## Structure du projet
- **assets** : Contient les sources front (JS/SCSS) utilisées par Vite.
- **bin** : Contient le script permettant de lancer des commandes. 
- **config** : Contient les fichiers de configuration de l'application.
- **public** : Contient les fichiers accessibles publiquement
    - **assets** : Contient les fichiers statiques (images, fichiers divers).
    - **build** : Contient les assets compilés par Vite.
- **src** : Contient le code source de l'application
    - **Console** : Contient les commandes de l'application
    - **Controller** : Contient les contrôleurs de l'application
    - **Models** : Contient les modèles de l'application
    - **Twig** : Contient les extension Twig de l'application
- **view** : Contient les fichiers .twig de l'application
