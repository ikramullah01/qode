<template>
    <div class="container my-5">
        <h2>Create New Blog</h2>

        <form @submit.prevent="submitBlog">
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input v-model="title" type="text" class="form-control" required />
            </div>

            <div class="mb-3">
                <label class="form-label">Excerpt</label>
                <textarea v-model="excerpt" class="form-control" rows="2"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Description </label>
                <textarea v-model="description" class="form-control" rows="5" required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Keywords (tags, multiple allowed)</label>
                <input type="text" class="form-control" v-model="keywordsInput"
                    placeholder="Enter keywords separated by commas" />
                <small class="text-muted">Example: Laravel, Vue, Pinia</small>
            </div>

            <div class="mb-3">
                <label class="form-label">Image</label>
                <input type="file" @change="handleFileUpload" class="form-control" />
            </div>

            <button type="submit" class="btn btn-primary" :disabled="submitting">
                {{ submitting ? "Submitting..." : "Create Blog" }}
            </button>
        </form>
    </div>
</template>

<script>
import axios from "axios";
import { useAuthStore } from "@/store/auth";

export default {
    name: "CreateBlog",
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
            submitting: false,
        };
    },
    methods: {
        handleFileUpload(event) {
            this.image = event.target.files[0];
        },
        async submitBlog() {
            if (!this.auth.isAuthenticated) {
                alert("You must be logged in to create a blog");
                return;
            }

            this.submitting = true;
            try {
                const formData = new FormData();
                formData.append("title", this.title);
                formData.append("excerpt", this.excerpt);
                formData.append("description", this.description);
                if (this.image) formData.append("image", this.image);

                const keywordsArray = this.keywordsInput
                    .split(",")
                    .map(k => k.trim())
                    .filter(k => k.length > 0);

                keywordsArray.forEach((keyword, index) => {
                    formData.append(`keywords[${index}]`, keyword);
                });

                const { data } = await axios.post("/api/blog-posts", formData, {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                });

                // Redirect to the blog detail page
                this.$router.push({ name: "BlogDetail", params: { id: data.id } });
            } catch (error) {
                console.error("Failed to create blog:", error);
            } finally {
                this.submitting = false;
            }
        },
    },
};
</script>

<style scoped>
textarea {
    resize: none;
}
</style>
