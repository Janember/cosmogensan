<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center p-4">
    <div class="w-full max-w-md !space-y-8">
      <div class="space-y-6 mb-8">
        <div class="text-center space-y-6 mb-8">
          <div class="flex justify-center">
            <img
              src="/cosmopolitanLogo.png"
              alt="Cosmopolitan Memorial Chapels"
              class="h-20 w-auto mb-6"
            />
          </div>
          <div class="space-y-2 text-center">
            <h1 class="text-3xl text-gray-800 mb-2">Registration Form</h1>
            <p class="text-gray-600">
              Cosmopolitan Memorial Chapels Management System
            </p>
          </div>
        </div>
        <div class="mb-8 gap-6 w-full max-w-md bg-white rounded-2xl shadow">
          <div class="space-y-1 text-center px-6 pt-6 mb-7">
            <h2 class="text-2xl font-semibold text-gray-800">Register</h2>
            <p class="text-gray-500 text-sm"></p>
          </div>
          <v-form @submit.prevent="handleRegister" class="px-6">
            <div class="space-y-1 mb-4">
              <label for="email" class="bold mb-2 block text-sm font-medium text-gray-700">Email</label>
              <div class="relative">
                <MailIcon class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
                <input
                  v-model="email"
                  label="Email"
                  type="text"
                  placeholder="Enter email"
                  class="w-full border border-gray-300 rounded-lg pl-10 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                  required
                ></input>
              </div>
            </div>
            <div class="space-y-1 mb-4">
              <label for="username" class="bold mb-2 block text-sm font-medium text-gray-700">Username</label>
              <div class="relative">
                <User class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
                <input
                  v-model="username"
                  label="Username"
                  type="text"
                  placeholder="Enter username"
                  class="w-full border border-gray-300 rounded-lg pl-10 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                  required
                ></input>
              </div>
            </div>
            <div class="space-y-4 mb-4 flex flex-col md:flex-row md:gap-4">
              <div class="flex-1 !space-y-2">
                <label for="firstName" class="block text-sm font-medium text-gray-700">First Name</label>
                <div class="relative">
                  <UserStar class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
                  <input
                    id="firstName"
                    v-model="firstName"
                    type="text"
                    placeholder="Enter first name"
                    class="w-full border border-gray-300 rounded-lg pl-10 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    required
                  />
                </div>
              </div>
              <div class="flex-1 !space-y-2">
                <label for="lastName" class="block text-sm font-medium text-gray-700">Last Name</label>
                <div class="relative">
                  <Users class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
                  <input
                    id="lastName"
                    v-model="lastName"
                    type="text"
                    placeholder="Enter last name"
                    class="w-full border border-gray-300 rounded-lg pl-10 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    required
                  />
                </div>
              </div>
            </div>
            <div class="space-y-2 mb-4">
              <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
              <div class="relative">
                <Phone class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
                <span class="absolute left-10 top-1/2 -translate-y-1/2 text-gray-500">+63</span>

                <input
                  id="phone"
                  v-model="phone"
                  type="text"
                  placeholder="9123456789"
                  class="w-full border border-gray-300 rounded-lg !pl-20 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                  @input="handlePhoneInput"
                  required
                />
              </div>
              <p class="text-xs text-gray-500 mt-1">Enter 10-digit mobile number without the leading zero.</p>
            </div>
            <div class="!space-y-2 mb-4">
              <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
              <div class="relative">
                <LockIcon class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
                <input
                  id="password"
                  v-model="password"
                  :type="showPassword ? 'text' : 'password'"
                  type="password"
                  placeholder="Enter your password"
                  class="w-full border border-gray-300 rounded-lg pl-10 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
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

            <div class="!space-y-2 mb-4">
              <label for="confirmPassword" class="block text-sm font-medium text-gray-700">Confirm Password</label>
              <div class="relative">
                <LockIcon class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
                <input
                  id="confirmPassword"
                  v-model="confirmPassword"
                  :type="showConfirmPassword ? 'text' : 'password'"
                  type="password"
                  placeholder="Confirm your password"
                  class="w-full border border-gray-300 rounded-lg pl-10 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                  required
                />
                <button
                  type="button"
                  @click="showConfirmPassword = !showConfirmPassword"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                >
                  <component :is="showConfirmPassword ? EyeOffIcon : EyeIcon" class="h-4 w-4" />
                </button>
              </div>
            </div>

    
            <button 
              type="submit" 
              class="w-full bg-black text-white py-2 rounded-lg hover:bg-blue-700 transition 
                    flex items-center justify-center gap-2 disabled:opacity-70"
              :disabled="loading"
            >
              <Loader2
                v-if="loading"
                class="animate-spin h-5 w-5 text-white"
              />
              <span v-if="!loading">Register</span>
            </button>

            <v-alert v-if="error" type="error" class="mt-4 mx-2" dense>
              {{ error }}
            </v-alert>
          </v-form>

          <div class="text-center py-4">
            <span>Already have an account? </span>
            <button variant="plain" text class="text-black-600 hover:underline" @click="goToLogin">Login</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import { EyeIcon, EyeOffIcon, LockIcon, MailIcon, User, UserStar, Users, Phone, Loader2 } from 'lucide-vue-next';

const username = ref("");
const firstname = ref("");
const lastname = ref("");
const email = ref("");
const password = ref("");
const confirmPassword = ref("");
const error = ref("");
const loading = ref(false);
const router = useRouter();
const showPassword = ref(false);
const showConfirmPassword = ref(false);
const phone = ref('')

const handlePhoneInput = (event) => {
  phone.value = event.target.value.replace(/\D/g, '')
  if (phone.value.length > 10) {
    phone.value = phone.value.slice(0, 10)
  }
}
const fullPhoneNumber = computed(() => {
  return phone.value ? `+63${phone.value}` : '+63'
})

const handleRegister = async () => {
  console.log(fullPhoneNumber.value);
  if (password.value !== confirmPassword.value) {
    error.value = "Passwords do not match";
    return;
  }
  if (phone.value.length < 10 || !phone.value.startsWith('9')){
    error.value = 'Phone number is invalid'
    return;
  }

  loading.value = true;
  error.value = "";

  try {
    const response = await axios.post(
      `${import.meta.env.VITE_API_URL}/register.php`,
      {
        username: username.value,
        firstname: firstname.value,
        lastname: lastname.value,
        email: email.value,
        phonenumber: fullPhoneNumber.value,
        password: password.value,
        confirm_password: confirmPassword.value,
      }
    );
    console.log(response.data)
    if (response.data.success) {
      router.push({ path: "/login", query: { registered: "true", msg: "registered"} });
    } else {
      error.value = response.data.message || "Registration failed";
    }
  } catch (err) {
    error.value = "Could not connect to the server";
  } finally {
    loading.value = false;
  }
};

const goToLogin = () => {
  router.push("/login");
};
</script>