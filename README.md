
# Snake Game - Projet IHM (février 2026)

Nous avons développé un mini-jeu interactif de snake dans le cadre du projet IHM (Interface Homme-Machine) pour la licence CNAM.     
Le projet combine le gameplay du célèbre jeu "Snake Game" pour enfants de 6 à 10 ans.

L'interface est épurée et simple avec des boutons de call-to-action bien visibles. Le but est de ne pas disperser l'attention de l'enfant avec trop d'éléments et de détails.&nbsp;  

L'enfant peut jouer au jeux sous le contrôle du parent qui a le mot de passe et l'accès au compte de mail.Le pseudo est admin et le mot de passe pour jouer est *1234*


## Fonctionnalités 

- Page de connexion : Authentification utilisateur avec sessions PHP
- Jeu interactif : Mini-jeu de type Flappy Bird
- Système de score : Suivi et affichage des scores
- Page d'accueil : Interface principale du projet
- Écran de fin : Affichage quand le joueur perd

# Structure du projet

<center>

![Architecture](Structure.png "TRstrcuture").

</center>


&nbsp;

En raison d'un délai imposé d'1 semaine, nous n'avons pas eu le temps de développer cette arborescence de fichiers. 


# Technologies

Le jeu est entièrement écrit en **JS** et tourne dans le navigateur.

&nbsp;
La partie back-end est codée en langage **PHP**, pour les fonctionnalités de gérer le score, vérifier le mot de passe, la connexion, détruire la session et garder en écrit les événements

# Installation
Clonez le dépôt
Placez les fichiers dans le répertoire web de votre serveur (htdocs pour XAMPP)
Accédez au projet via votre navigateur


# Implémentation

&nbsp;
Installer PHP en local sur Linux (Ubuntu)
```
sudo apt update
sudo apt install php
```
&nbsp;
Vérifie que ça marche : 
```
php -v.
```

&nbsp;


Ouvrir dans le navigateur ```php -S localhost:8000```


# License
Projet académique - CNAM

