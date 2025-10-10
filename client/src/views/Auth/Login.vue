<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center p-4">
    <div class="w-full max-w-md space-y-8">
      <div class="text-center space-y-6">
        <div class="flex justify-center">
          <img
            src="/cosmopolitanLogo.png"
            alt="Cosmopolitan Memorial Chapels"
            class="h-20 w-auto"
          />
        </div>
        <div class="space-y-2">
          <h1 class="text-3xl text-gray-800">Login Portal</h1>
          <p class="text-gray-600">
            Cosmopolitan Memorial Chapels Management System
          </p>
        </div>
      </div>
      <div class="w-full max-w-md bg-white rounded-2xl shadow !p-6">
        <div class="space-y-1 text-center mb-6">
          <h2 class="text-2xl font-semibold text-gray-800">Login</h2>
          <p class="text-gray-500 text-sm"></p>
        </div>

        <v-alert
          v-if="route.query.registered === 'true'"
          type="success"
          dense
          class="mb-4"
        >
          Registration successful! You may now log in.
        </v-alert>


        <v-form @submit.prevent="handleLogin">
          <div class="space-y-2">
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
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
          
          <div class="space-y-2">
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <div class="relative">
              <LockIcon class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
              <input
                v-model="password"
                label="Password"
                type="password"
                placeholder="Enter your password"
                class="w-full border border-gray-300 rounded-lg pl-10 pr-10 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                required
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
              >
                <component :is="showPassword ? EyeOffIcon : EyeIcon" class="h-4 w-4" />
              </button>
            </div>
          </div>
          
          <div class="flex justify-center items-center my-6">
            <vue-hcaptcha 
              sitekey="0e222aee-42b0-4ec2-849c-a2baf75af922"
              @verify="onVerify"
              @expired="onExpired"
              @error="onError"/>
          </div>

          <div class="flex items-center justify-between text-sm">
            <label class="flex items-center space-x-2">
              <input type="checkbox" id="remember" class="rounded border-gray-300" />
              <span>Remember me</span>
            </label>
            <a href="#" class="text-blue-600 hover:underline">Forgot password?</a>
          </div>
          
          <button 
            type="submit" 
            class="w-full bg-blue-600 !important text-white py-2 rounded-lg hover:bg-blue-700 transition"
            style="background-color: var(--color-blue-600) !important;"
          >
            Sign In
          </button>

          <v-alert v-if="error" type="error" class="mt-4" dense>
            {{ error }}
          </v-alert>
        </v-form>

        <div class="text-center mt-4">
          <span>Don't have an account?</span>
          <v-btn variant="plain" text color="primary" @click="goToRegister">Register</v-btn>
        </div>
      </div>
      <div class="text-center space-y-4">
        <div class="text-sm text-gray-500">
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
import { useRouter, useRoute } from "vue-router";
import axios from "axios";
import VueHcaptcha from '@hcaptcha/vue3-hcaptcha';
import { EyeIcon, EyeOffIcon, LockIcon, MailIcon } from 'lucide-vue-next';

const email = ref("");
const password = ref("");
const error = ref("");
const hcaptchaToken = ref(null);

const router = useRouter();
const route = useRoute();

const queryParams = route.query;

const handleLogin = async () => {

  if (!hcaptchaToken.value) {
    error.value = "Please complete the hCaptcha before logging in.";
    return;
  }

  try {
    const response = await axios.post(
      `${import.meta.env.VITE_API_URL}/login.php`,
      {
        email: email.value,
        password: password.value,
        hcaptcha_token: hcaptchaToken.value,
      }
    );

    if (response.data.status === 'success') {
      localStorage.setItem('user', JSON.stringify({
        id: response.data.user.id,
        username: response.data.user.username,
        email: response.data.user.email,
        hierarchy: response.data.user.hierarchy,
      }));
      
      router.push(`/${getDashboardRoute(response.data.user.hierarchy)}`);
    } else {
      error.value = response.data.message || "Login failed";
    }
  } catch (err) {
    error.value = "Could not connect to the server";
  }
};

const onVerify = (token) => {
  hcaptchaToken.value = token;
};

const onExpired = () => {
  hcaptchaToken.value = null;
  error.value = "hCaptcha expired. Please try again.";
};

const onError = () => {
  hcaptchaToken.value = null;
  error.value = "hCaptcha failed to load. Please refresh the page.";
};

const goToRegister = () => {
  router.push("/register");
};

const getDashboardRoute = (hierarchy) => {
  switch (hierarchy) {
    case 0:
      return "User/Dashboard"; 
    case 1:
      return "Staff/Dashboard"; 
    case 2:
      return "Admin/Dashboard"; 
    default:
      return "login";  
  }
};
</script>
