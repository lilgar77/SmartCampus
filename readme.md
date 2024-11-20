<h1>Stack de développement Symfony de la SAE3</h1>

--- 
Contenu : 
- [Prérequis](#prérequis)
- [Démarrage](#démarrage)
- [Se connecter au service `sfapp`](#se-connecter-au-service-sfapp)

--- 

## Prérequis

Sur votre machine Linux ou Mac :

- Docker 24 
- Docker Engine sous Linux (ne pas installer Docker Desktop sous Linux)
- Docker Desktop sous Mac
- PHPStorm  
  _Votre email étudiant vous permet de bénéficier d'une licence complète de 12 mois pour tous les produits JetBrains_  

De manière optionnelle, mais fortement recommandée :

- Une [clé SSH](https://forge.iut-larochelle.fr/help/ssh/index#generate-an-ssh-key-pair) active sur votre machine
  (perso) et [ajoutée dans votre compte gitlab](https://forge.iut-larochelle.fr/help/ssh/index#add-an-ssh-key-to-your-gitlab-account) :  
  elle vous permettra de ne pas taper votre mot de passe en permanence.

## Démarrage

⚠️ **Si vous êtes sous Linux**  
> Avant de démarrer la stack, il faut renseigner les variables qui se trouvent dans le fichier `.env` à la racine du dépôt     
> Vous pouvez obtenir l'id de votre user (et de son groupe) en lançant la commande `id -u ${USER}` dans un terminal

Dans un terminal positionné dans le dossier de la stack du projet : 

- démarrer la stack    
```
docker compose up --build
```

- inspecter l'état des services 
```
docker compose ps
```

- pour installer le vendor 
```
composer installer
``` 
## Se connecter au service `sfapp`

Dans un terminal positionné dans le dossier de la stack du projet : 
 
 - on se connecte au conteneur associé su service `sfapp` 
```bash
docker compose exec sfapp bash
```
- après connexion, on doit être dans `/app`, vérifier 
```
pwd 
```

- vérifier l'exécution du service `sfapp`
```
localhost:8000
```
