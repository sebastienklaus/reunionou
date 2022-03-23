<template>
    <div class="m-5 box">
        <figure class="has-text-centered">
            <img src="@/assets/images/user.jpg" width="100" alt="img" />
        </figure>
        <h2 class="title is-2 has-text-centered">Login</h2>
        <form class="m-3" @submit.prevent="validation()">
            <div class="field">
                <label for="email">Email</label>
                <input
                    id="email"
                    class="input"
                    type="email"
                    required
                    placeholder="Email"
                />
            </div>
            <div class="field">
                <label for="password">Mot de passe</label>
                <input
                    id="password"
                    class="input"
                    type="password"
                    required
                    placeholder="Mot de passe"
                />
            </div>
            <div class="field">
                <button class="button is-success">Se connecter</button>
            </div>
        </form>
        <div>
            <router-link class="m-3" to="register">Créer compte</router-link>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {};
    },
    methods: {
        validation() {
        },
    },
};
</script>

<style></style>
