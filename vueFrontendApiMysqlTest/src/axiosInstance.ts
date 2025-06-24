// src/axiosInstance.ts

import axios from 'axios';
import { setupInterceptors } from './interceptors/index';
import router from './router/index'; // Import your router here

const axiosInstance = axios.create({
  baseURL: 'http://localhost:9000/api',
  //baseURL: 'https://stingray-app-qyek8.ondigitalocean.app/backend/api',
});

// Set up interceptors with the Axios instance
setupInterceptors(axiosInstance);

export default axiosInstance;
