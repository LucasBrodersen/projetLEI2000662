// src/interceptors/errorInterceptor.ts

import { AxiosInstance } from 'axios';
import { useToast } from 'vue-toastification';
import { useAuthStore } from '../store/authStore';
import router from '../router/index.ts'; // Import your router instance correctly

export const setupErrorInterceptor = (axiosInstance: AxiosInstance) => {
  axiosInstance.interceptors.response.use(
    (response) => response,
    (error) => {
      const authStore = useAuthStore();
      const toast = useToast();

      if (error.response) {
        if (error.response.status === 401) {
          const errorMessage = error.response.data?.message;
          if (errorMessage === undefined) {
            authStore.setAuthToken('');
            toast.error('Your session has expired. Please log in again.');
            router.push('/');
          } else {
            toast.error('Invalid credentials. Please try again.');
          }
        } else {
          toast.error('An error occurred. Please try again later.');
        }
      } else {
        toast.error('Network error. Please check your connection.');
      }

      return Promise.reject(error);
    }
  );
};
