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
        <template v-slot:item.id="{ item }">
          <td>
            <a href="#" @click.prevent="showReservationDetails(item)">{{
              item.id
            }}</a>
          </td>
        </template>
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
  <v-dialog v-model="detailsDialog" max-width="700px">
    <v-card>
      <v-card-title class="text-h6 font-weight-bold"
        >Reservation Details</v-card-title
      >
      <v-card-text>
        <div v-if="selectedReservation">
          <h4 class="font-semibold mb-2">Details:</h4>
          <p><strong>ID:</strong> {{ selectedReservation.id }}</p>
          <p>
            <strong>Deceased Name:</strong>
            {{ selectedReservation.deceased_name }}
          </p>
          <p>
            <strong>Start Date:</strong>
            {{ formatDate(selectedReservation.start_date) }}
          </p>
          <p>
            <strong>End Date:</strong>
            {{ formatDate(selectedReservation.end_date) }}
          </p>
          <p><strong>Casket ID:</strong> {{ selectedReservation.casket_id }}</p>
          <p><strong>Chapel ID:</strong> {{ selectedReservation.chapel_id }}</p>
          <p><strong>Color:</strong> {{ selectedReservation.color }}</p>
          <p><strong>Size:</strong> {{ selectedReservation.size }}</p>
          <p>
            <strong>Additional Features:</strong>
            {{ selectedReservation.additional_features }}
          </p>
          <p>
            <strong>Instructions:</strong>
            {{ selectedReservation.additional_instructions }}
          </p>

          <h4 class="font-semibold mt-4 mb-2">
            Retrieval Address and Contact Info:
          </h4>
          <p><strong>City:</strong> {{ selectedReservation.city }}</p>
          <p><strong>Barangay:</strong> {{ selectedReservation.barangay }}</p>
          <p><strong>Street:</strong> {{ selectedReservation.street }}</p>
          <p><strong>House No.:</strong> {{ selectedReservation.house_no }}</p>
          <p><strong>User Name:</strong> {{ selectedReservation.user_name }}</p>
          <p><strong>Email:</strong> {{ selectedReservation.email }}</p>
          <p><strong>Phone:</strong> {{ selectedReservation.phone }}</p>

          <h4 class="font-semibold mt-4 mb-2">Status:</h4>
          <p><strong>Price:</strong> ₱{{ selectedReservation.price }}</p>
          <p><strong>Status:</strong> {{ selectedReservation.status }}</p>
        </div>
      </v-card-text>

      <v-card-actions>
        <v-spacer />
        <v-btn color="primary" @click="detailsDialog = false">Close</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
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
const selectedReservation = ref(null);
const detailsDialog = ref(false);

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
    "green--text": status === "approved",
    "blue--text": status === "confirming payment",
    "orange--text": status === "waiting approval",
    "red--text": status === "rejected" || status === "pending",
  };
};

const proceedToPayment = async (item) => {
  try {
    const response = await axios.post(
      `${import.meta.env.VITE_API_URL}/updateReservationStatus.php`,
      {
        reservation_id: item.id,
        status: "waiting payment",
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
        status: "waiting approval",
      }
    );
    if (response.data.success) {
      item.status = "waiting approval";
    }
  } catch (error) {
    console.error("Confirm payment failed:", error);
  }
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return `${date.getMonth() + 1}/${date.getDate()}/${date.getFullYear()}`;
};

const showReservationDetails = (reservation) => {
  console.log(reservation)
  selectedReservation.value = reservation;
  detailsDialog.value = true;
};
</script>
