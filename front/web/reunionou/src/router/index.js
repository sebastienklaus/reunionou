import Vue from "vue";
import VueRouter from "vue-router";

// Users Views
import Login from "../views/users/Login";
import Register from "../views/users/Register";
import EditProfile from "../views/users/EditProfile";

// Events Views
import Events from "../views/events/Events";
import CreateEvent from "../views/events/CreateEvent";
import OneEvent from "../views/events/OneEvent";

Vue.use(VueRouter);

const routes = [
    {
        path: "/",
        name: "events",
        component: Events,
    },
    {
        path: "/new-event",
        name: "new-event",
        component: CreateEvent,
    },
    {
        path: "/events/:id",
        name: "one-event",
        component: OneEvent,
    },
    {
        path: "/login",
        name: "login",
        component: Login,
    },
    {
        path: "/register",
        name: "register",
        component: Register,
    },
    {
        path: "/edit-profile",
        name: "edit-profile",
        component: EditProfile,
    },
];

const router = new VueRouter({
    routes,
});

export default router;
