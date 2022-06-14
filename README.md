# Snowtrick 🏂 - Openclassrooms Project
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/be9c8e1b209e4dd1812959215d1124dd)](https://www.codacy.com/gh/Florimond-Jouffroy/Openclassrooms-Project-006/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Florimond-Jouffroy/Openclassrooms-Project-006&amp;utm_campaign=Badge_Grade)

Dans le cadre de la formation « Développeur d'application - PHP / Symfony d’OpenClassroom », j'ai réalisé un site communautaire avec le framework Symfony. le projet Snowtrick permet aux utilisateurs de documenter les différentes figures de snowboard et d'échanger sur celle-ci par le biais de commentaires.

## ⚙️Installation du projet
---
Celon votre système d'exploitation plusieurs serveurs peuvent être installés :
```
- Windows : WAMP (http://www.wampserver.com/)
- MAC : MAMP (https://www.mamp.info/en/mamp/)
- Linux : LAMP (https://doc.ubuntu-fr.org/lamp)
- XAMP (https://www.apachefriends.org/fr/index.html)
```
#### Clonage du projet
---
Vous devez avoir Git d'installer pour pouvoir cloner le projet sur votre machine.
```
GIT (https://git-scm.com/downloads)
```
 Il faudra vous placer dans le répertoire de votre choix puis exécuté la commande suivante :
 ```
 git clone https://github.com/Florimond-Jouffroy/Openclassrooms-Project-006.git
 ```
#### Configuration des variables d'environnement
 ---
Dans votre projet, à la racine, faite une copie du fichier .env en la nommant .env.local
Vous pourrez ensuite y renseigner les identifiants de votre base de données en suivant le modèle ci-dessous.
```
- DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=8
```

#### Installation des dépendance
 ---
 Téléchargez et installez les dépendances back-end
 ```
 composer install
 ```
 Téléchargez et installez les dépendances front-end
 ```
 yarn install
 ```
#### Gestion des assets
 ---
 Vous pouvez générer à l'aide de Webpack vos assets Javascript et CSS avec Yarn en tapant la commande ci-dessous :
 ```
 yarn run
 ```
#### Création de la base de données
 ---
 Créez la base de données de l'application en tapant la commande ci-dessous :
 ```
 - php bin/console doctrine:database:create
 ```
 Puis lancer la migration pour créer les tables dans la base de données :
 ```
 - php bin/console doctrine:migrations:migrate
 ```
#### Lancement du serveur
---
 Vous pouvez lancer le serveur via la commande suivante :
 ```
 symfony server:start
 ```
#### Générer des fausses données
---
 Vous pouvez générer des fausses données grâce la fixture présente dans le projet avec la commande suivante :
```
php bin/console doctrine:fixtures:load
```
Vous trouverez dans le dossier public/uploads les images a copier dans le dossier public/pictures afin que les fixtures soit habiller d'images.
