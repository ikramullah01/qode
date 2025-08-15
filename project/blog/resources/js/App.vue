<template>
    <div class="container mt-4">
        <h1 class="mb-4">Laravel + Vue + Pinia + Bootstrap</h1>

        <nav class="nav nav-pills mb-4">
            <router-link class="nav-link" :class="{ active: isActive('/') }" to="/">Home</router-link>
            <router-link class="nav-link" :class="{ active: isActive('/about') }" to="/about">About</router-link>
        </nav>

        <router-view></router-view>
    </div>
</template>

<script>
import { useRoute } from 'vue-router';

export default {
    name: 'App',
    data() {
        return {};
    },
    methods: {
        isActive(path) {
            return this.$route.path === path;
        },
    },
};
</script>

<style>
.nav-link.active {
    font-weight: bold;
}
</style>