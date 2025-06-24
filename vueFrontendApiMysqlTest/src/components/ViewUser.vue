<template>
    <div>
      <h4>User Details</h4>
      <q-form @submit.prevent="handleSave">
        <q-input
        v-model="user.name"
        label="Name"
        :disable="isDisabled"
        outlined
        class="q-mb-md"
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
            :disable="isDisabled || !isAdmin"
            :error="validationErrors.enrolmentDate ? true : false"
            :error-message="validationErrors.enrolmentDate ? validationErrors.enrolmentDate[0] : ''"
            @click="openDatePicker"
            readonly
          />
          <q-dialog v-model="isDatePickerOpen">
            <q-date
              v-model="user.enrolmentDate"
              @update:model-value="onenrolmentDateChange"
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
            :disable="isDisabled || !isAdmin"
            :error="validationErrors.status ? true : false"
            :error-message="validationErrors.status ? validationErrors.status[0] : ''"
          />

        <div v-if="isDisabled" class="btn-container">
          <router-link :to="`/subscriptions/manage/${user.id}`" style="text-decoration: none;">
            <q-btn
              label="View Invoices"
              color="primary"
              class="q-mr-sm q-mt-2"
            />
          </router-link>
        </div>
      </div>

      <div v-if="user.activeSubscription" class="q-mb-md col-12">
        <q-banner
          v-if="user.activeSubscription.ends_at"
          class="bg-yellow-1 text-orange-10"
        >
          <div>
            <strong>Subscription scheduled for cancellation.</strong>
            <div>Plan: {{ user.activeSubscription.plan_name }}</div>
            <div>Status: {{ user.activeSubscription.stripe_status }}</div>
            <div>Start Date: {{ formatSubscriptionDate(user.activeSubscription.start_date) }}</div>
            <div>
              <strong>Ends At:</strong>
              {{ formatSubscriptionDate(user.activeSubscription.ends_at) }}
            </div>
          </div>
        </q-banner>
        <q-banner
          v-else
          class="bg-green-1 text-green-10"
        >
          <div>
            <strong>Active Subscription:</strong>
            <div>Plan: {{ user.activeSubscription.plan_name }}</div>
            <div>Status: {{ user.activeSubscription.stripe_status }}</div>
            <div>Start Date: {{ formatSubscriptionDate(user.activeSubscription.start_date) }}</div>
            <q-btn
              color="negative"
              label="Cancel Subscription"
              class="q-mt-md"
              @click="showCancelDialog = true"
              v-if="isDisabled"
            />
          </div>
        </q-banner>
      </div>
        <q-input
        v-model="user.email"
        label="Email"
        :disable="isDisabled"
        outlined
        class="q-mb-md"
        :error="validationErrors.email ? true : false"
        :error-message="validationErrors.email ? validationErrors.email[0] : ''"
        />

        <q-input
        v-model="user.address"
        label="Address"
        :disable="isDisabled"
        outlined
        class="q-mb-md"
        :error="validationErrors.address ? true : false"
        :error-message="validationErrors.address ? validationErrors.address[0] : ''"
        />
        <q-input
          v-model="user.state"
          label="State"
          :disable="isDisabled"
          outlined
          class="q-mb-md"
        />
        <q-input
          v-model="user.postalCode"
          label="Postal Code"
          :disable="isDisabled"
          outlined
          class="q-mb-md"
        />
        <q-select
          v-if="isAdmin"
          v-model="user.type"
          :options="userTypes"
          label="Type"
          :disable="isDisabled || !isAdmin"
          outlined
          class="q-mb-md"
        />
        <q-input
          v-model="password"
          autocomplete="new-password"  
          :type="isPwd ? 'password' : 'text'"
          label="Password"
          :disable="isDisabled"
          outlined
          class="q-mb-md"
          hint="Leave blank to keep current password"
          :error="validationErrors.password ? true : false"
          :error-message="validationErrors.password ? validationErrors.password[0] : ''"
        >
        <template v-slot:append>
            <q-icon :name="isPwd ? 'visibility_off' : 'visibility'" class="cursor-pointer" @click="isPwd = !isPwd" />
          </template>
        </q-input>
        <q-input
          v-model="passwordConfirmation"
          :type="isPwdConfirm ? 'password' : 'text'"
          label="Confirm Password"
          :disable="isDisabled"
          outlined
          class="q-mb-md"
          :error="validationErrors.passwordConfirmation ? true : false"
          :error-message="validationErrors.passwordConfirmation ? validationErrors.passwordConfirmation[0] : ''"
          >
          <template v-slot:append>
            <q-icon :name="isPwdConfirm ? 'visibility_off' : 'visibility'" class="cursor-pointer" @click="isPwdConfirm = !isPwdConfirm" />
          </template>
        </q-input>
        <q-banner v-if="passwordError" class="text-negative q-mb-md">
          Passwords do not match.
        </q-banner>
    
        <div>
          <!-- Show either Edit or Save button -->

          <div             v-if="isDisabled"
           class="btn-container">
           <q-btn
            label="Edit"
            color="primary"
            @click="isDisabled = false"
            class="q-mr-sm"
          />
            <q-btn class="!tw-bg-gray-500 q-mt-md" label="Back" @click="handleBack"></q-btn>
            
          </div>
          <q-btn
            v-else
            label="Save"
            color="positive"
            type="submit"
            class="q-mr-sm"
          />
          <q-btn
            v-if="!isDisabled"
            label="Cancel"
            color="negative"
            @click="cancelEdit"
          />
        </div>
      </q-form>
    </div>
    <q-dialog v-model="showCancelDialog">
        <q-card>
          <q-card-section>
            <div class="text-h6">Confirm Cancellation</div>
            <div>Are you sure you want to cancel your subscription?</div>
          </q-card-section>
          <q-card-actions align="right">
            <q-btn flat label="No" color="primary" v-close-popup />
            <q-btn flat label="Yes, Cancel" color="negative" @click="cancelSubscription" />
          </q-card-actions>
        </q-card>
      </q-dialog>
  </template>
  
  <script setup lang="ts">
  import { ref, onMounted, watch, computed } from 'vue';
  import { useRoute, useRouter } from 'vue-router';
  import axiosInstance from '../axiosInstance';
  import { useAuthStore } from '../store/authStore';
  import dayjs from 'dayjs'; // Import dayjs

  interface ValidationErrorResponse {
  errors: Record<string, string[]>; // This assumes errors are keyed by field name, with an array of error messages
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
const showCancelDialog = ref(false);

const isAdmin = computed(() => authStore.userRole === 'admin');

const cancelSubscription = async () => {
  showCancelDialog.value = false;
  try {
    const token = authStore.authToken;
    await axiosInstance.post(
      `/v1/subscriptions/${user.value.activeSubscription.stripe_id}/cancel`,
      { id: user.value.id }, // Adiciona o user.id no payload
      { headers: { Authorization: `Bearer ${token}` } }
    );
    // Optionally, refresh user data or update UI
    user.value.activeSubscription = null;
  } catch (error) {
    console.error('Error cancelling subscription:', error);
  }
};

  // Reactive references for user data and form state
  const user = ref({
  id: '',
  name: '',
  email: '',
  address: '',
  city: '',
  state: '',
  postalCode: '',
  type: '',
  password: '',
  passwordConfirmation: '',
  enrolmentDate: '', // Ensure it matches the YYYY-MM-DD format
  status: '',
});

const formattedEnrollmentDate = ref(''); // For displaying in DD/MM/YYYY format

// Watch the user.enrolmentDate to update the formatted date for display
watch(() => user.value.enrolmentDate, (newDate) => {
  if (newDate) {
    formattedEnrollmentDate.value = dayjs(newDate).format('DD/MM/YYYY');
  }
});

const statusOptions = ref(['active', 'inactive', 'suspended', 'pending']);


//Used just to formatting
const onenrolmentDateChange = (value: string) => {
  console.log("Enrollment Date changed to:", value);
  user.value.enrolmentDate = dayjs(value, 'DD/MM/YYYY').format('YYYY-MM-DD'); // Convert to YYYY-MM-DD before saving
  isDatePickerOpen.value = false; // Close the dialog
};

const openDatePicker = () => {
  console.log("entrou aqui");
  if (!isDisabled.value) {
    isDatePickerOpen.value = true;
  }
};

function formatSubscriptionDate(date: string) {
  return dayjs(date).format('DD/MM/YYYY');
}


  const isDisabled = ref(true); // Controls whether inputs are disabled
  const userTypes = ['admin', 'customer', 'partner']; // Types for q-select
  const password = ref(''); // Password input
  const passwordConfirmation = ref(''); // Password confirmation input
  const passwordError = ref(false); // Validation error state
  const isDatePickerOpen = ref(false); // Controls the dialog visibility

  // Route and Auth references
  const route = useRoute();
  const router = useRouter();
  const authStore = useAuthStore();
  
  const handleBack = () => {
    router.back(); // Navigate back to the previous route
  };
  

  // Fetch user details on component mount
  onMounted(async () => {
    try {
      const token = authStore.authToken;
      const response = await axiosInstance.get(`/v1/users/${route.params.id}?includeSubscription=true`, {
        headers: {
          Authorization: `Bearer ${token}`
        }
      });
      user.value = response.data.data; // Assign user details

      console.log('user.value');
      console.log('user.value', user.value)
    } catch (error) {
      console.error('Error fetching user details:', error);
    }
  });
  
  const validationErrors = ref<Record<string, string[]>>({});
    const handleSave = async () => {
  try {
    const token = authStore.authToken;

    // Include password and password_confirmation in user.value
    if (password.value) {
      user.value.password = password.value;
      user.value.passwordConfirmation = passwordConfirmation.value; // Changed from `passwordConfirmation` to `password_confirmation`
    }

    await axiosInstance.put(`/v1/users/${route.params.id}`, user.value, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    isDisabled.value = true; // Disable form after successful save
    validationErrors.value = {}; // Clear any previous errors

    // Reset password fields after successful save
    password.value = '';
    passwordConfirmation.value = '';
  } catch (error: unknown) {
    // Use type assertion to check if the error is an AxiosErrorWithResponse
    if (isAxiosErrorWithResponse(error)) {
      if (error.response && error.response.status === 422) {
        // Capture validation errors
        validationErrors.value = error.response.data.errors || {};
        console.error("Validation error:", validationErrors.value);
      } else {
        console.error("Error updating user:", error);
      }
    } else {
      console.error("An unknown error occurred:", error);
    }
  }
};

function isAxiosErrorWithResponse(error: unknown): error is AxiosErrorWithResponse {
  return (error as AxiosErrorWithResponse).response !== undefined;
}
  // Function to cancel the edit and revert changes
  const cancelEdit = async () => {
    validationErrors.value = {}
    try {
      // Fetch user data again to reset the form
      const token = authStore.authToken;
      const response = await axiosInstance.get(`/v1/users/${route.params.id}`, {
        headers: {
          Authorization: `Bearer ${token}`
        }
      });
      user.value = response.data.data; // Reset form to original values
      password.value = ''; // Clear password fields
      passwordConfirmation.value = '';
      isDisabled.value = true; // Disable the form
    } catch (error) {
      console.error('Error fetching user details:', error);
    }
  };
  </script>
  
  <style scoped>
  h4 {
    margin-bottom: 20px;
  }
  </style>
  