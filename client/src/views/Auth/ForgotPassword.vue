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
          <h1 class="text-3xl text-gray-800 mb-2">Forgot Password</h1>
          <p class="text-gray-600">
            Cosmopolitan Memorial Chapels Management System
          </p>
        </div>
      </div>
      <div class="mb-8 gap-6 w-full max-w-md bg-white rounded-2xl shadow">
        <v-form class="!p-6">
          <div class="space-y-2 mb-4">
            <label for="email" class="bold mb-2 block text-sm font-medium text-gray-700">Email</label>
            <div class="relative">
              <MailIcon class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
              <input
                v-model="email"
                label="Email"
                type="email"
                placeholder="Enter your email"
                class="w-full border border-gray-300 rounded-lg pl-10 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                required
              ></input>
            </div>
          </div>
          <v-alert
          v-if="message"
            dense
            :class="[
            '!p-3 mb-4 rounded-lg text-white text-center',
            messageStatus === 'success' ? '!bg-green-500' :
            messageStatus === 'error' ? '!bg-red-300' :
            'bg-gray-500'
            ]"
            >
            {{ message }}
          </v-alert>
          <button 
            type="submit"
            @click="submitForgotPassword"
            class="w-full bg-black text-white py-2 rounded-lg hover:bg-blue-700 transition"
            :disabled="loading"
          >
          <span v-if="!loading">Send Reset Link</span>
          <Loader2 v-else class="animate-spin h-5 w-5 text-white mx-auto" />
          </button>
        </v-form>
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
import { ref, computed } from "vue";
import axios from "axios";
import { useRoute } from "vue-router";
import { MailIcon, Loader2 } from "lucide-vue-next";

const email = ref("");
const message = ref("");
const messageStatus = ref("");
const loading = ref(false);

async function submitForgotPassword(e) {
  e.preventDefault();
  if (!email.value) return (message.value = "Please enter your email.", messageStatus.value = "error");
  loading.value = true;

  try {
    const res = await axios.post(
      `${import.meta.env.VITE_API_URL}/forgotPassword.php`,
      { email: email.value }
    );

    if (res.data.success) {
      messageStatus.value = 'success';
      message.value = "Password reset email sent. Please check your email";
    } else {
      console.log(res)
      messageStatus.value = 'error';
      message.value = res.data.message || "Error sending reset email.";
    }
  } catch (err) {
    messageStatus.value = 'error';
    message.value = "Server error. Try again later.";
  } finally {
    loading.value = false;
  }
}
</script>