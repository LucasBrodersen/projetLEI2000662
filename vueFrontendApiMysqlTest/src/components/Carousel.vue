<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';

const props = defineProps<{ images: string[] }>();
const carousel = ref<HTMLElement | null>(null);
const selectedImage = ref<string | null>(null);
const scrollProgress = ref(0); // Track scroll progress percentage

const updateScrollProgress = () => {
  if (carousel.value) {
    const maxScroll = carousel.value.scrollWidth - carousel.value.clientWidth;
    scrollProgress.value = maxScroll > 0 ? (carousel.value.scrollLeft / maxScroll) * 100 : 0;
  }
};

const scrollLeft = () => {
  if (carousel.value) {
    carousel.value.scrollBy({ left: -200, behavior: 'smooth' });
  }
};

const scrollRight = () => {
  if (carousel.value) {
    carousel.value.scrollBy({ left: 200, behavior: 'smooth' });
  }
};

const openImage = (image: string) => {
  selectedImage.value = image;
};

const closeImage = () => {
  selectedImage.value = null;
};

onMounted(() => {
  updateScrollProgress();
  if (carousel.value) {
    carousel.value.addEventListener('scroll', updateScrollProgress);
  }
});

watch(() => props.images, updateScrollProgress);
</script>

<template>
  <div class="carousel-container">
    <button @click="scrollLeft" class="arrow left">&#10094;</button>
    <div ref="carousel" class="carousel">
      <img v-for="(image, index) in images" :key="index" :src="image" class="carousel-item" @click="openImage(image)" />
    </div>
    <button @click="scrollRight" class="arrow right">&#10095;</button>

    <div v-if="selectedImage" class="modal" @click="closeImage">
      <img :src="selectedImage" class="modal-image" />
    </div>
  </div>

  <!-- Scroll Indicator Bar -->
  <div class="scroll-indicator">
    <div class="scroll-bar" :style="{ width: scrollProgress + '%' }"></div>
  </div>
</template>

<style scoped>
.carousel-container {
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  width: 80%;
  margin: auto;
  overflow: hidden;
}


.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.8);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10;
}


.carousel {
  display: flex;
  gap: 10px;
  overflow-x: auto;
  scroll-behavior: smooth;
  white-space: nowrap;
  width: 100%;
  padding: 10px 0;
  scrollbar-width: none;
}

.carousel::-webkit-scrollbar {
  display: none;
}

.carousel-item {
  width: 200px;
  height: 200px;
  object-fit: cover;
  border-radius: 8px;
  cursor: pointer;
}

.arrow {
  background: rgba(0, 0, 0, 0.5);
  color: white;
  border: none;
  padding: 10px;
  cursor: pointer;
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 1;
}

.left { left: 0; }
.right { right: 0; }

/* Scroll Indicator Bar */
.scroll-indicator {
  width: 80%;
  height: 4px;
  background: rgba(0, 0, 0, 0.1);
  border-radius: 5px;
  margin: 10px auto;
  position: relative;
  overflow: hidden;
}

.scroll-bar {
  height: 100%;
  background: black;
  border-radius: 5px;
  transition: width 0.3s ease;
}
</style>
