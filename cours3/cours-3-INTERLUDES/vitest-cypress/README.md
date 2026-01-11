# TP Automatisation du developpement - Test - Vitest / Cypress

Exercice pour le cours d'automatisation du développement sur les tests.

Ce projet contient une application Vue + Vite.

Il y a deux page pour ce projet :

- `Home` : Accueil de l'application
- `Demo` : Contient un counter et un champs texte

## Installation

```sh
docker compose run --rm node npm install
```

### Compile and Hot-Reload for Development

```sh
docker compose run --rm node npm run dev
```

### Compile and Minify for Production

```sh
docker compose run --rm node npm run build
```

### Run Unit Tests with [Vitest](https://vitest.dev/)

```sh
docker compose run --rm node npm run test
```

### Run Unit Tests with [Vitest](https://vitest.dev/) and [Istanbul](https://istanbul.js.org/) for coverage

```sh
docker compose run --rm node npm run test:coverage
```

### Run Unit Tests with [Vitest UI](https://vitest.dev/guide/ui.html)

```sh
docker compose run --rm node npm run test:ui
```

### Lint with [ESLint](https://eslint.org/)

```sh
docker compose run --rm node npm run lint
```

### Format with [Prettier](https://prettier.io/)

```sh
docker compose run --rm node npm run format
```

Docker est utilisé pour simplifier l'installation des dépendances et l'exécution des commandes, mais ici il est facultatif
(si vous avez `node` et `npm` installés localement, vous pouvez exécuter les commandes directement).

## Structure du projet

- **src** : Contient le code source de l'application
  - **assets** : image et css de base pour l'application
  - **component** : Composants Vue
    - **\_\_test\_\_** : Dossier contenant les tests pour les composants
  - **router** : Configuration du router
  - **store** : Définition des stores
    - **\_\_test\_\_** : Dossier contenant les tests pour le store
  - **views** : Dossier contenant les pages de l'application
- **coverage** : Dossier contentant les rapports de test coverage

## Attendu

1. ### Test des composants

   - Ecrire les tests pour les composants `Counter` et `InputText`

2. ### Test du Router

   - Ecrire les tests pour vérifier que le router navigue bien sur les bonnes pages.

3. ### Test du Store

   - Ecrire les tests pour vérifier que les methodes du store sont corrects