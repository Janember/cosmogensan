<template>
  <v-footer class="text-center d-flex flex-column ga-2 py-4" color="#133256">
    <div class="d-flex ga-3">
      <v-btn
        icon
        variant="text"
        href="https://facebook.com"
        target="_blank"
        aria-label="Facebook"
      >
        <v-icon size="24">mdi-facebook</v-icon>
      </v-btn>

      <v-btn
        icon
        variant="text"
        href="https://linkedin.com"
        target="_blank"
        aria-label="LinkedIn"
      >
        <v-icon size="24">mdi-linkedin</v-icon>
      </v-btn>

      <v-btn
        icon
        variant="text"
        href="https://instagram.com"
        target="_blank"
        aria-label="Instagram"
      >
        <v-icon size="24">mdi-instagram</v-icon>
      </v-btn>
    </div>

    <v-divider class="my-2" thickness="2" width="50"></v-divider>

    <div class="text-caption font-weight-regular opacity-60">
      Cosmopolitan Memorial Chapels is the specialists in family care and service in times of need. Since 1950, Cosmopolitan has been the company of choice for people from around the world who expect world class comfort and care.
    </div>

    <v-divider></v-divider>

    <div>
      <strong>&copy; {{ new Date().getFullYear() }} Philip Rafael G Leopando. All rights reserved.</strong>
    </div>
  </v-footer>
</template>