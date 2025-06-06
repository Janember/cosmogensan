<template>
  <div>
    <h2 class="text-2xl font-semibold mb-4">All Reservations</h2>
    <p>Displays all reservations for the Admin</p>
  </div>
  <div>
    <v-container>
      <v-data-table
        :headers="headers"
        :items="chapels"
        item-value="id"
        class="elevation-1"
      >
        <!-- Format Status -->
        <template v-slot:item.status="{ item }">
          <td :class="getStatusClass(item.status)">{{ item.status }}</td>
        </template>

      </v-data-table>
    </v-container>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

const headers = [
  { title: "Name", value: "name" },
  { title: "Status", value: "status" },
];

const chapels = ref([]);

const fetchChapels = async () => {
  try {
    const response = await axios.get(
      `${import.meta.env.VITE_API_URL}/getAllChapels.php`
    );
    if (response.data.success) {
      chapels.value = response.data.data;
    } else {
      console.error("Failed to fetch:", response.data.message);
    }
  } catch (err) {
    console.error("Error:", err);
  }
};

onMounted(() => {
  fetchChapels();
});

const getStatusClass = (status) => {
  return {
    "green--text": status === "available",
    "red--text": status === "not available",
  };
};
</script>
