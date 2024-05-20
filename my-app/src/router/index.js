import { createRouter, createWebHistory } from 'vue-router'
import RegisterPage from '@/components/RegisterPage.vue'
import LoginPage from '@/components/LoginPage.vue'

const routes = [
  {
    path: '/',
    name: 'LoginView',
    component: LoginPage
  },
  {
    path:'/register',
    name:'register',
    component:RegisterPage
  }
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

export default router
