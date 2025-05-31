<template>
  <div>
    <h2 class="text-2xl font-semibold mb-4">All Reservations</h2>
    <p>Displays all reservations for the Admin</p>
  </div>
  <div>
    <v-container>
      <v-data-table
        :headers="headers"
        :items="reservations"
        item-value="id"
        class="elevation-1"
      >
        <!-- Format Status -->
        <template v-slot:item.status="{ item }">
          <td :class="getStatusClass(item.status)">{{ item.status }}</td>
        </template>

        <!-- Actions based on status -->
        <template v-slot:item.actions="{ item }">
          <td>
            <v-btn
              v-if="item.status === 'pending'"
              color="blue"
              @click="proceedToPayment(item)"
            >
              Proceed to Payment
            </v-btn>
            <v-btn
              v-if="item.status === 'confirming payment'"
              color="green"
              @click="confirmPayment(item)"
            >
              Confirm Payment
            </v-btn>
          </td>
        </template>
      </v-data-table>
    </v-container>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

const headers = [
  { title: "Reservation ID", value: "id" },
  { title: "User ID", value: "user_id" },
  { title: "Price", value: "price" },
  { title: "Status", value: "status" },
  { title: "Actions", value: "actions", width: "200px" },
];

const reservations = ref([]);

const fetchReservations = async () => {
  try {
    const response = await axios.get(
      `${import.meta.env.VITE_API_URL}/getAllReservations.php`
    );
    if (response.data.success) {
      reservations.value = response.data.data;
    } else {
      console.error("Failed to fetch:", response.data.message);
    }
  } catch (err) {
    console.error("Error:", err);
  }
};

onMounted(() => {
  fetchReservations();
});

const getStatusClass = (status) => {
  return {
    'green--text': status === 'approved',
    'blue--text': status === 'confirming payment',
    'orange--text': status === 'waiting approval',
    'red--text': status === 'rejected' || status === 'pending'
  };
};

const proceedToPayment = async (item) => {
  try {
    const response = await axios.post(
      `${import.meta.env.VITE_API_URL}/updateReservationStatus.php`,
      {
        reservation_id: item.id,
        status: "waiting payment"
      }
    );
    if (response.data.success) {
      item.status = "waiting payment";
    }
  } catch (error) {
    console.error("Proceed to payment failed:", error);
  }
};

const confirmPayment = async (item) => {
  try {
    const response = await axios.post(
      `${import.meta.env.VITE_API_URL}/updateReservationStatus.php`,
      {
        reservation_id: item.id,
        status: "waiting approval"
      }
    );
    if (response.data.success) {
      item.status = "waiting approval";
    }
  } catch (error) {
    console.error("Confirm payment failed:", error);
  }
};
</script>