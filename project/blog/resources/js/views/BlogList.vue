<template>
  <div class="container my-5 blog-list-page">
    <h1 class="mb-4">All Blog Posts</h1>

    <div v-if="posts.length === 0 && loading" class="text-muted">
      Loading posts...
    </div>

    <div v-else>
      <div v-if="posts.length === 0" class="alert alert-info">
        No posts found.
      </div>

      <div class="row">
        <div
          v-for="post in posts"
          :key="post.id"
          class="col-md-6 col-lg-4 mb-4"
        >
          <div class="card h-100 shadow-sm">
            <img
              v-if="post.image"
              :src="post.image"
              class="card-img-top"
              alt="Post image"
            />
            <div class="card-body d-flex flex-column">
              <h5 class="card-title">{{ post.title }}</h5>
              <p class="card-text text-truncate">{{ post.excerpt }}</p>
              <p class="text-muted mb-2">
                Published: {{ formatDate(post.published_at) }}
              </p>
              <router-link
                :to="`/blogs/${post.id}`"
                class="btn btn-primary mt-auto"
              >
                Read More
              </router-link>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading indicator -->
      <div v-if="loading" class="text-center py-3">
        <div class="spinner-border text-primary" role="status"></div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "BlogList",
  data() {
    return {
      posts: [],
      loading: false,
      page: 1,
      lastPage: null,
      token:
        "1|WIYM92DZYDHs5rTV0Md2XROvTjdQZQLBNuIWR03a40916e36", // replace with actual token
    };
  },
  methods: {
    async fetchPosts() {
      if (this.loading || (this.lastPage && this.page > this.lastPage)) return;

      this.loading = true;
      try {
        const response = await axios.get(`/api/blog-posts?page=${this.page}`, {
          headers: { Authorization: `Bearer ${this.token}` },
        });

        this.posts.push(...response.data.data);
        this.lastPage = response.data.last_page;
        this.page++;
      } catch (error) {
        console.error("Failed to fetch blog posts:", error);
      } finally {
        this.loading = false;
      }
    },
    handleScroll() {
      // Debug to confirm event is firing
      console.log("scrolling...");

      const bottomOfWindow =
        window.innerHeight + window.scrollY >=
        document.documentElement.offsetHeight - 100;

      if (bottomOfWindow) {
        this.fetchPosts();
      }
    },
    formatDate(dateStr) {
      return new Date(dateStr).toLocaleDateString();
    },
  },
  mounted() {
    this.fetchPosts();
    window.addEventListener("scroll", this.handleScroll);
  },
  beforeUnmount() {
    window.removeEventListener("scroll", this.handleScroll);
  },
};
</script>

<style scoped>
.blog-list-page {
  min-height: 200vh; /* force scroll space for testing */
}

.card-text {
  min-height: 50px;
}

.card-img-top {
  max-height: 180px;
  object-fit: cover;
}
</style>
