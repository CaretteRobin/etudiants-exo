# Projet Vide
## Installation

```bash
docker compose run --rm php composer install
```

## Demarage du projet
```bash
docker compose up
```
=> http://localhost:8080

## Initialisation de la base de donnees + Peuplement
```bash
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction
```

## Acces aux vues :
- http://localhost:8080/films
- http://localhost:8080/realisateurs

---

# Note si problemes de docker UNIQUEMENT :
Certain on des probleme de performance/lenteur avec docker, vous pourrez utiliser votre composer/php local en gardant bien en tete que ce n'est pas une bonne pratique.

Sur la configuration docker ; vous verez une ligne "user" pour le service php. Elle sert a preciser quel user ecrira sur la machine hote, par defaut l'identifiant du user et du groupe est 1000.  
Vous trouverez votre valeur avec la commande suivante, et changer si cela est necessaire.
```bash
echo "UID: ${UID}"
```

Il faut respecter ces conditions:
- `php8.2` avec les extension php`CType`, `iconv`, `session`, `simpleXML` et `Tokenizer`. Et bien sur `composer`

```bash
composer install
```

```bash
php -S 0.0.0.0:8080 -t public
```
