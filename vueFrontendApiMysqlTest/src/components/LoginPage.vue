<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router';
import { useAuthStore } from '../store/authStore';
import axiosInstance from '../axiosInstance'; // Adjust the path as necessary

const router = useRouter();
const authStore = useAuthStore();
const email = ref('')
const password = ref('')
const isPwd = ref(true)
const errorMessage = ref('') // Ref for error message

const handleSubmit = async () => {
  errorMessage.value = ''; // Clear any previous error messages

  const loginData = {
    email: email.value,
    password: password.value
  };

  try {
    const response = await axiosInstance.post('/login', loginData); // Use your axios instance here

    if (response.status === 200) {
      console.log('Login successful:', response.data);
      const { token, role, id } = response.data;
      authStore.setAuthToken(token);
      authStore.setUserRole(role);
      authStore.setUserId(id);
      router.push('/welcome-page');
      console.log(response);
    } else {
      errorMessage.value = 'Login failed. Please check your credentials.'; // Set error message
    }
  } catch (error) {
    // Handle network errors or invalid credentials
    errorMessage.value = 'Login failed. Please check your credentials or try again later.';
    console.error('Error:', error);
  }
}

const handleRegister = () => {
  router.push('/register');
}

const handleForgotPassword = () => {
  router.push('/forgot-password');
}

</script>

<template>
  <q-card-section class="col-6">
    <h4 class="tw-text-black tw-mt-4">Insert your credentials</h4>
    <div class="container q-mt-xl">
      <div>
        <q-input class="!tw-w-full" outlined v-model="email" label="E-mail" :dense="false" />
      </div>
      <div class="q-mt-md">
        <q-input class="!tw-w-full" label="Password" v-model="password" filled :type="isPwd ? 'password' : 'text'">
          <template v-slot:append>
            <q-icon
              :name="isPwd ? 'visibility_off' : 'visibility'"
              class="cursor-pointer"
              @click="isPwd = !isPwd"
            />
          </template>
        </q-input>  
      </div>
    </div>
    <div v-if="errorMessage" class="tw-text-red-500 tw-mt-2">{{ errorMessage }}</div> <!-- Error message -->
    <div class="tw-mt-10">
      <div class="q-mt-md">
        <q-btn class="!tw-bg-blue-700" label="Submit" @click="handleSubmit"></q-btn>
      </div>
      <div class="q-mt-md">
        <q-btn class="!tw-bg-gray-500" label="Register" @click="handleRegister"></q-btn>
      </div>
      <div class="q-mt-md">
        <q-btn flat label="Forgot Password?" color="blue" @click="handleForgotPassword"></q-btn>
      </div>
    </div>
  </q-card-section>
</template>

<style scoped>
.container {
  display: flex;
  flex-direction: column;
  align-items: left;
}

.container div {
  margin-bottom: 10px;
  display: flex;
}

.tw-text-red-500 {
  color: red;
}
</style>
