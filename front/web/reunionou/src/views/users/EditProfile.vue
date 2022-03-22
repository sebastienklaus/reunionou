<template>
    <div class="m-5 box">
        <figure class="has-text-centered">
            <img src="@/assets/images/user.jpg" width="100" alt="img" />
        </figure>
        <h2 class="title is-2 has-text-centered">Modifier mon profile</h2>
        <form class="m-3" @submit.prevent="validation()">
            <div class="field">
                <label for="fullname">Nom et Prenom</label>
                <input
                    id="fullname"
                    class="input"
                    type="text"
                    required
                    placeholder="Nom et Prenom"
                />
            </div>
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
                <label for="username">Nom d'utilisateur</label>
                <input
                    id="username"
                    class="input"
                    type="text"
                    required
                    placeholder="Nom d'utilisateur"
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
                <button class="button is-danger">Modifer Profile</button>
            </div>
        </form>
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
