<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import axiosInstance from '../axiosInstance';

interface ValidationErrorResponse {
  errors: Record<string, string[]>;
}

interface AxiosErrorResponse {
  status: number;
  data: ValidationErrorResponse;
}

interface AxiosErrorWithResponse extends Error {
  response?: AxiosErrorResponse;
}

const router = useRouter();
const email = ref('');
const name = ref('');
const password = ref('');
const passwordConfirmation = ref('');
const isPwd = ref(true);
const isPwdConfirm = ref(true);

// For validation errors
const validationErrors = ref<Record<string, string[]>>({});

const handleSubmit = async () => {
  const registerData = {
    name: name.value,
    email: email.value,
    password: password.value,
    password_confirmation: passwordConfirmation.value,
    type: 'admin'
  };

  try {
    const response = await axiosInstance.post('/register', registerData);

    // Clear validation errors on successful submission
    validationErrors.value = {};

    if (response.status >= 200 && response.status < 300) {
      router.push('/');
    } else {
      console.error('Register failed');
    }
  } catch (error: unknown) {
    if (isAxiosErrorWithResponse(error)) {
      if (error.response?.status === 422) {
        // Capture validation errors
        validationErrors.value = error.response.data.errors || {};
        console.error("Validation error:", validationErrors.value);
      } else {
        console.error('Error registering user:', error);
      }
    }
  }
};

function isAxiosErrorWithResponse(error: unknown): error is AxiosErrorWithResponse {
  return (error as AxiosErrorWithResponse).response !== undefined;
}

const handleBack = () => {
  router.push('/');
};
</script>

<template>
  <q-card-section class="col-6">
    <h4 class="tw-text-black tw-mt-4">
      Register for an account
    </h4>
    <div class="container q-mt-xl">
      <div>
        <q-input
          class="!tw-w-full"
          outlined
          v-model="name"
          label="Name"
          :dense="false"
          :error="validationErrors.name ? true : false"
          :error-message="validationErrors.name ? validationErrors.name[0] : ''"
        />
      </div>
      <div class="q-mt-md">
        <q-input
          class="!tw-w-full"
          outlined
          v-model="email"
          label="E-mail"
          :dense="false"
          :error="validationErrors.email ? true : false"
          :error-message="validationErrors.email ? validationErrors.email[0] : ''"
        />
      </div>
      <div class="q-mt-md">
        <q-input
          class="!tw-w-full"
          label="Password"
          v-model="password"
          filled
          :type="isPwd ? 'password' : 'text'"
          :error="validationErrors.password ? true : false"
          :error-message="validationErrors.password ? validationErrors.password[0] : ''"
        >
          <template v-slot:append>
            <q-icon
              :name="isPwd ? 'visibility_off' : 'visibility'"
              class="cursor-pointer"
              @click="isPwd = !isPwd"
            />
          </template>
        </q-input>
      </div>
      <div class="q-mt-md">
        <q-input
          class="!tw-w-full"
          label="Password Confirmation"
          v-model="passwordConfirmation"
          filled
          :type="isPwdConfirm ? 'password' : 'text'"
          :error="validationErrors.password_confirmation ? true : false"
          :error-message="validationErrors.password_confirmation ? validationErrors.password_confirmation[0] : ''"
        >
          <template v-slot:append>
            <q-icon
              :name="isPwdConfirm ? 'visibility_off' : 'visibility'"
              class="cursor-pointer"
              @click="isPwdConfirm = !isPwdConfirm"
            />
          </template>
        </q-input>
      </div>
    </div>
    <div class="tw-mt-10">
      <div class="q-mt-md">
        <q-btn class="!tw-bg-blue-700 q-mt-md" label="Register" @click="handleSubmit"></q-btn>
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
