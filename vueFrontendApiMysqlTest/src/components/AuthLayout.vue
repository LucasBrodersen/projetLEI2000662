<!-- AuthLayout.vue -->
<template>
  <q-card class="row q-mx-auto tw-mt-80 tw-max-w-7xl" flat>
    <q-img
      class="col-6"
      src="../assets/7.jpg"
    />
      <router-view />
    </q-card>
</template>

<style scoped>
.q-card {
  height: 50%;
  display: flex;
  align-items: center;
}
</style>
