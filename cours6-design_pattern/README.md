# Cours 6 - Design Patterns

## Groupe
- Noé Franoux
- Robin Carette 

## Contexte
Ce depot contient le rendu du TD de design patterns en PHP, regroupé dans un seul readme au lieu de plusieurs. 

Les exercices realises sont :
- Singleton
- Factory
- Decorator
- Observer
- Builder

## Structure
Le projet est organise par exercice dans le dossier `design-pattern-main` :
- `singleton`
- `factory`
- `decorator`
- `observer`
- `builder`

Chaque dossier contient son propre `composer.json` et peut etre lance separement.

## Prerequis
- PHP 8.2 ou plus recent
- Composer

## Installation
Se placer dans le dossier de l'exercice voulu, puis lancer :

```bash
composer install
```

## Exercices realises

### Singleton
Objectif :
creer une classe `Config` en Singleton pour lire les valeurs du fichier de configuration.

Fichier principal :
- `design-pattern-main/singleton/app/Config.php`

Execution :

```bash
cd design-pattern-main/singleton
composer install
php public/index.php
```

### Factory
Objectif :
creer une interface commune pour plusieurs vehicules et une factory pour les instancier.

Elements ajoutes :
- `VehicleInterface`
- `Bicycle`
- `Car`
- `Truck`
- `VehicleFactory`

Execution :

```bash
cd design-pattern-main/factory
composer install
php public/index.php
```

### Decorator
Objectif :
ajouter des fonctionnalites a un `Laptop` sans modifier directement la classe de base.

Decorateurs ajoutes :
- `GPUDecorator`
- `OLEDScreenDecorator`

Tests :

```bash
cd design-pattern-main/decorator
composer install
composer run-script phpunit
```

### Observer
Objectif :
notifier les utilisateurs qui suivent un groupe lorsqu'une nouvelle date de concert est ajoutee.

Implementation :
- `User` implemente `SplObserver`
- `MusicBand` implemente `SplSubject`

Tests :

```bash
cd design-pattern-main/observer
composer install
composer run-script phpunit
```

### Builder
Objectif :
creer un Query Builder MySQL avec une construction fluide des requetes SQL.

Elements ajoutes :
- `QueryBuilderInterface`
- `MySQLQueryBuilder`

Exemple de requetes :
- `SELECT id, name, email FROM users WHERE active = 1;`
- `SELECT title, author FROM books WHERE category = 'php' AND published = 1;`

Execution :

```bash
cd design-pattern-main/builder
composer install
php public/index.php
```

## Remarques
- Les exercices `decorator` et `observer` sont verifies avec PHPUnit.
- Les exercices `singleton`, `factory` et `builder` sont verifies via les fichiers `public/index.php`.
- Aucun service externe ni base de donnees n'est necessaire pour lancer le projet.
