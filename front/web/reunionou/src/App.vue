<template>
    <div id="app">
        <Navbar
            :fullname="fullname"
            @deconnect="deconnect"
        />
        <section class="section">
            <div class="container">
                <router-view />
            </div>
        </section>
    </div>
</template>

<style></style>

<script>
import Navbar from "./components/Navbar.vue";

export default {
    data() {
        return {
            fullname: "false",
        };
    },
    components: {
        Navbar,
    },
    mounted() {},
    methods: {
        deconnect() {},
    },
};
</script>
