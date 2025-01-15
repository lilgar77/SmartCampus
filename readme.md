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
- déplacez vous ensuite dans le dossier `/sfapp`, avec la commande :
```
cd sfapp 
```

- pour installer le `/vendor` 
```
composer installer
``` 

- Créer un fichier `.env.local` et rentrez : 
```php
API_USERNAME=l1eq1
API_USERPASS=dicvex-Zofsip-4juqru
```

- vérifier l'exécution du service `sfapp` en rentrant l'URL
```
localhost:8000
```

## Configurer l'environnement de test `PhpUnit`

Après avoir configuré la connexion au service `sfapp`, suivez les étapes ci-dessous :

1. Ajouter une variable d'environnement dans `phpunit.php` :
```php
putenv('APP_ENV=test'); 
```

2.	Configurer la base de données de test dans `.env.test` :
```php
DATABASE_URL="mysql://root:rdbsfapp@database:3306/dbsfapp?serverVersion=10.10.2-MariaDB&charset=utf8mb4"
```

3.Créer la base de données de test :
```php
php bin/console doctrine:database:create --env=test
```

4.	Exécuter les migrations sur la base de test :
```php

php bin/console doctrine:migrations:migrate --env=test
```

5. Créer un fichier `env.test.local` et rentrez : 
```php
API_USERNAME=l1eq1
API_USERPASS=dicvex-Zofsip-4juqru
```

6.	Lancer les tests :
```php
php bin/phpunit --testdox
```



