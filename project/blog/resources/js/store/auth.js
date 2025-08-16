// store/auth.js
import { defineStore } from "pinia";
import axios from "axios";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        user: null,
        token: null,
    }),
    getters: {
        isAuthenticated: (state) => !!state.token,
    },
    actions: {
        async requestLogin(email) {
            await axios.post("/api/otp/request", { email:email });
        },
        async verifyTwoFA(email, otp) {
            const { data } = await axios.post("/api/otp/verify", { email, otp });
            this.user = data.user;
            this.token = data.token;
            axios.defaults.headers.common["Authorization"] = `Bearer ${this.token}`;
        },
        async logout() {
            try {
                await axios.post("/api/logout"); 
            } catch (error) {
                console.error("Logout failed on server:", error);
            }
            this.user = null;
            this.token = null;
            delete axios.defaults.headers.common["Authorization"];
        },
    },
    persist: true,
});
