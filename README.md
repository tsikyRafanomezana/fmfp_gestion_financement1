Cette repository est une application web simple permettant de soumettre des demandes de financement

# Choix technique: Utilisation du framework; Symfony 7.4
  ## Avantage symfony:
	  - Architecture propre (MVC: Model, View, Controller)
	  - Symfony facilite la gestion des validations, erreurs
  ## Améliorations possibles:
	  - Mise en place d’un système d’authentification et de gestion des rôles
	  - Ajout historique changement statut(date changement statut) et l'utilisateur ayant effectué l'action 
	  - Tableau de bord pour voir le nb de demandes par statut
	  - Envoi mail à l'entreprise si le statut de sa demande a été validé ou rejeté

# Installation et exécution du projet

## Prérequis

Assurez-vous d’avoir installé :

- PHP 8.2 minimum
- Composer
- MySQL 
- Git

## Cloner le projet

git clone https://github.com/tsikyRafanomezana/fmfp_gestion_financement1.git
cd fmfp_gestion_financement1


## Installer les dépendances du projet

composer install


## Configurer les variables d’environnement

Copier le fichier `.env` si nécessaire et configurer la connexion à la base de données :

Changer le user et password 
```env 
DATABASE_URL="mysql://user:password@127.0.0.1:3306/gestion_financement"
```

Ou créer un fichier `.env.local` pour surcharger la configuration locale.
Installation base de donnée

## Créer la base de données

```bash
php bin/console doctrine:database:create
```

php bin/console make:migration

## Exécuter les migrations (création table dans bdd)

```bash
php bin/console doctrine:migrations:migrate
```
 
