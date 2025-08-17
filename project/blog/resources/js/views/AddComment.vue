<template>
    <div class="mb-5">
        <h5>Add a Comment</h5>

        <MarkdownEditor v-model="comment" />

        <button class="btn btn-primary my-3" @click="submitComment" :disabled="posting || !comment.trim()">
            <span v-if="posting" class="spinner-border spinner-border-sm me-2"></span>
            {{ posting ? "Posting..." : "Post Comment" }}
        </button>
    </div>
</template>

<script>
import axios from "axios";
import MarkdownEditor from "./MarkdownEditor.vue";

export default {
    name: "AddComment",
    components: { MarkdownEditor },
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
    methods: {
        async submitComment() {
            if (!this.comment.trim()) return;
            this.posting = true;
            try {
                const { data } = await axios.post(
                    `/api/blog-posts/${this.postId}/comments`,
                    { content: this.comment },
                    { headers: { Authorization: `Bearer ${this.token}` } }
                );
                this.$emit("comment-added", data);
                this.comment = "";
            } catch (error) {
                console.error("Failed to post comment:", error);
            } finally {
                this.posting = false;
            }
        }
    }
};
</script>
