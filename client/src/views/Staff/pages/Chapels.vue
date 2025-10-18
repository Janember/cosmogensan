<template>
  <div class="!space-y-6">
    <div>
      <h1 class="text-3xl font-semibold">Chapels</h1>
      <p class="text-muted-foreground">
        Manage all chapel status
      </p>
    </div>
    <div>
      <v-container class="!p-0 mx-auto !shadow-none !bg-card !text-card-foreground !flex flex-col gap-6 !rounded-xl !border">
        <v-data-table
          :headers="headers"
          :items="chapels"
          item-value="id"
        >
          <!-- Format Status -->
          <template v-slot:item.status="{ item }">
            <td :class="getStatusClass(item.status)">{{ item.status }}</td>
          </template>

          <!-- Actions based on status -->
          <template v-slot:item.actions="{ item }">
            <td>
              <v-btn
                v-if="item.status === 'available'"
                color="red"
                @click="updateChapelStatus(item)"
              >
                UPDATE TO "NOT AVAILABLE"
              </v-btn>
              <v-btn
                v-if="item.status === 'not available'"
                color="blue"
                @click="updateChapelStatus(item)"
              >
                UPDATE TO "AVAILABLE"
              </v-btn>
            </td>
          </template>
        </v-data-table>
      </v-container>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

const headers = [
  { title: "Name", value: "name" },
  { title: "Status", value: "status" },
  { title: "Actions", value: "actions", width: "200px" },
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

const updateChapelStatus = async (item) => {
  try {
    const temp_status = item.status === 'available' ? 'not available' : 'available';

    const response = await axios.post(
      `${import.meta.env.VITE_API_URL}/updateChapelStatus.php`,
      {
        chapel_id: item.id,
        status: temp_status,
      }
    );

    if (response.data.success) {
      item.status = temp_status;
    } else {
      console.error("Failed to update chapel status:", response.data.message);
    }

  } catch (error) {
    console.error("Chapel status update failed:", error);
  }
};
</script>
