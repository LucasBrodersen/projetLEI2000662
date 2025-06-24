import { createApp } from 'vue';
import App from './App.vue';
import './style.css'
import router from './router';
import { createPinia } from 'pinia'; // Import Pinia
import './index.css'
import { Quasar } from 'quasar'
import Toast, { PluginOptions } from 'vue-toastification';
import 'vue-toastification/dist/index.css';
// Import icon libraries
import '@quasar/extras/material-icons/material-icons.css'


// Import Quasar css
import 'quasar/src/css/index.sass'


const app = createApp(App);

// Create a Pinia instance
const pinia = createPinia();

//Toast options
const options: PluginOptions = {
    // You can customize options here
  };

// Use Pinia with the app
app.use(pinia);
app.use(Quasar, {plugins: {},});
app.use(Toast, options); // Use the toast plugin

// Use the router
app.use(router);

// Mount the app
app.mount('#app');