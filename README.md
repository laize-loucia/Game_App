
# Contexte

Nous avons développé un jeu simple de snake pour enfants de 6 à 10 ans. 
L'interface est épurée et simple avec des boutons de call-to-action bien visibles.
Le but est de ne pas disperser l'attention de l'enfant avec trop d'éléments et de détails.
L'enfant peut jouer au jeux sous le contrôle du parent qui a le mot de passe et l'accès au compte de mail.
**Le mot de passe pour jouer est Snake2026!*


# Structure souhaitée

<center>

![Architecture](Structure.png "TRstrcuture").

</center>


&nbsp;

# Technologies

Le jeu est entièrement écrit en **JS** et tourne dans le navigateur.

&nbsp;
La partie back-end est codée en langage **PHP**, pour les fonctionnalités de gérer le score, vérifier le mot de passe, la connexion, détruire la session et garder en écrit les événements

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

Démarrer le serveur local depuis le dossier projet :
```cd game_app/public```

&nbsp;

Ouvrir dans le navigateur

&nbsp;

```php -S localhost:8000```

ouvre ```http://localhost:8000/index.php``` dans le navigateur


