<script setup lang="ts">
import { ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axiosInstance from '../axiosInstance';

const router = useRouter();
const route = useRoute();

// Form fields
const password = ref('');
const passwordConfirmation = ref('');
const isPwd = ref(true);
const isPwdConfirm = ref(true);

// Token and email from the query params
const token = route.query.token as string || '';
const email = route.query.email as string || '';

// Error message and dialog visibility state
const errorMessage = ref('');
const showDialog = ref(false);
const validationErrors = ref<Record<string, string[]>>({});

// Function to handle form submission
const handleSubmit = async () => {
  const resetData = {
    token: token,
    email: email,
    password: password.value,
    password_confirmation: passwordConfirmation.value,
  };

  try {
    const response = await axiosInstance.post('/password/reset', resetData);

    if (response.status >= 200 && response.status < 300) {
      validationErrors.value = {}; // Clear any previous validation errors
      router.push('/'); // Redirect to login page after successful reset
    } else {
      console.error('Password reset failed');
      errorMessage.value = 'An error occurred. Please try again later.';
      showDialog.value = true;
    }
  } catch (error: any) {
    if (error.response && error.response.status === 422) {
      // Capture validation errors
      validationErrors.value = error.response.data.errors || {};
      console.error("Validation error:", validationErrors.value);
    } else {
      errorMessage.value = 'An unexpected error occurred. Please try again.';
      showDialog.value = true;
    }
  }
};

// Function to handle "Back" button click
const handleBack = () => {
  router.push('/');
};

// Function to clear the error message and close the dialog
const closeDialog = () => {
  showDialog.value = false;
  errorMessage.value = ''; // Reset the error message
};
</script>

<template>
  <q-card-section class="col-6">
    <h4 class="tw-text-black tw-mt-4">
      Reset your password
    </h4>

    <div class="container q-mt-xl">
      <div>
        <q-input
          class="!tw-w-full"
          outlined
          v-model="email"
          label="Email"
          :dense="false"
          readonly
        />
      </div>
      <div class="q-mt-md">
        <q-input
          class="!tw-w-full"
          label="New Password"
          v-model="password"
          filled
          :type="isPwd ? 'password' : 'text'"
          :error="validationErrors.password ? true : false"
          :error-message="validationErrors.password ? validationErrors.password[0] : ''"
        >
          <template v-slot:append>
            <q-icon :name="isPwd ? 'visibility_off' : 'visibility'" class="cursor-pointer" @click="isPwd = !isPwd" />
          </template>
        </q-input>
      </div>
      <div class="q-mt-md">
        <q-input
          class="!tw-w-full"
          label="Confirm New Password"
          v-model="passwordConfirmation"
          filled
          :type="isPwdConfirm ? 'password' : 'text'"
          :error="validationErrors.password_confirmation ? true : false"
          :error-message="validationErrors.password_confirmation ? validationErrors.password_confirmation[0] : ''"
        >
          <template v-slot:append>
            <q-icon :name="isPwdConfirm ? 'visibility_off' : 'visibility'" class="cursor-pointer" @click="isPwdConfirm = !isPwdConfirm" />
          </template>
        </q-input>
      </div>
    </div>

    <div class="tw-mt-10">
      <div class="q-mt-md">
        <q-btn class="!tw-bg-blue-700 q-mt-md" label="Reset Password" @click="handleSubmit"></q-btn>
      </div>
      <div class="q-mt-md">
        <q-btn class="!tw-bg-gray-500 q-mt-md" label="Back" @click="handleBack"></q-btn>
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
</style>
