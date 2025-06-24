// src/interceptors/index.ts

import { setupAuthInterceptor } from './authInterceptor';
import { setupErrorInterceptor } from './errorInterceptor';

export const setupInterceptors = (axiosInstance: any) => {
  setupAuthInterceptor(axiosInstance);
  setupErrorInterceptor(axiosInstance);
};
