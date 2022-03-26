# Atelier n°2 - Reunionou

Project made by Sébastien Klaus, Armand Pérignon, Malek Ben Khalifa, Youssef Bahi & Maxime Piscaglia

# Summary

- [Architecture](#architecture)
- [API end-points](#api-end-points)
  - [Backoffice](#backoffice)
    - [Events/Messages Service](#eventsmessages-service)
    - [Auth Users Service](#auth-users-service)
  - [Admin](#admin)

---

# Architecture

<p align="center">
<img src="mcd.drawio.png" alt="Reunionou structure" width="600"/>
</p>

- Database : **MySQL + PhpMyAdmin**
- Backoffice : **Vue.js**
- Backend : **Slim**
- Web app : **Vue.js**
- Mobile app : **Flutter**

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
