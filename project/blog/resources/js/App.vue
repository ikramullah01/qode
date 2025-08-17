<template>
    <div class="container mt-4">
        <h1 class="mb-4">Qode Blogs App</h1>
        <h5 class="mb-4">Laravel + Vue + Pinia + Bootstrap + Redis</h5>

        <nav class="nav nav-pills mb-4">
            <router-link class="nav-link" :class="{ active: isActive('/blogs') }" to="/blogs">Blogs</router-link>

            <!-- Show Login and Register only when NOT authenticated -->
            <router-link v-if="!auth.isAuthenticated" class="nav-link ms-3" :class="{ active: isActive('/login') }" to="/login">Login</router-link>

            <router-link v-if="!auth.isAuthenticated" class="nav-link ms-3" :class="{ active: isActive('/register') }"
                to="/register">
                Register
            </router-link>

            <!-- Show Create Blog when authenticated -->
            <router-link v-if="auth.isAuthenticated" class="nav-link" :class="{ active: isActive('/blogs/create') }"
                to="/blogs/create">
                Create Blog
            </router-link>

            <!-- Logout button -->
            <button v-if="auth.isAuthenticated" class="btn btn-danger ms-3" @click="logoutUser" :disabled="loggingOut">
                <span v-if="loggingOut" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true">
                </span>
                Logout
            </button>
        </nav>

        <router-view></router-view>
    </div>
</template>

<script>
import { useAuthStore } from "@/store/auth";

export default {
    name: 'App',
    data() {
        return {
            loggingOut: false
        };
    },
    computed: {
        auth() {
            return useAuthStore();
        }
    },
    methods: {
        isActive(path) {
            return this.$route.path === path;
        },
        async logoutUser() {
            this.loggingOut = true;
            try {
                await this.auth.logout();
                this.$router.push({ name: "BlogList" });
            } finally {
                this.loggingOut = false;
            }
        }
    }
};
</script>

<style>
.nav-link.active {
    font-weight: bold;
}
</style>
