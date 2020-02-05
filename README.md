# Beer Api 

Beer api est une application de notations de biere.

## Description

Cette application permet a des utilisateurs de rentrer une note pour une biere lorsqu'il la deguste.

Cette api permet aussi de trouver des biere sen fonction de leur degre d'alcool ou d'amertume.

Les specifications se trouvent dans le dossier `doc/spec`.

**Prerequis**
> Docker et docker compose doivent etre installer sur la machine

> Make aussi doit etre installer

Pour lancer l'initialisation de la stack faire un `make serve`, les conteneurs seront construits et démareés.
une fois lander make vendor provisionnera les vendors.

Si on rencontre une erreur durant l'installation de la base de donnée avec le `make serve`, il faut lancer un `make db`

Pour avoir plus d'informations sur les differentes commande disponible avec make juste faire `make`
