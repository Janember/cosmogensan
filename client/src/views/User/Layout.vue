<template>
  <v-app>
    <v-navigation-drawer app expand-on-hover rail color="#133256" class="text-white" style="border-color: black; border-right-width: 2px">
      <!-- User Info -->
      <v-list>
        <v-list-item
          prepend-avatar="https://i.pinimg.com/736x/f1/0f/f7/f10ff70a7155e5ab666bcdd1b45b726d.jpg"
          :subtitle="email"
          :title="username"
        ></v-list-item>
      </v-list>

      <v-divider></v-divider>

      <!-- Navigation Links -->
      <v-list density="compact" nav>
        <v-list-item
          to="/User/Dashboard"
          prepend-icon="mdi-view-dashboard"
          title="Dashboard"
          value="dashboard"
          exact
          router
        ></v-list-item>
        <v-list-item
          to="/User/Reserve"
          prepend-icon="mdi-church"
          title="Reserve a Chapel"
          value="reserve"
          exact
          router
        ></v-list-item>
        <v-list-item
          to="/User/Reservations"
          prepend-icon="mdi-calendar-check"
          title="Reservations"
          value="reservations"
          exact
          router
        ></v-list-item>
        <v-list-item
          to="/User/Transactions"
          prepend-icon="mdi-bank-transfer"
          title="Transactions"
          value="transactions"
          exact
          router
        ></v-list-item>
        <v-list-item
          to="/User/Account"
          prepend-icon="mdi-account"
          title="Manage Account"
          value="account"
          exact
          router
        ></v-list-item>
      </v-list>

      <!-- Spacer and Logout Button -->
      <v-divider></v-divider>

      <v-list density="compact" nav>
        <v-list-item
          prepend-icon="mdi-logout"
          title="Logout"
          @click="handleLogout"
        ></v-list-item>
      </v-list>
    </v-navigation-drawer>

    <!-- Main Content Area -->
    <v-main>
      <div class="flex-1 ml-64 flex flex-col min-h-screen">
        <Header />
        <main class="p-6 flex-grow bg-gray-50" style="padding-left: 5%; padding-right: 5%; min-height: 76dvh">
          <router-view />
        </main>
        <Footer />
      </div>
    </v-main>
  </v-app>
</template>


<script setup>
import Header from './components/Header.vue'
import Footer from '../../components/Footer.vue'
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const username = ref('')
const email = ref('')

onMounted(() => {
  const user = JSON.parse(localStorage.getItem('user'))
  username.value = user ? user.username : 'Default User'
  email.value = user ? user.email : 'Default User'
})

const handleLogout = () => {
  localStorage.removeItem('user')
  router.push('/login')
}

if (!localStorage.getItem('user')) {
  router.push('/login') 
}
</script>
