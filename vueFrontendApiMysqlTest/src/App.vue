<script setup lang="ts">
import LoginPage from './components/LoginPage.vue';
</script>

<template>
  <!-- <LoginPage/> -->
  <!-- <router-link to="/register">sasasas </router-link> -->
  <router-view></router-view>
</template>

<style scoped>
.logo {
  height: 6em;
  padding: 1.5em;
  will-change: filter;
  transition: filter 300ms;
}
.logo:hover {
  filter: drop-shadow(0 0 2em #646cffaa);
}
.logo.vue:hover {
  filter: drop-shadow(0 0 2em #42b883aa);
}
</style>./components/LoginPage.vue
./components/LoginForm.vue