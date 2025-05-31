<template>
  <v-container class="d-flex align-center justify-center fill-height">
    <v-card elevation="8" width="400">
      <v-card-title class="text-h5 text-center">
        Create an Account
      </v-card-title>
      <v-card-text>
        <v-form @submit.prevent="handleRegister">
          <v-text-field
            v-model="username"
            label="Username"
            type="text"
            prepend-icon="mdi-account"
            required
          ></v-text-field>
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

          <v-text-field
            v-model="confirmPassword"
            label="Confirm Password"
            type="password"
            prepend-icon="mdi-lock-check"
            required
          ></v-text-field>

          <v-btn color="primary" type="submit" class="mt-4" block>
            Register
          </v-btn>

          <v-alert v-if="error" type="error" class="mt-4" dense>
            {{ error }}
          </v-alert>
        </v-form>

        <div class="text-center mt-4">
          <span>Already have an account?</span>
          <v-btn text color="primary" @click="goToLogin">Login</v-btn>
        </div>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const username = ref("");
const email = ref("");
const password = ref("");
const confirmPassword = ref("");
const error = ref("");
const router = useRouter();

const handleRegister = async () => {
  if (password.value !== confirmPassword.value) {
    error.value = "Passwords do not match";
    return;
  }

  try {
    const response = await axios.post(
      `${import.meta.env.VITE_API_URL}/register.php`,
      {
        username: username.value,
        email: email.value,
        password: password.value,
        confirm_password: confirmPassword.value,
      }
    );

    if (response.data.success) {
      router.push({ path: "/login", query: { registered: "true" } });
    } else {
      error.value = response.data.message || "Registration failed";
    }
  } catch (err) {
    error.value = "Could not connect to the server";
  }
};

const goToLogin = () => {
  router.push("/login");
};
</script>