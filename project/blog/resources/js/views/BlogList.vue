<template>
  <div class="container my-5 blog-list-page">
    <h1 class="mb-4 text-center fw-bold">All Blog Posts</h1>

    <!-- Search bar -->
    <div class="input-group mb-5 shadow-sm rounded-pill overflow-hidden">
      <input v-model="searchQuery" @keyup.enter="searchPosts" type="text" class="form-control border-0"
        placeholder="Search blog posts..." :disabled="searching" />
      <button class="btn btn-primary px-4 d-flex align-items-center justify-content-center" type="button"
        @click="searchPosts" :disabled="searching">
        <span v-if="!searching">Search</span>
        <span v-else class="spinner-border spinner-border-sm text-light" role="status"></span>
      </button>
    </div>

    <div v-if="posts.length === 0 && loading" class="text-muted text-center py-5">
      <div class="spinner-border text-primary" role="status"></div>
      <div class="mt-2">Loading posts...</div>
    </div>

    <div v-else>
      <div v-if="posts.length === 0" class="alert alert-info text-center">
        No posts found.
      </div>

      <div class="row g-4">
        <div v-for="post in posts" :key="post.id" class="col-md-6 col-lg-4">
          <div class="card h-100 shadow-sm border-0 blog-card">
            <img v-if="post.image" :src="post.image" class="card-img-top" alt="Post image" />
            <div class="card-body d-flex flex-column">
              <h5 class="card-title fw-bold">{{ post.title }}</h5>
              <p class="card-text text-truncate mb-3">{{ post.excerpt }}</p>
              <p class="text-muted small mb-3">
                Published: {{ formatDate(post.published_at) }}
              </p>
              <router-link :to="`/blogs/${post.id}`" class="btn btn-outline-primary mt-auto">
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
      searching: false,
      page: 1,
      lastPage: null,
      searchQuery: "",
    };
  },
  methods: {
    async fetchPosts() {
      if (this.loading || (this.lastPage && this.page > this.lastPage)) return;

      this.loading = true;
      try {
        const response = await axios.get(`/api/blog-posts?page=${this.page}`);
        this.posts.push(...response.data.data);
        this.lastPage = response.data.last_page;
        this.page++;
      } catch (error) {
        console.error("Failed to fetch blog posts:", error);
      } finally {
        this.loading = false;
      }
    },
    async searchPosts() {
      if (!this.searchQuery) return; // optional: prevent empty search

      this.searching = true;
      try {
        const response = await axios.get(`/api/blog-posts/search`, {
          params: { q: this.searchQuery },
        });
        this.posts = response.data;
      } catch (error) {
        console.error("Failed to fetch blog posts:", error);
      } finally {
        this.searching = false;
      }
    },
    handleScroll() {
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
  min-height: 100vh;
}

.card-img-top {
  max-height: 200px;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.blog-card:hover .card-img-top {
  transform: scale(1.05);
}

.card-body {
  display: flex;
  flex-direction: column;
}

.blog-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  border-radius: 0.75rem;
}

.blog-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

.card-title {
  font-size: 1.25rem;
}

.card-text {
  min-height: 60px;
}

.input-group .form-control:focus {
  box-shadow: none;
}

.input-group .btn {
  border-radius: 0 50px 50px 0;
}
</style>
