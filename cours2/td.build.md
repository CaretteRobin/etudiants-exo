# Outils de build javascript
Vous en avez sans doute déjà utilisé, mais les outils de build JavaScript sont devenus incontournables dans le développement web. Nous allons voir rapidement ici l’utilisation de Vite, un outil de build récent et très performant.  
Nous l’utiliserons pour du JavaScript vanilla et du SCSS.

## Vite  
[Liens vers la documentation](https://vitejs.dev/)

Installer Vite est très simple, il suffit d’installer le package npm :  
```bash
npm install vite --save-dev
```

Il ne nécessite quasiment aucune configuration si vous travaillez uniquement pour le front. Si vous voulez le faire marcher avec un projet PHP existant en développement, il est plus simple de modifier légèrement le fichier de configuration :  
```js
import { defineConfig } from 'vite'

export default defineConfig({
    root: './assets',
    base: '/build/',
    server: {
        port: 3000
    },
    build: {
        outDir: '../public/build',
    }
})
```

Il faut ensuite modifier le fichier `package.json` pour ajouter les commandes suivantes :  
```json
{
    "scripts": {
        "dev": "vite",
        "build": "vite build"
    }
}
```

Le script `dev` permet de lancer le serveur de développement qui met à jour les fichiers automatiquement, et `build` génère les fichiers pour la production.

Dans votre fichier HTML, ajoutez ensuite ces balises :  
```html
<script type="module" src="http://localhost:3000/build/@vite/client"></script>
<script type="module" src="http://localhost:3000/build/app.js"></script>
```

Cette configuration est pour le mode développement. En production, il faudra modifier les liens pour qu’ils pointent vers le dossier de build. Le plus simple est de gérer cela automatiquement via des variables d’environnement.


## Utilisation du manifest.json  
Pour une utilisation plus avancée, consultez la documentation concernant l’utilisation du fichier `manifest.json` : [la documentation](https://vitejs.dev/guide/backend-integration).

L’intérêt est de pouvoir disposer d’un nom de fichier dynamique et d’un hash pour la gestion du cache. Le fichier `manifest.json` contient les noms des fichiers générés par Vite, par exemple :  
```json
{
    "index.js": "/build/index.123456.js",
    "index.css": "/build/index.123456.css"
}
```

Modifiez le fichier `vite.config.js` en ajoutant :  
```js
import { defineConfig } from 'vite'

export default defineConfig({
    root: './assets',
    base: '/build/',
    server: {
        port: 3000
    },
    build: {
        manifest: true,
        outDir: '../public/build',
    }
})
```

Il faudra ensuite lire le fichier `manifest.json` dans votre application pour récupérer les noms des fichiers. La balise devra contenir une variable, à adapter selon votre moteur de template. Exemple avec Twig :  
```html
<script type="module" src="http://localhost:3000/build/@vite/client"></script>
<script type="module" src="http://localhost:3000/build/{{ manifest['index.js'] }}"></script>
```

Je vous recommande d’utiliser une fonction qui génère automatiquement les liens en fonction du contenu du `manifest.json` et de l’environnement actuel (dev ou prod).
