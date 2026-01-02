# Exemple de linters
Dans ce dossier, vous trouverez des exemples de linters JS et php

## Installation
Pour installer les dépendances, il faut lancer la commande suivante:
```bash
docker compose run --rm node npm install
docker compose run --rm php composer install
```

## ESLint
Pour lancer le linter, il faut lancer la commande suivante:
```bash
docker compose run --rm node npm run lint
```
La configuration du linter se trouve dans le fichier `.eslintrc.js`, et se base sur la configuration de base airbnb.  
La commande exacte d'eslint est dans le fichier `package.json` (partie scripts):
```json
"lint": "eslint . --ext .js "
```

## PHP Code Sniffer
Pour lancer le linter, il faut lancer la commande suivante:
```bash
docker compose run --rm php composer run-script phpcs
```

La configuration du linter se trouve dans le fichier `phpcs.xml`.  

Phpcs propose aussi une commande pour corriger automatiquement certaines erreurs:  
```bash
docker compose run --rm php composer run-script phpcs:fix
```

La commande exacte de phpcs est dans le fichier `composer.json` (partie scripts):
```json
"phpcs": "vendor/bin/phpcs",
"phpcs:fix": "vendor/bin/phpcbf"
```


## PHPStan
Pour lancer le linter, il faut lancer la commande suivante:
```bash
docker compose run --rm php composer run-script phpstan
```

La configuration du linter se trouve dans le fichier `phpstan.neon`.

La commande exacte de phpstan est dans le fichier `composer.json` (partie scripts):
```json
"phpstan": "vendor/bin/phpstan analyse"
```

## Instructions
Utilisez les linters pour corriger les erreurs dans les fichiers php et js dans les dossiers `src` et `js`.
