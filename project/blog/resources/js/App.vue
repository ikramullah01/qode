<template>
    <div class="container mt-4">
        <h1 class="mb-4">Laravel + Vue + Pinia + Bootstrap + Redis</h1>

        <nav class="nav nav-pills mb-4">
            <router-link class="nav-link" :class="{ active: isActive('/blogs') }" to="/blogs">Blogs</router-link>
            <router-link v-if="!auth.isAuthenticated" class="nav-link ms-3" :class="{ active: isActive('/login') }" to="/login">Login</router-link>
            <router-link v-if="auth.isAuthenticated" class="nav-link" :class="{ active: isActive('/blogs/create') }"
                to="/blogs/create">
                Create Blog
            </router-link>
            <!-- Logout button, only show if user is logged in -->
            <button v-if="auth.isAuthenticated" class="btn btn-danger ms-3" @click="logoutUser">
                Logout
            </button>
        </nav>

        <router-view></router-view>
    </div>
</template>

<script>
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from "@/store/auth";

export default {
    name: 'App',
    setup() {
        const route = useRoute();
        const router = useRouter();
        const auth = useAuthStore();

        const logoutUser = async () => {
            await auth.logout();
            router.push({ name: "BlogList" });
        };

        const isActive = (path) => route.path === path;

        return { auth, logoutUser, isActive };
    },
};
</script>

<style>
.nav-link.active {
    font-weight: bold;
}
</style>
