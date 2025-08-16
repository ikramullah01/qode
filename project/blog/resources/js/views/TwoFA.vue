<template>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow">
                    <div class="card-body">
                        <h3 class="card-title text-center mb-4">Verify OTP Code</h3>
                        <form @submit.prevent="verifyCode">
                            <div class="mb-3">
                                <label for="code" class="form-label">Enter OTP Code</label>
                                <input v-model="code" type="text" class="form-control" id="code" required />
                            </div>
                            <button 
                                type="submit" 
                                class="btn btn-success w-100" 
                                :disabled="loading"
                            >
                                <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status"></span>
                                Verify
                            </button>
                        </form>
                        <p v-if="error" class="text-danger mt-3">{{ error }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { useAuthStore } from "../store/auth";

export default {
    data() {
        return {
            code: "",
            error: "",
            loading: false, // new loading state
        };
    },
    computed: {
        email() {
            return this.$route.query.email;
        },
    },
    methods: {
        async verifyCode() {
            const auth = useAuthStore();
            this.error = "";
            this.loading = true; // start spinner / disable button
            try {
                await auth.verifyTwoFA(this.email, this.code);
                this.$router.push({ name: "Home" });
            } catch (e) {
                this.error = e.response?.data?.message || "Invalid OTP code";
            } finally {
                this.loading = false; // stop spinner / enable button
            }
        },
    },
};
</script>
