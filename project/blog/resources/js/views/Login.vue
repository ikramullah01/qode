<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-5">
        <div class="card shadow">
          <div class="card-body">
            <h3 class="card-title text-center mb-4">Login</h3>

            <form @submit.prevent="requestLogin">
              <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input
                  v-model="email"
                  type="email"
                  class="form-control"
                  id="email"
                  required
                  :disabled="loading"
                />
              </div>

              <button
                type="submit"
                class="btn btn-primary w-100"
                :disabled="loading"
              >
                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                {{ loading ? "Sending..." : "Send OTP Code" }}
              </button>
            </form>

            <p v-if="success" class="text-success mt-3">
              OTP code sent. Check your email.
            </p>
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
      email: "",
      loading: false, // 🔹 track request status
      success: false,
      error: "",
    };
  },
  methods: {
    async requestLogin() {
      const auth = useAuthStore();
      this.error = "";
      this.loading = true; // 🔹 disable button and show spinner
      try {
        await auth.requestLogin(this.email);
        this.success = true;
        this.$router.push({ name: "TwoFA", query: { email: this.email } });
      } catch (e) {
        this.error = e.response?.data?.message || "Failed to send OTP code";
      } finally {
        this.loading = false; // 🔹 re-enable button
      }
    },
  },
};
</script>
