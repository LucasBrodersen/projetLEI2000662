<script setup lang="ts">
import { ref } from 'vue';
import { AxiosError } from 'axios';
import { useRouter } from 'vue-router';
import axiosInstance from '../axiosInstance';


const router = useRouter();
const email = ref('');
const successMessage = ref('');
const errorMessage = ref('');

interface ErrorResponse {
  message: string;
}

const handleSubmit = async () => {
  try {
    const response = await axiosInstance.post('/password/forgot', { email: email.value });
    
    if (response.status === 200) {
      successMessage.value = 'A password reset link has been sent to your email.';
      errorMessage.value = ''; // Clear any previous error message
    } else {
      errorMessage.value = 'Failed to send reset link. Please try again later.';
      successMessage.value = ''; // Clear any previous success message
    }
  } catch (error) {
    const axiosError = error as AxiosError<ErrorResponse>;
    if (axiosError.response && axiosError.response.data && axiosError.response.data.message) {
      errorMessage.value = axiosError.response.data.message;
    } else {
      errorMessage.value = 'Error occurred while sending reset link.';
    }
    successMessage.value = '';
    console.error('Error:', error);
  }
};

const handleBack = () => {
  router.push('/');
};
</script>

<template>
  <q-card-section class="col-6">
    <h4 class="tw-text-black tw-mt-4">
      Forgot Password
    </h4>
    <div class="q-mt-xl">
      <p class="tw-text-gray-600">
        Enter your email address and we'll send you a link to reset your password.
      </p>
      <div>
        <q-input class="!tw-w-full" outlined v-model="email" label="E-mail" :dense="false" />
      </div>

      <div v-if="successMessage" class="tw-text-green-500 tw-mt-4">{{ successMessage }}</div>
      <div v-if="errorMessage" class="tw-text-red-500 tw-mt-4">{{ errorMessage }}</div>

      <div class="tw-mt-10">
        <!-- Wrap the buttons in a container to stack them vertically -->
        <div class="btn-container">
          <q-btn class="!tw-bg-blue-700" label="Send Reset Link" @click="handleSubmit"></q-btn>
          <q-btn class="!tw-bg-gray-500 q-mt-md" label="Back" @click="handleBack"></q-btn>
        </div>
      </div>
    </div>
  </q-card-section>
</template>

<style scoped>
.container {
  display: flex;
  flex-direction: column;
  align-items: center; /* Center the contents */
}

.container div {
  margin-bottom: 10px;
  display: flex;
}

/* Ensure buttons are stacked vertically with space between */
.btn-container {
  display: flex;
  flex-direction: column;
  align-items: center;
}

</style>
