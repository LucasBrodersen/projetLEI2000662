<script setup lang="ts">
import { ref, watch, onMounted } from 'vue';
import axiosInstance from '../axiosInstance';
import { useRouter } from 'vue-router';
import dayjs from 'dayjs';

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

const isPwd = ref(true);
const isPwdConfirm = ref(true);

// Reactive references for user data and form state
const user = ref({
  name: '',
  email: '',
  address: '',
  city: '',
  state: '',
  postal_code: '',
  type: '',
  password: '',
  password_confirmation: '',
  enrolment_date: '',
  status: '',
});

const formattedEnrollmentDate = ref(''); // For displaying in DD/MM/YYYY format
const isDatePickerOpen = ref(false); // Controls the dialog visibility

// Watch the user.enrolment_date to update the formatted date for display
watch(() => user.value.enrolment_date, (newDate) => {
  if (newDate) {
    formattedEnrollmentDate.value = dayjs(newDate).format('DD/MM/YYYY');
  }
});

onMounted(async () => {
  user.value.enrolment_date = new Date().toISOString().substr(0, 10); // Sets the enrollment date to today
  user.value.status = 'active'
});

// For validation errors
const validationErrors = ref<Record<string, string[]>>({});

// Options for the 'type' dropdown
const typeOptions = [
  { label: 'Administrador', value: 'admin' },
  { label: 'Parceiro', value: 'parceiro' },
  { label: 'Cliente', value: 'cliente' }
];

// Use router for navigation
const router = useRouter(); // Get the router instance

// Method to handle form submission
const handleSubmit = async () => {
  try {
    console.log('Registering user:', user.value);
    // Send POST request to register the user
    await axiosInstance.post('/register', user.value);
    
    // On success, clear validation errors
    validationErrors.value = {};

    // Redirect or do something on success
    router.push('/list-users'); // Replace with your desired route

  } catch (error: unknown) {
    // Use type assertion to check if the error is an AxiosErrorWithResponse
    if (isAxiosErrorWithResponse(error)) {
      if (error.response && error.response.status === 422) {
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

const statusOptions = ref(['active', 'inactive', 'suspended', 'pending']);

const openDatePicker = () => {
  isDatePickerOpen.value = true;
};

const onenrolment_dateChange = (value: string) => {
  console.log("Enrollment Date changed to:", value);
  user.value.enrolment_date = dayjs(value, 'DD/MM/YYYY').format('YYYY-MM-DD'); // Convert to YYYY-MM-DD before saving
  isDatePickerOpen.value = false; // Close the dialog
};

// Watch the user.enrolment_date to update the formatted date for display
watch(() => user.value.enrolment_date, (newDate) => {
  if (newDate) {
    formattedEnrollmentDate.value = dayjs(newDate).format('DD/MM/YYYY');
  }
});

// Method to handle back navigation
const goBack = () => {
  router.back(); // Navigate back to the previous route
};
</script>

<template>
  <div>
    <h4 class="tw-text-black">Register a new user</h4>
    <q-form @submit.prevent="handleSubmit">
      <q-input
        class="tw-mt-5"
        v-model="user.name"
        label="Name"
        outlined
        :error="validationErrors.name ? true : false"
        :error-message="validationErrors.name ? validationErrors.name[0] : ''"
      />

      <div class="q-gutter-sm row">
          <!-- Enrollment Date -->
          <q-input
            v-model="formattedEnrollmentDate"
            label="Enrollment Date"
            outlined
            class="q-mb-md col-2"
            :error="validationErrors.enrolment_date ? true : false"
            :error-message="validationErrors.enrolment_date ? validationErrors.enrolment_date[0] : ''"
            @click="openDatePicker"
            readonly
          />
          <q-dialog v-model="isDatePickerOpen">
            <q-date
              v-model="user.enrolment_date"
              @update:model-value="onenrolment_dateChange"
              minimal
            />
          </q-dialog>

          <!-- Status -->
          <q-select
            v-model="user.status"
            :options="statusOptions"
            label="Status"
            outlined
            class="q-mb-md col-2"
            :error="validationErrors.status ? true : false"
            :error-message="validationErrors.status ? validationErrors.status[0] : ''"
          />
      </div>
      
      <!-- Dropdown for 'Type' -->
      <q-select
        class="tw-mt-5"
        v-model="user.type"
        :options="typeOptions"
        label="Type"
        outlined
        emit-value
        :error="validationErrors.type ? true : false"
        :error-message="validationErrors.type ? validationErrors.type[0] : ''"
      />

      <q-input
        class="tw-mt-5"
        v-model="user.email"
        label="Email"
        type="email"
        outlined
        :error="validationErrors.email ? true : false"
        :error-message="validationErrors.email ? validationErrors.email[0] : ''"
      />

      <q-input
        autocomplete="new-password"
        class="tw-mt-5"
        label="Password"
        v-model="user.password"
        outlined
        :type="isPwd ? 'password' : 'text'"
        :error="validationErrors.password ? true : false"
        :error-message="validationErrors.password ? validationErrors.password[0] : ''"
      >
        <template v-slot:append>
          <q-icon :name="isPwd ? 'visibility_off' : 'visibility'" class="cursor-pointer" @click="isPwd = !isPwd" />
        </template>
      </q-input>

      <q-input
        autocomplete="new-password"
        class="tw-mt-5"
        label="Password Confirmation"
        v-model="user.password_confirmation"
        outlined
        :type="isPwdConfirm ? 'password' : 'text'"
        :error="validationErrors.password_confirmation ? true : false"
        :error-message="validationErrors.password_confirmation ? validationErrors.password_confirmation[0] : ''"
      >
        <template v-slot:append>
          <q-icon :name="isPwdConfirm ? 'visibility_off' : 'visibility'" class="cursor-pointer" @click="isPwdConfirm = !isPwdConfirm" />
        </template>
      </q-input>

      <q-input
        class="tw-mt-5"
        v-model="user.address"
        label="Address"
        outlined
        :error="validationErrors.address ? true : false"
        :error-message="validationErrors.address ? validationErrors.address[0] : ''"
      />

      <q-input
        class="tw-mt-5"
        v-model="user.city"
        label="City"
        outlined
        :error="validationErrors.city ? true : false"
        :error-message="validationErrors.city ? validationErrors.city[0] : ''"
      />

      <q-input
        class="tw-mt-5"
        v-model="user.state"
        label="State"
        outlined
        :error="validationErrors.state ? true : false"
        :error-message="validationErrors.state ? validationErrors.state[0] : ''"
      />

      <q-input
        class="tw-mt-5"
        v-model="user.postal_code"
        label="Postal Code"
        outlined
        :error="validationErrors.postal_code ? true : false"
        :error-message="validationErrors.postal_code ? validationErrors.postal_code[0] : ''"
      />

      <div class="btn-container">
        <q-btn class="tw-mt-5" label="Register" type="submit" color="primary" />
      </div>
    </q-form>
  </div>
</template>

<style scoped>
h4 {
  margin-bottom: 20px;
}
</style>