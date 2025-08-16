<!-- components/AddComment.vue -->
<template>
    <div class="mb-5">
        <h5>Add a Comment</h5>
        <textarea v-model="comment" class="form-control mb-2" rows="3" placeholder="Write your comment here..."
            :disabled="posting"></textarea>
        <button class="btn btn-primary" @click="submitComment" :disabled="posting || !comment.trim()">
            <span v-if="posting" class="spinner-border spinner-border-sm me-2"></span>
            {{ posting ? "Posting..." : "Post Comment" }}
        </button>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "AddComment",
    props: {
        postId: {
            type: [Number, String],
            required: true,
        },
        token: {
            type: String,
            required: true,
        },
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
                    { content: this.comment }
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
