# Analyse et maintenance des applications
## Cours 7 - Optimisation

### Objectif
Le projet a ete complete sur les points demandes par le TD:
- optimisation de l'import des cartes
- ajout de logs sur l'API et l'import
- recherche de cartes
- filtres `setCode` et artiste
- pagination du listing
- bonus artiste dans l'import et sur le detail

### Choix techniques
#### Import
- Remplacement du `in_array()` sur la liste des UUID par une table de hachage (`array_fill_keys`) pour passer d'une verification lineaire a un acces en temps constant.
- Ajout d'un import par batch avec `flush()` / `clear()` reguliers pour limiter la croissance du `UnitOfWork` Doctrine.
- Ajout d'un cache applicatif des artistes deja connus (`artistExternalId => id`) pour eviter les requetes repetitives pendant l'import.
- Ajout de l'option `--limit` pour rejouer rapidement des imports sur 10 000 puis 30 000 lignes, comme demande dans le TD.

#### Recherche, filtres et pagination
- Centralisation des filtres dans `CardRepository` avec un `QueryBuilder` partage entre listing, recherche et detail.
- Limitation de la recherche a 20 resultats et declenchement front automatique a partir de 3 caracteres.
- Pagination serveur sur 100 cartes par page pour eviter de charger l'ensemble du catalogue en une seule fois.

#### Artistes
- Ajout d'une relation Doctrine `Card -> Artist`.
- Import des artistes depuis le CSV avec reutilisation des artistes deja importes.
- Exposition des artistes dans le detail d'une carte et comme filtre de recherche / listing.

#### Logs
- Ajout d'un subscriber Symfony sur les routes `/api/*` pour tracer methode, URL, query string, code HTTP et duree.
- Logs dedies sur l'import: debut, fin, duree, compteurs et erreur eventuelle.

### Design patterns / organisation
- Repository pattern: les requetes de recherche, pagination et filtres restent dans les repositories plutot que dans le controller.
- Event Subscriber: la journalisation des appels API est centralisee et ne duplique pas du code dans chaque action.

### Comment la solution a ete trouvee
- Lecture du flux existant backend et frontend pour identifier les goulets d'etranglement.
- Verification statique avec `php -l`, `phpcs`, `phpstan`, `lint:container`, `lint:twig`, `eslint` et `vite build`.
- Correction incrementalement en commencant par le back, puis par l'API, puis par les pages Vue.

### Validations realisees
- `composer run-script phpcs`
- `composer run-script phpstan`
- `php bin/console lint:container`
- `php bin/console lint:twig templates`
- `npm run lint`
- `npm run build`
- `php bin/console debug:router | rg 'api_card_'`

### Limites sur le benchmark d'import
Le TD demande de tester 10 000 puis 30 000 cartes. La logique necessaire est en place (`--limit`, batch import, logs, caches), mais le benchmark complet n'a pas pu etre execute ici pour deux raisons externes au code:
- le projet ne contenait pas initialement le fichier `data/cards.csv`
- le daemon Docker n'etait pas accessible dans l'environnement, donc aucune base MariaDB de test n'a pu etre demarree

Commandes prevues pour mesurer l'import une fois la base disponible:
```bash
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console import:card --limit=10000
php bin/console import:card --limit=30000
```

### Ameliorations possibles
- Ajouter un benchmark automatise avec `Stopwatch` et export CSV des temps.
- Ajouter des tests fonctionnels sur les endpoints de recherche et pagination.
- Envisager une recherche prefixee ou fulltext si le volume depasse largement les 30 000 cartes.
