<script setup lang="ts">
import { ref, onMounted } from 'vue';
import Carousel from './Carousel.vue';
import Blog from './Blog.vue';
import axiosInstance from '../axiosInstance'
import { useAuthStore } from '../store/authStore'


const authStore = useAuthStore()
const user = ref<any>(null)
const loading = ref(true)
const error = ref('')

const token = authStore.authToken

onMounted(async () => {
  try {
    // Busca os dados do usuário logado, incluindo assinatura
    const response = await axiosInstance.get(`/v1/users/${authStore.userId}?includeSubscription=true`, {
      headers: { Authorization: `Bearer ${token}` }
    })
    user.value = response.data.data
  } catch (err: any) {
    error.value = 'Erro ao carregar informações do usuário'
  } finally {
    loading.value = false
  }
})

const images = ref([
  '/CarouselImages/image1.jpg',
  '/CarouselImages/image2.jpg',
  '/CarouselImages/image3.jpg',
  '/CarouselImages/image4.jpg',
  '/CarouselImages/image5.jpg',
  '/CarouselImages/image6.jpg',
  '/CarouselImages/image7.jpg',
  '/CarouselImages/image8.jpg',
  '/CarouselImages/image9.jpg',
  '/CarouselImages/image10.jpg'
]);


const blogPosts = ref([
  { title: 'Post 1', content: 'This is the first blog post. It contains some sample content to demonstrate layout.' },
  { title: 'Post 2', content: 'Here is another blog post. It includes a brief description of the topic being discussed.' },
  { title: 'Post 3', content: 'Yet another blog post with more information and insights.' },
  { title: 'Post 4', content: 'Yet another blog post with more information and insights.' },
]);
</script>

<template>
  <div class="welcome-page">
    <Carousel :images="images" />
    <!-- <Blog :posts="blogPosts" /> -->
      <div class="welcome-page">
    <div v-if="loading">Carregando informações...</div>
    <div v-else-if="error">{{ error }}</div>
    <div v-else>
      <h2>Bem-vindo{{ user?.name ? ', ' + user.name : '' }}!</h2>
      <div style="margin-bottom: 16px;">
        <span v-if="user?.activeSubscription">
          <strong>Plano ativo:</strong> {{ user.activeSubscription.plan_name }}<br>
          <strong>Status:</strong> {{ user.activeSubscription.stripe_status }}
        </span>
        <span v-else>
          <strong>Você não possui um plano ativo no momento.</strong>
        </span>
      </div>
      <div>
        <h3>O que você pode fazer no sistema:</h3>
        <ul style="text-align:left; max-width:400px; margin:0 auto;">
          <li>Visualizar e editar seu perfil</li>
          <li>Gerenciar sua assinatura e pagamentos</li>
          <li>Visualizar faturas</li>
          <li>Entrar em contato com o suporte</li>
        </ul>
      </div>
    </div>
  </div>
  </div>
</template>

<style scoped>
.welcome-page {
  text-align: center;
  padding: 20px;
}
</style>
