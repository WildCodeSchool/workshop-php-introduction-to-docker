# Tes premiers pas avec Docker

Travailler avec Docker peut paraître difficile au premier abord. Comme le reste, c'est une compétence qui s'acquiert avec le temps et nous allons voir dans cet atelier les toutes premières marches à gravir pour découvrir ce formidable outil.

## Docker Desktop
1. Installe [Docker Desktop](https://www.docker.com/products/docker-desktop/) sur ta machine.
2. Lance la commande `docker --version` pour vérifier l’installation.  
Tu devrais obtenir quelque chose comme ça :
```bash
Docker version 24.0.6, build ed223bc
```

L'installation de Docker Desktop contient l'interface graphique qui permet de piloter Docker Engine également accessible depuis le terminal.
{: .alert-info :}

Nous allons commencer par utiliser Docker avec le terminal mais il te faut tout de même lancer l'application Docker Desktop pour démarrer Docker Engine.

## Voyage dans le temps
Commence par cloner ce dépôt grâce au lien donné à&nbsp;la&nbsp;section&nbsp;<a href="#input-clone"><i class="bi bi-code-slash"></i>&nbsp;Code ⬆</a>.  
{: .alert-info }

En racine du dossier, démarre un serveur php sur le port 8000 de ton localhost puis [ouvre-le](http://localhost:8000) avec ton navigateur. La version de php installée sur ta machine doit s'afficher grâce au fichier `index.php`.


En consultant le fichier `Dockerfile`, tu constateras que ce projet tourne normalement sur une vieille version de php, la 5.6 (qui date de 2014 tout de même 😎).  
Vas-tu installer cette vieille version sur ta machine ? Non ! En tout cas, pas directement.  
En effet, le fichier `Dockerfile` contient les informations suffisantes pour que Docker puisse s'en occuper.


Éteins ton server php localhost et lance l'application Docker Desktop si ce n'est pas déjà fait. Utilise simplement la commande suivante pour construire une image contenant une version php 5.6 installée avec le serveur web Apache (n'oublie pas le point `.` il est important) :
```bash
docker build -t my-php-app .
```
Lorsque la construction de l'image est terminée, demande à Docker de démarrer un conteneur à partir de cette image grâce à la commande :
```bash
docker run -p 8000:80 my-php-app
```
Grâce à cette commande, le port 8000 de ta machine a été relié au port 80 du conteneur (le serveur web Apache). Tu peux à présent rafraîchir ton navigateur et constater que la version de php a changé 🎉.  

### Volume : persister les modifications
Tu travailles maintenant avec ce conteneur en php 5.6. Seulement si tu modifies le fichier `index.php` et que tu rafraîchis à nouveau ton navigateur, tu te rends compte que la modification n'est pas prise en compte 😥.  
Il faudrait rejouer la première commande (pour reconstruire une image incluant ta modification) puis relancer le conteneur. Pas très pratique si tu dois faire ça sur un vrai projet 🥵.  
C'est là qu'intervient le concept de _volume_.

Éteins ton conteneur qui tournait via ton terminal avec les touches `ctrl + c` et relance-le avec cette commande :
```bash
docker run -v ./:/var/www/html/ -p 8000:80 my-php-app
```
L'option `-v` signifie _volume_ et indique à Docker de reporter toute modification effectuée dans le dossier courant `./` vers `:` le répertoire `/var/www/html/` à l'intérieur du conteneur.

Les modifications sont maintenant prises en compte et visibles à chaque rafraîchissement du navigateur, on peut travailler ! 🥳

Retrouve toutes les commandes et options sur la [documentation de Docker Engine](https://docs.docker.com/engine/).

## Revenons au présent
Nous venons de voir que Docker s'avère très pratique mais que les commandes peuvent devenir assez lourdes avec toutes les options possible (volume, port, etc.). Et si on a besoin de plusieurs services (mysql, phpmyadmin, …) cela peut devenir très très lourd.  
C'est là qu'intervient Docker Compose.

### Docker Compose
Docker Compose est aussi inclus avec Docker Desktop.  
Le concept est simple : réunir au sein d'un fichier yaml tous les services nécessaires à un projet et les faire fonctionner ensemble.  
Un exemple ? Ton projet 3 😀.

Ouvre un terminal en racine de ton projet 3 et lance la commande :
```bash
docker compose up -d
```
Tu te rends compte que la construction est un peu plus longue que tout à l'heure.  
Cette commande va construire les images, installer les outils et exécuter les commandes dont le projet a besoin à partir des fichiers `docker-compose.yml` et `Dockerfile`, à savoir :
- installer un environnement php 8.2 avec composer et les extensions php appropriées et déjà configurées,
- une image mysql,
- installer `node` et `yarn`,
- lancer les commandes `composer install` et `yarn install`,
- mettre à jour la BDD,
- etc.

> ⚠️
Selon les OS ou les configurations des machines, il arrive que Docker retourne une erreur lors du montage de l'image MySQL indiquant le port `3306` est déjà utilisé.  
Si c'est le cas, deux possibilités s'offrent à toi :
- éteindre le service MySQL sur ta machine pour libérer le port 3306 ;
- ou changer le port `3306` en `3307` par exemple dans le fichier `docker-compose.yml` comme ceci :
  ```yaml
  web:
    environment:
        DATABASE_URL: mysql://user:password@database:3307/symfony?serverVersion=8&charset=utf8mb4
        //..
  database:
        ports:
            - "3307:3306"
  ```


Une fois terminée, deux conteneurs sont lancés et tu peux accéder au projet avec ton navigateur comme tu l'as fait précédemment à l'adresse [http://localhost:8000](http://localhost:8000).  
Attention, il faut parfois attendre quelques secondes pour que tout soit réellement terminé et accessible.


### Et ma CLI dans tout ça ?
En phase de développement, tu as souvent besoin de déclencher certaines commandes avec ton terminal (`composer require`, `yarn add`, `php bin/console …`).  
Docker Desktop permet aussi de se connecter à n'importe quel conteneur avec à un terminal embarqué dans l'interface. C'est comme ouvrir la porte du conteneur et y entrer pour travailler.  
Pour faire cela, regarde la fenêtre de Docker Desktop à l'onglet _Containers_, tu devrais voir apparaître ton projet dans la liste des conteneurs actifs, comme dans la capture ci-dessous, avec plusieurs options dont l'accès à un terminal.
![Capture écran de Docker Desktop](docker-desktop.png)

### Démonter les conteneurs et libérer l'espace
Lorsque tu as terminé ton travail, tu peux supprimer tes conteneurs Docker et libérer l'espace disque associé avec la commande :
```bash
docker compose down
```
Ton travail ne sera pas perdu pour autant car tu disposes toujours des fichiers source et il est possible de remonter un environnement à tout moment avec `docker compose up -d`.

# Conclusion
Configurer un environnement avec Docker nécessite des connaissances et de l'expérience. Dans une équipe, c'est souvent le rôle d'un DevOps ou d'un développeur expérimenté.  
Mais il est très facile, même pour un junior, de travailler dans un environnement Docker pré-configuré en utilisant de simples commandes.


Bienvenue à bord !
{: .text-center }
![](docker.gif)
{: .text-center }