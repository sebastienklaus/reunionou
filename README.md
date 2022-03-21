# Atelier n°2 - Reunionou

## First Architecture
<p align="center">
<img src="./first_architecture.png" alt="Reunionou structure" width="600"/>
</p>

## Modified Architecture

- Database : MySQL + PhpMyAdmin
- Backoffice : Slim with Server-side rendering
- Backend : Slim
- Web app : Vue.js
- Mobile app : Flutter


# API end-points

## Events/Messages Service

### Client
- /events/{id} : **GET** - **POST** - **PUT** - **DELETE**
- /events/ : **GET**
- /events/{id}/messages : **GET**
- /messages/{id} : **PUT** - **POST** - **DELETE**
- /events/{id}/members : **GET**

### Backoffice
- /All routes in Client
- /messages/{id} : **GET**
- /users/{id} : **GET** - **POST** - **PUT** - **DELETE**
- /users/ : **GET**
- /guests/{id} :  **GET** - **POST** - **PUT** - **DELETE**
- /guests/ : **GET**


## Auth Users Service
- /signin/ : **POST**
- /auth/ : **POST**
- /check/ : **GET**