# Reunionou
Ce projet permet d'organiser des événements depuis son smartphone ou son ordinateur à travers une API afin de gérer toutes les informations (lieu, membres, météo, discussion, ...)

Ce dépôt contient le backoffice (ou **gateway**) développé avec le framework **Slim**.

## Planning:
- Date de debut: **21 Mars 2022**
- Date de fin: **30 Mars 2022**


## Membres du groupe:
- Youssef Bahi (youssef.bahi1@etu.univ-lorraine.fr)
- Malek Ben Khalifa (malek.ben-khalifa3@etu.univ-lorraine.fr)
- Sebastien Klaus (sebastien.klaus2@etu.univ-lorraine.fr)
- Maxime Piscalgia (maxime.piscaglia1@etu.univ-lorraine.fr)
- Armand Perignon (armand.perignon3@etu.univ-lorraine.fr)

## Lien utiles:
- Version web de l'application (Admin): https://reunionou-admin.netlify.app/
- Version web de l'application (client): https://reunionou.netlify.app/
- Documentation : https://reunionou-doc.netlify.app/
- Tableau de bord (Trello) : https://trello.com/b/fEIl2eXh/tableau-de-bord

## Code source :
- Version web de l'application (Admin): https://github.com/sebastienklaus/reunionou-admin-webapp
- Version web de l'application (client): https://github.com/you97ssef/reunionou-web-app
- Version mobile: https://github.com/malekbk98/reunionou-mobile-app
- Backoffice: https://github.com/sebastienklaus/reunionou/tree/develop

## Installation:

```
docker-compose up --no-start
```

```
docker-compose start
```

**Note : dans chaque container de ce réseau docker, il faut excécuter cette commande :**
```
docker exec -it nom_du_container /bin/bash
```
```
composer install
```

## Ports :
- Service d'events : **62010**
- Service d'authentification : **62011**
- Service d'administartion des données (Adminer) : **62013**
- Service de backoffice : **62015** (HTTP) | **62016** (HTTPS)


## Fonctionnalités:
- Fournir les routes du service d'authentification
- Fournir les routes du service d'événements/messages/membres
- Fournir les rbases de données
- Accéder à une interfacde d'administration



## Technologies :
- Backoffice : Vue.js
- Web app : Vue.js
- Mobile app : Flutter
- Backend : Slim
- Database : Adminer + Sql


---

# API end-points

## Backoffice

### Events/Messages/Members Service

- /members/{pseudo}/events : **GET**
- /events/{id} : **GET** - **PUT** - **DELETE**
- /events/ : **GET** - **POST**
- /events/{id}/members : **GET**
- /members/{id} : **GET** - **PUT** - **DELETE**
- /members/ : **POST**
- /messages/{id} : **GET** - **PUT**
- /messages/ : **POST**
- /events/{id}/messages : **GET**
- /users/{id}/members : **GET**

---

## Auth Users Service

- /users/ : **GET** - **POST**
- /users/{id} : **GET** - **PUT**
- /auth/ : **POST**

---

## Admin

- /events/{id} : **DELETE**
