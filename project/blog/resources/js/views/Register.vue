<template>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3 class="mb-4 text-center">Register</h3>

                        <!-- Error Message -->
                        <div v-if="error" class="alert alert-danger">{{ error }}</div>

                        <!-- Success Message -->
                        <div v-if="success" class="alert alert-success">
                            Registration successful! Redirecting to verification...
                        </div>

                        <form @submit.prevent="registerUser">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" v-model="form.name" class="form-control" required />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" v-model="form.email" class="form-control" required />
                            </div>

                            <button type="submit" class="btn btn-primary w-100" :disabled="loading">
                                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                                Register
                            </button>
                        </form>

                        <div class="mt-3 text-center">
                            Already have an account?
                            <router-link :to="{ name: 'Login' }">Login here</router-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "Register",
    data() {
        return {
            form: {
                name: "",
                email: "",
            },
            loading: false,
            error: null,
            success: false,
        };
    },
    methods: {
        async registerUser() {
            this.loading = true;
            this.error = null;
            this.success = false;

            try {
                const response = await axios.post("/api/register", this.form);

                // if API success, show message then redirect
                this.success = true;

                setTimeout(() => {
                    this.$router.push({ name: "TwoFA", query: { email: this.form.email } });
                }, 1500);
            } catch (err) {
                if (err.response?.status === 422) {
                    // validation error
                    this.error = err.response.data.message || "This email may already be taken.";
                } else {
                    this.error = "Registration failed. Try again.";
                }
            } finally {
                this.loading = false;
            }
        },
    },
};
</script>
