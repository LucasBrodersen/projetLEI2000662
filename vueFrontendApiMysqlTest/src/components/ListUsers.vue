<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import axiosInstance from '../axiosInstance';
import { useAuthStore } from '../store/authStore'; // Assuming your store path is correct
import { useRouter } from 'vue-router'

// Data for the q-table
const users = ref([]);
const showDeleteDialog = ref(false);
const userToDelete = ref(null); // Store the user to be deleted
const router = useRouter();

// Explicitly type columns array
interface QTableColumn {
  name: string;
  label: string;
  field: string | ((row: any) => any);
  required?: boolean;
  align?: 'left' | 'right' | 'center';
}

const filterValues = ref<Record<string, string>>({
  name: '',
  email: '',
  type: '',
  plan: ''
});

// Get authStore instance
const authStore = useAuthStore();

// Function to fetch users with optional filters
const fetchUsers = async () => {
  try {
    const token = authStore.authToken;
    
    // Manually construct the query string for filters
    let query = [];
    
    if (filterValues.value.email) {
      query.push(`email[like]=${encodeURIComponent(filterValues.value.email)}`);
    }
    if (filterValues.value.type) {
      query.push(`type[like]=${encodeURIComponent(filterValues.value.type)}`);
    }
    if (filterValues.value.name) {
      query.push(`name[like]=${encodeURIComponent(filterValues.value.name)}`);
    }
    if (filterValues.value.plan) {
      query.push(`planName[like]=${encodeURIComponent(filterValues.value.plan)}`);
    }
    
    const queryString = query.length ? `&${query.join('&')}` : '';
    
    // Fetch users with filters
    const response = await axiosInstance.get(`/v1/users?includeSubscription=true${queryString}`, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });

      // Access activeSubscription and the plan name
      const usersWithPlanName = response.data.data.map(user => {
      // Check if there's an active subscription and include the plan name
      if (user.activeSubscription) {
        user.planName = user.activeSubscription.plan_name; // Assuming `stripe_plan` is the field you need
      } else {
        user.planName = 'No Subscription'; // Or handle this case if no subscription exists
      }
      return user;
    });

    users.value = usersWithPlanName; // Assign the updated data to the table
    
    users.value = response.data.data; // Assign the users data to the table
    console.log('users.value', users.value);
  } catch (error) {
    console.error('Error fetching users:', error);
  }
};


// Fetch users on component mount
onMounted(() => {
  fetchUsers();
});

// Watch for changes in filterValues and refetch users
watch(filterValues, () => {
  console.log("filterValuesChanged", filterValues)
  fetchUsers();
}, { deep: true });  // Use deep: true to track changes inside the object

// Corrected columns definition for q-table
const columns: QTableColumn[] = [
  {
    name: 'name',
    label: 'Name',
    align: 'left',
    field: 'name',
    required: true,
  },
  {
    name: 'email',
    label: 'Email',
    align: 'left',
    field: 'email',
    required: true,
  },
  {
    name: 'type',
    label: 'Type',
    align: 'left',
    field: 'type',
    required: true,
  },
  {
    name: 'plan',
    label: 'Plan',
    align: 'left',
    field: 'planName',
    required: true,
  },
  {
    name: 'actions',
    label: 'Actions',
    align: 'center',
    field: '',
  }
];

// View user action
const handleView = (user: any) => {
  console.log('user', user);
  router.push({ path: `/view-user/${user.id}` });
};

// Prepare for deletion
const confirmDelete = (user: any) => {
  userToDelete.value = user.id; // Set user to be deleted
  showDeleteDialog.value = true; // Open the confirmation dialog
};

// Delete user action
const handleDelete = async () => {
  console.log('userToDelete', userToDelete.value);
  try {
    const token = authStore.authToken;
    await axiosInstance.delete(`/v1/users/${userToDelete.value}`, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });
    // Remove the user from the table after deletion
    users.value = users.value.filter((u: any) => u.id !== userToDelete.value);
    console.log('User deleted:', userToDelete.value);
  } catch (error) {
    console.error('Error deleting user:', error);
  } finally {
    showDeleteDialog.value = false; // Close the dialog
    userToDelete.value = null; // Reset the user to be deleted
  }
};
</script>

<template>
  <div>
    <h4>Welcome to List Users</h4>
    
    <!-- Quasar Q-Table -->
    <q-table
      :rows="users"
      :columns="columns"
      row-key="id"
      title="Users"
    >
      <template v-slot:header="props">
        <q-tr :props="props">
          <q-th
            v-for="col in props.cols"
            :key="col.name"
            :props="props"
          >
            {{ col.label }}
            <q-input
              v-if="col.name !== 'actions'"
              v-model="filterValues[col.name]"
              :placeholder="`Filter by ${col.label}`" 
              dense
              debounce="300"
            />
          </q-th>
        </q-tr>
      </template>
      <!-- Slot to add custom actions (view/delete buttons) -->
      <template v-slot:body-cell-actions="props">
        <td>
          <q-btn
            label="View"
            color="primary"
            size="sm"
            @click="handleView(props.row)"
            class="q-mr-sm"
          />
          <q-btn
            label="Delete"
            color="negative"
            size="sm"
            @click="confirmDelete(props.row)"
          />
        </td>
      </template>
    </q-table>

    <!-- Confirmation Dialog -->
    <q-dialog v-model="showDeleteDialog">
      <q-card>
        <q-card-section>
          <div class="text-h6">Confirm Deletion</div>
          <div>Are you sure you want to delete this user?</div>
        </q-card-section>
        <q-card-actions>
          <q-btn label="Cancel" color="grey" @click="showDeleteDialog = false" />
          <q-btn label="Delete" color="negative" @click="handleDelete" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<style scoped>
h4 {
  margin-bottom: 20px;
}
</style>
