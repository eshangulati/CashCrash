import { createRouter, createWebHistory } from 'vue-router'
import RegisterPage from '@/components/RegisterPage.vue'
import LoginPage from '@/components/LoginPage.vue'
import DashboardPage from '@/components/DashboardPage.vue'
import TransactionPage from '@/components/TransactionPage.vue'
import BudgetPage from '@/components/BudgetPage.vue'
import SavingsPage from '@/components/SavingsPage.vue'


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
  },
  {
    path:'/dashboard/:user_id',
    name: 'dashboard',
    component: DashboardPage
  },
  {
    path:'/transaction/:user_id',
    name:'transaction',
    component: TransactionPage
  },
  {
    path:'/budget/:user_id',
    name:'budget',
    component: BudgetPage
  },
  {
    path:'/savings/:user_id',
    name:'savings',
    component: SavingsPage
  }
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

export default router
