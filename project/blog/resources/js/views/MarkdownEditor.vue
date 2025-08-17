<!-- components/MarkdownEditor.vue -->
<template>
    <div ref="editorContainer"></div>
</template>

<script>
import { Editor } from "@toast-ui/editor";
import "@toast-ui/editor/dist/toastui-editor.css";

export default {
    name: "MarkdownEditor",
    props: {
        modelValue: String
    },
    emits: ["update:modelValue"],
    mounted() {
        this.editor = new Editor({
            el: this.$refs.editorContainer,
            initialEditType: "wysiwyg",
            previewStyle: "vertical",
            height: "200px",
            initialValue: this.modelValue || ""
        });

        this.editor.on("change", () => {
            this.$emit("update:modelValue", this.editor.getMarkdown());
        });
    },
    watch: {
        modelValue(newVal) {
            if (this.editor && newVal !== this.editor.getMarkdown()) {
                this.editor.setMarkdown(newVal || ""); // reset editor content
            }
        }
    },
    beforeUnmount() {
        this.editor.destroy();
    }
};
</script>
