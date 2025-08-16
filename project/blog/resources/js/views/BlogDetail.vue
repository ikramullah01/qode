<template>
    <div class="container my-5">
        <div v-if="loading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status"></div>
        </div>

        <div v-else>
            <h1 class="mb-3">{{ post.title }}</h1>
            <p class="text-muted mb-3">Published: {{ formatDate(post.published_at) }}</p>
            <img v-if="post.image" :src="post.image" class="img-fluid mb-4 rounded shadow-sm" alt="Post image" />
            <p>{{ post.excerpt }}</p>
            <hr />
            <div v-html="formattedDescription"></div>

            <hr />
            <h4>Comments ({{ post.comments.length }})</h4>
            <div v-if="post.comments.length === 0" class="text-muted mb-3">
                No comments yet.
            </div>
            <ul class="list-group mb-4">
                <li v-for="comment in post.comments" :key="comment.id" class="list-group-item">
                    <div v-html="renderMarkdown(comment.content)"></div>
                    <small class="text-muted">  Posted by: {{ comment.user.name }}  </small>
                    <small class="text-muted">  Posted: {{ formatDate(comment.created_at) }}</small>
                </li>
            </ul>

            <div class="mb-5">
                <h5>Add a Comment</h5>
                <textarea v-model="newComment" class="form-control mb-2" rows="3"
                    placeholder="Write your comment here..."></textarea>
                <button class="btn btn-primary" @click="submitComment" :disabled="commenting">
                    {{ commenting ? "Posting..." : "Post Comment" }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import { marked } from "marked";

export default {
    name: "BlogDetail",
    data() {
        return {
            post: null,
            loading: true,
            newComment: "",
            commenting: false,
            token: "1|WIYM92DZYDHs5rTV0Md2XROvTjdQZQLBNuIWR03a40916e36", // replace with actual token
        };
    },
    computed: {
        formattedDescription() {
            // preserve line breaks
            return this.post ? this.post.description.replace(/\n/g, "<br/>") : "";
        },
    },
    methods: {
        async fetchPost() {
            this.loading = true;
            try {
                const response = await axios.get(`/api/blog-posts/${this.$route.params.id}`, {
                    headers: { Authorization: `Bearer ${this.token}` },
                });
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
            return marked(content); // ✅ convert markdown to HTML
        },
        async submitComment() {
            if (!this.newComment.trim()) return;

            this.commenting = true;
            try {
                const response = await axios.post(
                    `/api/blog-posts/${this.post.id}/comments`,
                    { content: this.newComment },
                    { headers: { Authorization: `Bearer ${this.token}` } }
                );
                this.post.comments.push(response.data); // append new comment
                this.newComment = "";
            } catch (error) {
                console.error("Failed to post comment:", error);
            } finally {
                this.commenting = false;
            }
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
}

textarea {
    resize: none;
}
</style>
