import { createRouter, createWebHistory } from 'vue-router';
import LoginPage from '../components/LoginPage.vue';
import RegisterPage from '../components/RegisterPage.vue';
import RegisterUser from '../components/RegisterUser.vue';
import ViewUser from '../components/ViewUser.vue';
import viewSubscription from '../components/viewSubscription.vue';
import Checkout from '../components/Checkout.vue';
import WelcomePage from '../components/WelcomePage.vue';
import ForgotPasswordPage from '../components/ForgotPasswordPage.vue';
import ResetPasswordPage from '../components/ResetPasswordPage.vue';
import ListUsers from '../components/ListUsers.vue';
import { useAuthStore } from '../store/authStore';
import AuthLayout from '../components/AuthLayout.vue';
import MainLayout from '../components/MainLayout.vue';
import PostsAdmin from '../components/PostsAdmin.vue';

const routes = [
  {
    path: '/',
    component: AuthLayout,
    children: [
      { path: '', component: LoginPage },
      { path: 'register', component: RegisterPage },
      { path: '/forgot-password', component: ForgotPasswordPage },
      { path: '/reset-password', component: ResetPasswordPage } // No route params
      
    ],
  },
  {
    path: '/welcome-page',
    component: MainLayout,
    meta: { requiresAuth: true },
    children: [
      { path: '', component: WelcomePage },
      { path: '/list-users', component: ListUsers, meta: { requiresAuth: true, allowedRoles: ['admin', 'parceiro'] } },
      { path: '/register-user', component: RegisterUser, meta: { requiresAuth: true, allowedRoles: ['admin'] } },
      { path: '/view-user/:id', component: ViewUser, meta: { requiresAuth: true, allowedRoles: ['admin', 'cliente'] } },
      { path: '/subscriptions/manage/:id', component: viewSubscription, meta: { requiresAuth: true, allowedRoles: ['admin', 'cliente'] } },
      { path: '/checkout', component: Checkout, meta: { requiresAuth: true, allowedRoles: ['admin'] } },
      { path: '/Posts', component: PostsAdmin, meta: { requiresAuth: true, allowedRoles: ['admin'] } },
      { path: '/subscriptions', component: Checkout, meta: { requiresAuth: true, allowedRoles: ['cliente', 'admin'] } },
    ],
  },
  // Add other routes here
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Navigation guard for authentication and role checks
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();
  const token = authStore.isAuthenticated;
  const userRole = authStore.userRole;

  // If the route requires authentication
  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (!token) {
      next({ path: '/' });
    } else {
      const allowedRoles = to.meta.allowedRoles;
      if (Array.isArray(allowedRoles) && !allowedRoles.includes(userRole)) {
        // Redirect to a "forbidden" page or back to a safe route if user lacks the required role
        next({ path: '/forbidden' });
      } else {
        next();
      }
    }
  } else {
    next();
  }
});


export default router;
