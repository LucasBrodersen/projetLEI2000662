<script setup lang="ts">
import { ref } from 'vue';

const props = defineProps<{ posts: { title: string; content: string }[] }>();
const selectedPost = ref<{ title: string; content: string } | null>(null);

const openModal = (post: { title: string; content: string }) => {
  selectedPost.value = post;
};

const closeModal = () => {
  selectedPost.value = null;
};
</script>

<template>
  <div class="blog-section">
    <h2>Latest Blog Posts</h2>
    <div class="blog-list">
      <div v-for="(post, index) in posts" :key="index" class="blog-card" @click="openModal(post)">
        <h3>{{ post.title }}</h3>
        <p>{{ post.content.substring(0, 100) }}...</p> <!-- Shows only a preview -->
      </div>
    </div>

    <!-- Modal -->
    <div v-if="selectedPost" class="modal" @click="closeModal">
      <div class="modal-content" @click.stop>
        <button class="close-btn" @click="closeModal">&times;</button>
        <h2>{{ selectedPost.title }}</h2>
        <p>{{ selectedPost.content }}</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.blog-section {
  margin-top: 50px;
  padding: 20px;
}

.blog-section h2 {
  font-size: 24px;
  margin-bottom: 20px;
}

.blog-list {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 15px;
}

.blog-card {
  width: 80%;
  max-width: 600px;
  padding: 20px;
  background: #f9f9f9;
  border-radius: 10px;
  box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
  text-align: left;
  cursor: pointer;
  transition: transform 0.2s ease-in-out;
}

.blog-card:hover {
  transform: scale(1.02);
}

.blog-card h3 {
  margin-bottom: 10px;
  font-size: 20px;
}

.blog-card p {
  font-size: 16px;
  color: #555;
}

/* Modal */
.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10;
}

.modal-content {
  background: white;
  padding: 20px;
  border-radius: 10px;
  max-width: 500px;
  text-align: center;
  box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
}

.close-btn {
  position: absolute;
  top: 15px;
  right: 20px;
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
}
</style>
