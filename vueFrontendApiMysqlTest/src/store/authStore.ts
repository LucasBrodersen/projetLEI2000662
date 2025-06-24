import { defineStore } from 'pinia';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    // Initialize authToken and userRole from localStorage, if available
    authToken: localStorage.getItem('authToken') as string | null,
    userRole: localStorage.getItem('userRole') as string | null, // Add userRole to the state
    userId: localStorage.getItem('userId') as string | null, // Add userRole to the state
  }),
  getters: {
    isAuthenticated: (state) => !!state.authToken,
    getUserRole: (state) => state.userRole, // Getter to access the user role
  },
  actions: {
    setAuthToken(token: string | null) {
      this.authToken = token;

      if (token) {
        localStorage.setItem('authToken', token);
      } else {
        localStorage.removeItem('authToken');
      }
    },
    setUserRole(role: string | null) {
      this.userRole = role;

      if (role) {
        localStorage.setItem('userRole', role);
      } else {
        localStorage.removeItem('userRole');
      }
    },
    setUserId(id: string | null) {
      this.userId = id;

      if (id) {
        localStorage.setItem('userId', id);
      } else {
        localStorage.removeItem('useruserIdRole');
      }
    },
    clearAuth() {
      this.authToken = null;
      this.userRole = null;
      this.userId = null;
      // Clear both token and role from localStorage when logging out
      localStorage.removeItem('authToken');
      localStorage.removeItem('userRole');
      localStorage.removeItem('userId');
    },
  },
});
