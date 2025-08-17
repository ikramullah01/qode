<!-- components/AddComment.vue -->
<template>
    <div class="mb-5">
        <h5>Add a Comment</h5>
        <span>Type Markdown:(**bold**, _italic_, # Header, etc.)</span>
        <textarea v-model="comment" class="form-control mb-2" rows="3" placeholder="Write your comment here..."
            :disabled="posting"></textarea>

        <button class="btn btn-primary mb-3" @click="submitComment" :disabled="posting || !comment.trim()">
            <span v-if="posting" class="spinner-border spinner-border-sm me-2"></span>
            {{ posting ? "Posting..." : "Post Comment" }}
        </button>

        <div v-if="comment.trim()">
            <h6>Preview:</h6>
            <div v-html="renderedMarkdown" class="border p-2 rounded"></div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import { marked } from "marked";

export default {
    name: "AddComment",
    props: {
        postId: { type: [Number, String], required: true },
        token: { type: String, required: true },
    },
    data() {
        return {
            comment: "",
            posting: false,
        };
    },
    computed: {
        renderedMarkdown() {
            return marked(this.comment);
        },
    },
    methods: {
        async submitComment() {
            if (!this.comment.trim()) return;
            this.posting = true;
            try {
                const { data } = await axios.post(
                    `/api/blog-posts/${this.postId}/comments`,
                    { content: this.comment },
                    {
                        headers: {
                            Authorization: `Bearer ${this.token}`, // add your token here
                        },
                    }
                );
                this.$emit("comment-added", data); // emit to parent
                this.comment = "";
            } catch (error) {
                console.error("Failed to post comment:", error);
            } finally {
                this.posting = false;
            }
        },
    },
};
</script>

<style scoped>
div[v-html] {
    background: #f8f9fa;
    min-height: 50px;
    white-space: pre-wrap;
}
</style>
