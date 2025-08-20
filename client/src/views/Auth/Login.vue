<template>
  <v-container class="d-flex align-center justify-center fill-height">
    <v-card elevation="8" width="400">
      <v-card-title class="text-h5 text-center"> Welcome Back </v-card-title>
      <v-card-text>
        <v-alert
          v-if="route.query.registered === 'true'"
          type="success"
          dense
          class="mb-4"
        >
          Registration successful! You may now log in.
        </v-alert>
        <v-form @submit.prevent="handleLogin">
          <v-text-field
            v-model="email"
            label="Email"
            type="email"
            prepend-icon="mdi-email"
            required
          ></v-text-field>

          <v-text-field
            v-model="password"
            label="Password"
            type="password"
            prepend-icon="mdi-lock"
            required
          ></v-text-field>


          <v-btn color="primary" type="submit" class="mt-4" block>
            Login
          </v-btn>

          <v-alert v-if="error" type="error" class="mt-4" dense>
            {{ error }}
          </v-alert>
        </v-form>

        <div class="text-center mt-4">
          <span>Don't have an account?</span>
          <v-btn text color="primary" @click="goToRegister">Register</v-btn>
        </div>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script setup>
import { ref } from "vue";
import { useRouter, useRoute } from "vue-router";
import axios from "axios";

const email = ref("");
const password = ref("");
const error = ref("");

const router = useRouter();
const route = useRoute();

const queryParams = route.query;

const handleLogin = async () => {
  try {
    const response = await axios.post(
      `${import.meta.env.VITE_API_URL}/login.php`,
      {
        email: email.value,
        password: password.value,
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
