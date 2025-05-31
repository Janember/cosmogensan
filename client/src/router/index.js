import { createRouter, createWebHistory } from 'vue-router'
import Login from '../views/Auth/Login.vue'
import Register from '../views/Auth/Register.vue'
import UserLayout from '../views/User/Layout.vue'
import UserDashboard from '../views/User/pages/Dashboard.vue'
import UserReservations from '../views/User/pages/Reservations.vue'
import UserTransactions from '../views/User/pages/Transactions.vue'
import UserReserve from '../views/User/pages/Reserve.vue'
import UserAccount from '../views/User/pages/Account.vue'
import StaffLayout from '../views/Staff/Layout.vue'
import StaffDashboard from '../views/Staff/pages/Dashboard.vue'
import StaffReservations from '../views/Staff/pages/Reservations.vue'
import AdminLayout from '../views/Admin/Layout.vue'
import AdminDashboard from '../views/Admin/pages/Dashboard.vue'
import AdminReservations from '../views/Admin/pages/Reservations.vue'
import test from '../views/User/pages/badger.vue'

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
    path: '/User',
    component: UserLayout,
    meta: { requiresAuth: true, role: 0 },
    children: [
      { path: 'dashboard', component: UserDashboard },
      { path: 'reservations', component: UserReservations },
      { path: 'transactions', component: UserTransactions },
      { path: 'reserve', component: UserReserve },
      { path: 'account', component: UserAccount },
      { path: 'test', component: test },
    ]
  },
  {
    path: '/Staff',
    component: StaffLayout,
    meta: { requiresAuth: true, role: 1 },
    children: [
      { path: 'dashboard', component: StaffDashboard},
      { path: 'reservations', component: StaffReservations},
    ]
  },
  {
    path: '/Admin',
    component: AdminLayout,
    meta: { requiresAuth: true, role: 2 },
    children: [
      { path: 'dashboard', component: AdminDashboard},
      { path: 'reservations', component: AdminReservations},
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