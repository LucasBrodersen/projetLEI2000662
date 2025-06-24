<template>
  <div>
    <q-layout view="hHh Lpr lff">
      <q-header elevated :class="$q.dark.isActive ? 'bg-secondary' : 'bg-black'">
        <q-toolbar>
          <q-btn flat @click="drawer = !drawer" round dense icon="menu" />
          <q-toolbar-title :class="drawer ? 'tw-ml-36' : 'tw-mr-12'">SPA V1</q-toolbar-title>
        </q-toolbar>
      </q-header>

      <q-drawer
          v-model="drawer"
          show-if-above
          :width="200"
          :breakpoint="500"
          bordered
          class="tw-bg-black tw-text-white"
        >
        <q-scroll-area class="fit" :horizontal-thumb-style="{ opacity: 0 }">
          <q-list padding class="tw-items-baseline">
            <q-item clickable v-ripple :active="route.path === '/welcome-page'">
              <router-link to="/welcome-page" class="q-item-section" style="display: flex; align-items: center;">
                <q-item-section avatar>
                  <q-icon name="star" />
                </q-item-section>
                <q-item-section>
                  Welcome Page
                </q-item-section>
              </router-link>
            </q-item>
            <q-item clickable v-ripple :active="route.path === '/subscriptions'">
              <router-link to="/subscriptions" class="q-item-section" style="display: flex; align-items: center;">
                <q-item-section avatar>
                  <q-icon name="book" />
                </q-item-section>
                <q-item-section>
                  Subscriptions
                </q-item-section>
              </router-link>
            </q-item>
            <q-item clickable v-ripple :active="route.path === `/view-user/${authStore.userId}`" @click="goToMyProfile">
              <router-link style="display: flex; align-items: center;">
                <q-item-section avatar>
                  <q-icon name="person" />
                </q-item-section>
                <q-item-section>
                  My Profile
                </q-item-section>
              </router-link>
            </q-item>
            
            <!-- Condicionalmente mostrar este item para 'admin' e 'parceiro' -->
            <q-item v-if="isAdminOrPartner" clickable v-ripple :active="route.path === '/list-users'">
              <router-link to="/list-users" class="q-item-section" style="display: flex; align-items: center;">
                <q-item-section avatar>
                  <q-icon name="list" />
                </q-item-section>
                <q-item-section>
                  List Users
                </q-item-section>
              </router-link>
            </q-item>  

            <!-- Exibir apenas para 'admin' -->
            <q-item v-if="isAdmin" clickable v-ripple :active="route.path === '/register-user'">
              <router-link to="/register-user" class="q-item-section" style="display: flex; align-items: center;">
                <q-item-section avatar>
                  <q-icon name="add" />
                </q-item-section>
                <q-item-section>
                  Register User
                </q-item-section>
              </router-link>
            </q-item>

            <q-item v-if="isAdmin" clickable v-ripple :active="route.path === '/posts'">
              <router-link to="/posts" class="q-item-section" style="display: flex; align-items: center;">
                <q-item-section avatar>
                  <q-icon name="article" />
                </q-item-section>
                <q-item-section>
                  Posts
                </q-item-section>
              </router-link>
            </q-item>

            <q-item clickable v-ripple @click="handleLogout">
              <q-item-section avatar>
                <q-icon name="exit_to_app" />
              </q-item-section>
              <q-item-section style="align-items: start;">
                Logout
              </q-item-section>
            </q-item>
          </q-list>
        </q-scroll-area>
      </q-drawer>

      <q-page-container>
        <q-page class="tw-bg-white tw-rounded-lg" padding>
          <!-- Aqui é onde suas rotas serão renderizadas -->
          <router-view />
        </q-page>
      </q-page-container>
    </q-layout>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../store/authStore';


const drawer = ref(false)
const miniState = ref(false)
const route = useRoute()
const router = useRouter();
const authStore = useAuthStore()

// Verifica se o usuário está autenticado antes de renderizar as rotas protegidas
// if (route.meta.requiresAuth && !userIsAuthenticated()) {
//   console.log("aqui")
//   // Redirecionar para a página de login se o usuário não estiver autenticado
//   // Aqui você deve substituir `userIsAuthenticated()` pela sua lógica de autenticação
//   redirectToLoginPage()
// }

// function userIsAuthenticated() {
//   // Lógica para verificar se o usuário está autenticado
//   // Retorne true se autenticado, false caso contrário
// }

// function redirectToLoginPage() {
//   // Redireciona para a página de login
//   // Você pode usar router.push('/caminho/para/login') ou algo similar
// }

const isAdmin = computed(() => authStore.userRole === 'admin')
const isAdminOrPartner = computed(() => ['admin', 'parceiro'].includes(authStore.userRole))

function handleLogout() {
  authStore.clearAuth()
  router.push('/')
}

function goToMyProfile() {
  router.push(`/view-user/${authStore.userId}`)
}
</script>


<style scoped>
.white-background {
  background-color: white; /* White background for q-page */
  padding: 20px; /* Add padding inside the page */
  min-height: 100vh; /* Ensure the page takes full height */
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Optional: Add a light shadow for depth */
}
</style>
