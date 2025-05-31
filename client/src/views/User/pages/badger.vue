<template>
  <v-btn
            block
            prepend-icon="mdi-church"
            height="56"
            variant="tonal"
            @click="badger = true"
          ></v-btn>
          <v-dialog v-model="badger" max-width="700">
            <v-card>
                badger
            </v-card>
          </v-dialog>
</template>

<script>
</script>