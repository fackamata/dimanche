# Readme

## clone repository

- pour cloner le répertoire :
```
git clone https://github.com/fackamata/terrainSymfony.git
```

- pour installer toutes les dépendances :
```
composer install
```

- configurer son fichier .env : user et password. Pour que doctrine puisse avoir accès à la base de données

- pour créer la base de donnée par rapport aux entitées :

```
bin/console doctrine:schema:update --force 
```

## Autentification et User

### création de l'user avec :  
```
symfony console make:user
```
permet la création d'une entité user avec une propriété unique, ici sur username
On retrouve l'entité User dans /src/Entity/


### création des entité avec : 
```
symfony console make:entity
```
On retrouve les entitées crées dans /src/Entity/

### création de l'authentification :
```
symfony console make:auth
```
Création de la page login dans /templates/security/login.html.twig

Création des fonction de login et logout dans /Controller/AuthController.php

## Formulaire

### création des formulaire avec : 
```
symfony console make:form
```
Créer des formulaires par rapport au entitées

### création du formulaire de registration avec : 
```
symfony console make:registration-form
```
Créer un formulaire d'autentification, qui peut gérer l'envoie de mail pour valider l'autentification
mais également le login dès validation et la redirection dès login

on ajoute ensuite les champs que l'on veut pour le formulaire d'enregistrement dans /src/entity/RegistrationFormType.php
```
symfony console make:form
```
## Enregistrement des images

on ajoute dans les entitées voulu :

```
use App\Interfaces\FilableInterface;

class User implements FilableInterface
{
    public const FILE_DIR = '/upload/user';
...

public function getFileDirectory(): string
{
    return self::FILE_DIR;
}
```

## récupération de l'user

pour pouvoir récupérer l'username de l'user, on ajoute a l'entité user :

```
public function __toString()
    {
        return $this->getUsername();
    }
```

## récupération des types

pour pouvoir récupérer le nom des différent type, on ajoute a l'entité type :

```
public function __toString()
    {
        return $this->getNom();
    }
```

## Automatisation de la date

pour enregistrer automatiquement la date de publication,

on rajoute dans l'entité    

```
public function __construct()
    {
        $this->date = new \DateTime();
    }

```

### création des controller avec : 
```
symfony console make:controller
```
dans le controller on crée les différentes fonction qui on chacune des routes définies et qui renvoie vers la vue donnée.

## Front-end

### CSS

les feuilles de style sont placer dans le fichier /public/css/
les images relative au front-end sont placer dans le fichier /public/img/

### Bootstrap form

```
form_themes: ['bootstrap_5_layout.html.twig']
```
Ajout de cette ligne dans /config/packages/twig.yaml.

Permet d'avoir des formulaire bootstrap dans l'ensemble de l'application

## Autre 

### Role

dans /config/packages/security.yaml

## Optimisation

### compteur de vue

création d'une fonction pour compter le nombre de vue sur une annonce ou un conseil

utilisation double, donc on créer un fonction public dans le dossier Service

### User service

fonction findByUser : pour trouver les annonces, avis, conseil en fonction de l'utilisateur connecté

fonction count : pour retourner le nombre d'annonces, avis, conseil de chaque utilisateur connecté