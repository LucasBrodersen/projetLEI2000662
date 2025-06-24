<template>
  <div>
    <h2>Available Products</h2>
    <ul>
      <li v-for="product in products" :key="product.id">
        <h3 class="tw-mt-10">{{ product.name }}</h3>
        <p>{{ product.description }}</p>
        <div style="display: flex; justify-content: center; gap: 32px; margin-top: 16px;">
          <div v-for="price in product.prices" :key="price.id" style="text-align: center;">
            <img
              v-if="price.imagePrice"
              :src="price.imagePrice"
              alt="Price Image"
              style="max-width: 200px; margin-bottom: 8px;"
            />
            <div>
              <span>{{ price.nickname || 'Default' }} - {{ (price.unit_amount / 100).toFixed(2) }} {{ price.currency.toUpperCase() }}</span>
            </div>
            <q-btn
              class="tw-mt-3"
              @click="checkout(price.id, product.id)"
              color="primary"
              label="Buy"
              v-if="!activeSubscription"
            />
            <q-banner v-else class="bg-yellow-1 text-orange-10 tw-mt-3">
              You already have an active subscription.
            </q-banner>
          </div>
        </div>
      </li>
    </ul>
  </div>
</template>

<script>
import { loadStripe } from '@stripe/stripe-js';
import { useAuthStore } from '../store/authStore';

export default {
  data() {
    return {
      products: [],
      activeSubscription: null,
    };
  },
  methods: {
    async fetchProducts() {
      const authStore = useAuthStore();
      const token = authStore.authToken;

      try {
        const response = await fetch('http://localhost:9000/api/v1/products', {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
          },
        });

        const data = await response.json();
        console.log('products',data);
        this.products = data.map((item) => ({
          id: item.product.id,
          name: item.product.name,
          description: item.product.description,
          image: item.product.metadata?.image || null, // Adiciona imagem do produto
          prices: item.prices.data.map((price) => ({
            id: price.id,
            nickname: price.nickname,
            unit_amount: price.unit_amount,
            currency: price.currency,
            interval: price.recurring?.interval || 'one_time',
            imagePrice: price.metadata?.imagePrice || null, // Adiciona imagem do preço
          })),
        }));
      } catch (error) {
        console.error('Error fetching products:', error);
      }
    },

     async fetchActiveSubscription() {
      const authStore = useAuthStore();
      const token = authStore.authToken;
      const userId = authStore.userId;

      try {
        const response = await fetch(`http://localhost:9000/api/v1/users/${userId}?includeSubscription=true`, {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
          },
        });

        const data = await response.json();
        this.activeSubscription = data.data.activeSubscription || null;
        // Agora você pode usar this.activeSubscription para lógica de exibição ou bloqueio de compra
      } catch (error) {
        console.error('Error fetching active subscription:', error);
      }
    },

    async checkout(priceId, productId) {
      const stripe = await loadStripe(import.meta.env.VITE_STRIPE_PUBLIC_KEY);
      const authStore = useAuthStore();
      const token = authStore.authToken;

      try {
        const response = await fetch('http://localhost:9000/api/v1/create-checkout-session', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
          },
          body: JSON.stringify({ priceId, productId }),
        });

        const session = await response.json();
        
        if (session.id) {
          const { error } = await stripe.redirectToCheckout({ sessionId: session.id });
          if (error) {
            console.error('Error during Stripe checkout redirection:', error);
          }
        } else {
          console.error('Invalid session data received:', session);
        }
      } catch (error) {
        console.error('Error during checkout:', error);
      }
    },
  },
  mounted() {
    this.fetchProducts();
    this.fetchActiveSubscription(); // Chame aqui ao montar
  },
};
</script>