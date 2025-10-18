<template>
  <div class="!space-y-6">
    <div>
      <h1 class="text-3xl font-semibold">Reservations</h1>
      <p class="text-muted-foreground">
        Manage all chapel bookings
      </p>
    </div>
    <v-container class="!p-0 mx-auto !shadow-none !bg-card !text-card-foreground !flex flex-col gap-6 !rounded-xl !border">
      <v-data-table
        :headers="headers"
        :items="reservations"
        item-value="name"
      >
        <template v-slot:item.id="{ item }">
          <td>
            <a
              href="#"
              @click.prevent="showReservationDetails(item)"
              class="text-blue-600 hover:text-blue-800 underline hover:underline-offset-2 transition duration-150"
            >{{
              item.id
            }}</a>
          </td>
        </template>

        <template v-slot:item.start_date="{ item }">
          <td>{{ formatDate(item.start_date) }}</td>
        </template>

        <template v-slot:item.end_date="{ item }">
          <td>{{ formatDate(item.end_date) }}</td>
        </template>
        <template v-slot:item.status="{ item }">
          <td :class="getStatusClass(item.status)">
            {{ item.status }}
          </td>
        </template>

        <template v-slot:item.actions="{ item }">
          <td>
            <v-btn
              color="red"
              @click="cancelReservation(item.id)"
              v-if="item.status !== 'rejected' && item.status !== 'approved'"
            >
              Cancel Reservation
            </v-btn>
          </td>
        </template>
      </v-data-table>
    </v-container>
    <!-- Reservation Detail Dialog -->
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
            <p><strong>Price:</strong> ₱{{ convertToNumber(selectedReservation.price) }}</p>
            <p><strong>Status:</strong> {{ selectedReservation.status }}</p>
          </div>
        </v-card-text>

        <v-card-actions>
          <v-spacer />
          <v-btn color="primary" @click="detailsDialog = false">Close</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { convertToNumber } from "../../../composables/globalfuncs";

const headers = ref([
  { title: "Reservation ID", align: "start", key: "id", value: "id" },
  { title: "Name", value: "deceased_name" },
  { title: "Start Date", value: "start_date" },
  { title: "End Date", value: "end_date" },
  { title: "Status", value: "status" },
  { title: "Actions", value: "actions", width: "150px" },
]);

const reservations = ref([]);
const selectedReservation = ref(null);
const detailsDialog = ref(false);

const fetchReservations = async () => {
  try {
    const user = JSON.parse(localStorage.getItem("user"));
    const userID = user.id;
    const response = await axios.get(
      `${
        import.meta.env.VITE_API_URL
      }/getUserReservations.php?user_id=${userID}`
    );
    if (response.data.success) {
      reservations.value = response.data.data;
    } else {
      console.error("Failed to fetch reservations:", response.data.message);
    }
  } catch (error) {
    console.error("Error fetching reservations:", error);
  }
};

onMounted(() => {
  fetchReservations();
});

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return `${date.getMonth() + 1}/${date.getDate()}/${date.getFullYear()}`;
};

const getStatusClass = (status) => {
  return status === "confirmed" ? "green--text" : "red--text";
};

const cancelReservation = async (reservationID) => {
  try {
    const response = await axios.post(
      `${import.meta.env.VITE_API_URL}/updateReservationStatus.php`,
      {
        reservation_id: reservationID,
        status: "rejected",
      }
    );

    if (response.data.success) {
      fetchReservations();
      alert("Reservation canceled successfully.");
    } else {
      alert("Failed to cancel reservation.");
    }
  } catch (error) {
    console.error("Error canceling reservation:", error);
    alert("An error occurred while canceling the reservation.");
  }
};

const showReservationDetails = (reservation) => {
  selectedReservation.value = reservation;
  detailsDialog.value = true;
};
</script>
