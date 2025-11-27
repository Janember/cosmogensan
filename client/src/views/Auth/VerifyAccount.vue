<template>
  <div class="flex min-h-screen items-center justify-center bg-blue-50">
    <div class="bg-white p-8 rounded-xl shadow w-full max-w-md text-center space-y-4">
      <h1 class="text-2xl font-semibold">Verifying your account...</h1>
      <div class="flex justify-center py-6">
        <Loader2 class="animate-spin h-8 w-8 text-blue-600" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import { Loader2 } from 'lucide-vue-next'

const route = useRoute()
const router = useRouter()

onMounted(async () => {
  const token = route.query.token
  if (!token) {
    console.log("no token")
    router.push({ path: '/login', query: { msg: 'invalid' } })
    return
  }

  try {
    const response = await axios.get(`${import.meta.env.VITE_API_URL}/verifyAccount.php?token=${token}`)
    if (response.data.success) {
      router.push({ path: '/login', query: { msg: 'verified' } })
    } else {
      console.log(response)
      router.push({ path: '/login', query: { msg: 'invalid' } })
    }
  } catch (err) {
    router.push({ path: '/login', query: { msg: 'error' } })
  }
})
</script>