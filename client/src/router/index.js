import { createRouter, createWebHistory } from 'vue-router'
import Login from '../views/Auth/Login.vue'
import Register from '../views/Auth/Register.vue'
import VerifyAccount from '../views/Auth/VerifyAccount.vue'
import ForgotPassword from '../views/Auth/ForgotPassword.vue'
import ResetPassword from '../views/Auth/ResetPassword.vue'
import UserLayout from '../views/User/Layout.vue'
import UserDashboard from '../views/User/pages/Dashboard.vue'
import UserReservations from '../views/User/pages/Reservations.vue'
import UserTransactions from '../views/User/pages/Transactions.vue'
import UserReserve from '../views/User/pages/Reserve.vue'
import UserAccount from '../views/User/pages/Account.vue'
import UserNotifications from '../views/User/pages/Notifications.vue'
import StaffLayout from '../views/Staff/Layout.vue'
import StaffDashboard from '../views/Staff/pages/Dashboard.vue'
import StaffReserve from '../views/Staff/pages/Reserve.vue'
import StaffReservations from '../views/Staff/pages/Reservations.vue'
import StaffChapels from '../views/Staff/pages/Chapels.vue'
import StaffTransactions from '../views/Staff/pages/Transactions.vue'
import StaffAccount from '../views/Staff/pages/Account.vue'
import StaffNotifications from '../views/Staff/pages/Notifications.vue'
import AdminLayout from '../views/Admin/Layout.vue'
import AdminDashboard from '../views/Admin/pages/Dashboard.vue'
import AdminReserve from '../views/Admin/pages/Reserve.vue'
import AdminReservations from '../views/Admin/pages/Reservations.vue'
import AdminChapels from '../views/Admin/pages/Chapels.vue'
import AdminTransactions from '../views/Admin/pages/Transactions.vue'
import AdminAccount from '../views/Admin/pages/Account.vue'
import AdminNotifications from '../views/Admin/pages/Notifications.vue'
import CityPricing from '../views/Admin/pages/CityPricing.vue'

const routes = [
  { 
    path: '/login', 
    name: 'Login', 
    component: Login 
  },
  { 
    path: '/register', 
    component: Register 
  },
  { 
    path: '/', 
    redirect: '/login' 
  },
  {
    path: '/verifyaccount',
    name: 'VerifyAccount',
    component: VerifyAccount
  },
  {
    path: '/forgotpassword',
    name: 'ForgotPassword',
    component: ForgotPassword
  },
  {
    path: '/resetpassword',
    name: 'ResetPassword',
    component: ResetPassword
  },
  {
    path: '/User',
    component: UserLayout,
    meta: { requiresAuth: true, role: 0 },
    children: [
      { path: 'dashboard', component: UserDashboard },
      { path: 'reservations', component: UserReservations },
      { path: 'transactions', component: UserTransactions },
      { path: 'reserve', component: UserReserve },
      { path: 'account', component: UserAccount },
      { path: 'notifications', component: UserNotifications },
    ]
  },
  {
    path: '/Staff',
    component: StaffLayout,
    meta: { requiresAuth: true, role: 1 },
    children: [
      { path: 'dashboard', component: StaffDashboard},
      { path: 'reservations', component: StaffReservations},
      { path: 'reserve', component: StaffReserve},
      { path: 'chapels', component: StaffChapels},
      { path: 'transactions', component: StaffTransactions},
      { path: 'account', component: StaffAccount},
      { path: 'notifications', component: StaffNotifications}
    ]
  },
  {
    path: '/Admin',
    component: AdminLayout,
    meta: { requiresAuth: true, role: 2 },
    children: [
      { path: 'dashboard', component: AdminDashboard},
      { path: 'reserve', component: AdminReserve},
      { path: 'reservations', component: AdminReservations},
      { path: 'chapels', component: AdminChapels},
      { path: 'transactions', component: AdminTransactions},
      { path: 'account', component: AdminAccount},
      { path: 'notifications', component: AdminNotifications},
      { path: 'citypricing', component: CityPricing}
    ]
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const user = JSON.parse(localStorage.getItem('user'))
  const isLoggedIn = !!user

  if (to.meta.requiresAuth) {
    if (!isLoggedIn) {
      next('/login')
    } else if (user.hierarchy !== to.meta.role) {
      switch (user.hierarchy) {
        case 2: next('/Admin/dashboard'); break
        case 1: next('/Staff/dashboard'); break
        default: next('/User/dashboard')
      }
    } else {
      next()
    }
  } else if ((to.path === '/login' || to.path === '/register') && isLoggedIn) {
    switch (user.hierarchy) {
      case 2: next('/Admin/dashboard'); break
      case 1: next('/Staff/dashboard'); break
      default: next('/User/dashboard')
    }
  } else {
    next()
  }
})

export default router