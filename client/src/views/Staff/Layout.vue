<template>
  <v-app>
    <Sidebar
      :currentSection="currentSection"
      :onSectionChange="setCurrentSection"
      @logout="handleLogout" 
    />
    <!-- Main Content Area -->
    <v-main class="lg:!pl-80 bg-gray-100">
      <div class="flex-1 ml-64 flex flex-col min-h-screen">
        <main class="flex-1 overflow-y-auto">
          <div class="!p-6 bg-gray-100">
            <router-view />
          </div>
        </main>
      </div>
    </v-main>
  </v-app>
</template>


<script setup>
import Header from './components/Header.vue'
import Footer from '../../components/Footer.vue'
import Sidebar from './components/Sidebar.vue'
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const username = ref('')
const email = ref('')
const currentSection = ref('/Staff/dashboard')

onMounted(() => {
  const user = JSON.parse(localStorage.getItem('user'))
  username.value = user ? user.username : 'Default User'
  email.value = user ? user.email : 'Default User'
})

const handleLogout = () => {
  localStorage.removeItem('user')
  router.push('/login')
}

const setCurrentSection = (section) => {
  console.log(section);
  currentSection.value = section
}

if (!localStorage.getItem('user')) {
  router.push('/login') 
}
</script>
