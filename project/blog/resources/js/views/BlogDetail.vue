<template>
    <div class="container my-5">
        <!-- Loading Spinner -->
        <div v-if="loading" class="d-flex justify-content-center align-items-center py-5">
            <div class="spinner-border text-primary" role="status"></div>
        </div>

        <div v-else>
            <!-- Blog Header -->
            <div class="mb-4">
                <h1 class="display-5 fw-bold">{{ post.title }}</h1>
                <p class="text-muted mb-0">Published: {{ formatDate(post.published_at) }}</p>
            </div>

            <!-- Keywords -->
            <div v-if="post.keywords && post.keywords.length" class="mb-3">
                <span v-for="(keyword, index) in post.keywords" :key="index" class="badge bg-primary me-2">
                    {{ keyword }}
                </span>
            </div>

            <!-- Blog Image -->
            <div v-if="post.image" class="mb-4">
                <img :src="post.image" class="img-fluid rounded shadow-sm w-100" alt="Post image" />
            </div>

            <!-- Blog Content -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <p class="lead">{{ post.excerpt }}</p>
                    <hr>
                    <div v-html="formattedDescription" class="fs-6"></div>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="mb-4">
                <h4 class="mb-3">Comments ({{ post.comments.length }})</h4>

                <div v-if="post.comments.length === 0" class="text-muted mb-3">
                    No comments yet.
                </div>

                <ul class="list-group">
                    <li v-for="comment in post.comments" :key="comment.id"
                        class="list-group-item list-group-item-action mb-2 shadow-sm">
                        <div v-html="renderMarkdown(comment.content)"></div>
                        <div class="mt-2">
                            <small class="text-muted me-3">Posted by: {{ comment.user.name }}</small>
                            <small class="text-muted">Posted: {{ formatDate(comment.created_at) }}</small>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Add Comment -->
            <div>
                <AddComment v-if="auth.isAuthenticated" :postId="post.id" :token="token"
                    @comment-added="handleNewComment" />
                <p v-else class="text-muted fst-italic">Please log in to add a comment.</p>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import { marked } from "marked";
import AddComment from "./AddComment.vue";
import { useAuthStore } from "@/store/auth";

export default {
    name: "BlogDetail",
    components: { AddComment },
    setup() {
        const auth = useAuthStore();
        return { auth };
    },
    data() {
        return {
            post: null,
            loading: true,
            token: "",
        };
    },
    computed: {
        formattedDescription() {
            return this.post ? this.post.description.replace(/\n/g, "<br/>") : "";
        },
    },
    methods: {
        async fetchPost() {
            this.loading = true;
            try {
                const response = await axios.get(`/api/blog-posts/${this.$route.params.id}`);
                this.post = response.data;
            } catch (error) {
                console.error("Failed to fetch post:", error);
            } finally {
                this.loading = false;
            }
        },
        formatDate(dateStr) {
            return new Date(dateStr).toLocaleString();
        },
        renderMarkdown(content) {
            return marked(content);
        },
        handleNewComment(comment) {
            this.post.comments.push(comment);
        },
    },
    mounted() {
        this.fetchPost();
    },
};
</script>

<style scoped>
img {
    max-height: 400px;
    object-fit: cover;
}

.list-group-item {
    font-size: 0.95rem;
    border-radius: 0.5rem;
}

.card-body p,
.card-body div {
    line-height: 1.6;
}

textarea {
    resize: none;
}
</style>
