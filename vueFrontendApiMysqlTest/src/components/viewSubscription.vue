<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axiosInstance from '../axiosInstance';
import { useAuthStore } from '../store/authStore';

const route = useRoute();
const authStore = useAuthStore();
const invoices = ref([]);
const loading = ref(true);
const error = ref('');
const router = useRouter();


const userId = route.params.id;

const invoiceColumns = [
  { name: 'id', label: 'ID', field: 'id', align: 'left' },
  { name: 'amount_due', label: 'Amount Due', field: 'amount_due', align: 'left' },
  { name: 'amount_paid', label: 'Amount Paid', field: 'amount_paid', align: 'left' },
  { name: 'currency', label: 'Currency', field: 'currency', align: 'left' },
  { name: 'status', label: 'Status', field: 'status', align: 'left' },
  { name: 'invoice_pdf', label: 'PDF', field: 'invoice_pdf', align: 'left' },
  { name: 'hosted_invoice_url', label: 'Hosted URL', field: 'hosted_invoice_url', align: 'left' },
  { name: 'created_at', label: 'Created At', field: 'created_at', align: 'left' }
];

const fetchInvoices = async () => {
  loading.value = true;
  error.value = '';
  try {
    const token = authStore.authToken;
    const response = await axiosInstance.get(`/v1/users/${userId}/invoices`, {
      headers: { Authorization: `Bearer ${token}` }
    });
    invoices.value = response.data;
  } catch (err) {
    error.value = 'Erro ao buscar invoices.';
    invoices.value = [];
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchInvoices();
});

  const handleBack = () => {
    router.back(); // Navigate back to the previous route
  };
  
</script>

<template>
  <div>
    <h4>Minhas Faturas</h4>
    <q-table
      v-if="!loading && invoices.length"
      :rows="invoices"
      :columns="invoiceColumns"
      row-key="id"
      title="Invoices"
    >
      <template v-slot:body-cell-invoice_pdf="props">
        <td>
          <a :href="props.row.invoice_pdf" target="_blank">PDF</a>
        </td>
      </template>
      <template v-slot:body-cell-hosted_invoice_url="props">
        <td>
          <a :href="props.row.hosted_invoice_url" target="_blank">Link</a>
        </td>
      </template>
    </q-table>
    <q-banner v-else-if="!loading && !invoices.length" class="bg-red-1 text-negative q-mt-md">
      Nenhuma fatura encontrada para este usuário.
    </q-banner>
    <q-banner v-else-if="error" class="bg-red-1 text-negative q-mt-md">
      {{ error }}
    </q-banner>
    <q-spinner v-else color="primary" size="2em" />
  </div>
  <q-btn class="!tw-bg-gray-500 q-mt-md" label="Back" @click="handleBack"></q-btn>

</template>

<style scoped>
h4 {
  margin-bottom: 20px;
}
</style>