// router/index.js
import { createRouter, createWebHashHistory } from "vue-router";
import Home from "../views/Home.vue";
import About from "../views/About.vue";
import BlogList from "../views/BlogList.vue";
import BlogDetail from "../views/BlogDetail.vue";
import Login from "../views/Login.vue";
import TwoFA from "../views/TwoFA.vue";
import CreateBlog from "@/views/CreateBlog.vue";
import { useAuthStore } from "../store/auth";

const routes = [
    { path: "/", name: "Home", component: BlogList },
    { path: "/about", name: "About", component: About },
    { path: "/blogs", name: "BlogList", component: BlogList },
    {
        path: "/blogs/:id",
        name: "BlogDetail",
        component: BlogDetail,
        props: true,
    },
    { path: "/login", name: "Login", component: Login },
    { path: "/verify-otp", name: "TwoFA", component: TwoFA },
    {
        path: "/blogs/create",
        name: "CreateBlog",
        component: CreateBlog,
        meta: { requiresAuth: true }, // optional: check auth before entering
    },
];

const router = createRouter({
    history: createWebHashHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const auth = useAuthStore();
    if (to.meta.requiresAuth && !auth.isAuthenticated) {
        next({ name: "Login" });
    } else {
        next();
    }
});

export default router;
