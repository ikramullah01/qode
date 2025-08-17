<template>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h2 class="card-title mb-4 text-center">Create New Blog</h2>

                        <form @submit.prevent="submitBlog">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Title</label>
                                <input v-model="title" type="text" class="form-control form-control-lg"
                                    placeholder="Enter blog title" required />
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Excerpt</label>
                                <textarea v-model="excerpt" class="form-control" rows="2"
                                    placeholder="Write a brief excerpt" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Description</label>
                                <textarea v-model="description" class="form-control" rows="5"
                                    placeholder="Enter full description" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Keywords (tags)</label>
                                <input type="text" class="form-control" v-model="keywordsInput"
                                    placeholder="Laravel, Vue, Pinia" />
                                <small class="text-muted">Separate multiple keywords with commas</small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Featured Image</label>
                                <input type="file" @change="handleFileUpload" class="form-control" />
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Publishing Date</label>
                                <Datepicker v-model="publishDate" :enable-time-picker="true" :clearable="true"
                                    placeholder="Select publishing date & time" class="form-control" />
                            </div>

                            <!-- Error Message -->
                            <div v-if="error" class="alert alert-danger">{{ error }}</div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg" :disabled="submitting">
                                    <span v-if="submitting" class="spinner-border spinner-border-sm me-2"></span>
                                    {{ submitting ? "Submitting..." : "Create Blog" }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import { useAuthStore } from "@/store/auth";
import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

export default {
    name: "CreateBlog",
    components: {
        Datepicker,
    },
    setup() {
        const auth = useAuthStore();
        return { auth };
    },
    data() {
        return {
            title: "",
            excerpt: "",
            description: "",
            keywordsInput: "",
            image: null,
            publishDate: null,
            submitting: false,
            error: null,
        };
    },
    methods: {
        formatDate(date) {
            if (!date) return "";
            return date.toLocaleString(); // Customize formatting if needed
        },
        handleFileUpload(event) {
            this.image = event.target.files[0];
        },
        async submitBlog() {
            if (!this.auth.isAuthenticated) {
                alert("You must be logged in to create a blog");
                return;
            }

            this.submitting = true;
            this.error = null;
            try {
                const formData = new FormData();
                formData.append("title", this.title);
                formData.append("excerpt", this.excerpt);
                formData.append("description", this.description);
                if (this.image) formData.append("image", this.image);
                if (this.publishDate) formData.append("published_at", this.publishDate.toISOString());

                const keywordsArray = this.keywordsInput
                    .split(",")
                    .map(k => k.trim())
                    .filter(k => k.length > 0);

                keywordsArray.forEach((keyword, index) => {
                    formData.append(`keywords[${index}]`, keyword);
                });

                const { data } = await axios.post("/api/blog-posts", formData, {
                    headers: {
                        "Authorization": `Bearer ${this.auth.token}`,
                    },
                });

                // Redirect to the blog detail page
                this.$router.push({ name: "BlogDetail", params: { id: data.id } });
            } catch (error) {
                if (error.response?.status === 422) {
                    // validation error
                    this.error = error.response.data.message || "The data is not valid.";
                } else {
                    this.error = "Something went wrong. Try again.";
                }
            } finally {
                this.submitting = false;
            }
        },
    },
};
</script>
