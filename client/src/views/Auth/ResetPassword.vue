<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center p-4">
    <div class="w-full max-w-md space-y-8">
      <div class="text-center space-y-6 mb-8">
        <div class="flex justify-center">
          <img
            src="/cosmopolitanLogo.png"
            alt="Cosmopolitan Memorial Chapels"
            class="h-20 w-auto mb-6"
          />
        </div>
        <div class="space-y-2">
          <h1 class="text-3xl text-gray-800 mb-2">Reset Password</h1>
          <p class="text-gray-600">
            Cosmopolitan Memorial Chapels Management System
          </p>
        </div>
      </div>
      <div class="!p-6 mb-8 flex flex-col gap-6 w-full max-w-md bg-white rounded-2xl shadow">
        <div class="space-y-2">
          <label for="new-password" class="block text-sm font-medium text-gray-700">New Password</label>
          <div class="relative">
            <LockIcon class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
            <input
              v-model="newPassword"
              id="new-password"
              type="password"
              placeholder="Enter your new password"
              class="w-full border border-gray-300 rounded-lg pl-10 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
              required
            />
          </div>
        </div>
        <div class="space-y-2">
          <label for="confirm-password" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
          <div class="relative">
            <LockIcon class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
            <input
              v-model="confirmPassword"
              id="confirm-password"
              type="password"
              placeholder="Confirm your new password"
              class="w-full border border-gray-300 rounded-lg pl-10 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
              required
            />
          </div>
          <p v-if="errorMessage" class="text-red-500 text-sm mt-1">{{ errorMessage }}</p>
        </div>

        <v-alert
          v-if="message"
            dense
            class="!bg-red-300 !p-3 mb-4 rounded-lg text-white text-center"
            >
            {{ message }}
        </v-alert>

        <button
          @click="resetPassword"
          :disabled="loading"
          class="w-full bg-black text-white py-2 rounded-lg hover:bg-blue-700 transition disabled:opacity-60"
        >
          <span v-if="!loading">Reset Password</span>
          <span v-else>Processing...</span>
        </button>
      </div>
      <div class="text-center space-y-4">
        <div class="text-sm text-gray-500 mb-4">
          <p>Locations: Manila • Cebu • Davao • Tagum • Cagayan • Iloilo</p>
          <p>General Santos • Iligan • Valencia</p>
        </div>
        <div class="text-xs text-gray-400">
          <p>© 2024 Cosmopolitan Memorial Chapels. All rights reserved.</p>
          <p>For technical support, contact IT department</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";
import { LockIcon } from "lucide-vue-next";

const route = useRoute();
const router = useRouter();

const token = route.query.token;
const newPassword = ref("");
const confirmPassword = ref("");
const message = ref("");
const errorMessage = ref("");
const loading = ref(false);

async function resetPassword() {
  errorMessage.value = "";
  message.value = "";

  if (!newPassword.value || !confirmPassword.value) {
    errorMessage.value = "Please fill out both fields.";
    return;
  }
  if (newPassword.value.length < 8) {
    errorMessage.value = "Password must be at least 8 characters long.";
    return;
  }
  if (newPassword.value !== confirmPassword.value) {
    errorMessage.value = "Passwords do not match.";
    return;
  }

  loading.value = true;
  try {
    const res = await axios.post(`${import.meta.env.VITE_API_URL}/resetPassword.php`, {
      token,
      new_password: newPassword.value,
    });
    message.value = res.data.message;

    if (res.data.success) {
      setTimeout(() => {
        router.push("/login?msg=reset-success");
      }, 1500);
    }
  } catch (e) {
    message.value = "An error occurred. Please try again.";
  } finally {
    loading.value = false;
  }
}
</script>
