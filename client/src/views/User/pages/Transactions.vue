<template>
  <div style="margin-bottom: 40px">
    <h2 class="text-2xl font-semibold mb-4">User Transactions</h2>
    <p>This is the main page for the user.</p>
  </div>
  <v-container>
    <v-data-table
      :headers="headers"
      :items="reservations"
      item-value="name"
      class="elevation-1"
    >
      <template v-slot:item.id="{ item }">
        <td>
          <a href="#" @click.prevent="showReservationDetails(item)">{{
            item.id
          }}</a>
        </td>
      </template>
      <template v-slot:item.transaction_id="{ item }">
        <td>
          <span v-if="item.transaction_id">{{ item.transaction_id }}</span>
          <span v-else class="text-gray-400">—</span>
        </td>
      </template>
      <template v-slot:item.price="{ item }">
        <td>₱ {{ convertToNumber(item.price) }}</td>
      </template>
      <template v-slot:item.date="{ item }">
        <td>{{ formatDate(item.date) }}</td>
      </template>
      <template v-slot:item.status="{ item }">
        <td :class="getStatusClass(item.status)">{{ item.status }}</td>
      </template>
      <template v-slot:item.actions="{ item }">
        <td>
          <v-btn
            color="blue"
            @click="processPayment(item)"
            v-if="item.status === 'waiting payment'"
          >
            <div v-if="loading" class="spinner">
              <div class="loader"></div>
            </div>
            <div v-else>Pay Balance</div>
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
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import axios from "axios";
import { convertToNumber } from "../../../composables/globalfuncs";

const selectedReservation = ref(null);
const detailsDialog = ref(false);
const headers = ref([
  { title: "Reservation ID", align: "start", key: "id", value: "id" },
  { title: "Transaction ID", value: "transaction_id" },
  { title: "Price", value: "price" },
  { title: "Status", value: "status" },
  { title: "Actions", value: "actions", width: "150px" },
]);
const reservations = ref([]);
const loading = ref(false);
const pollingInterval = 5000;
let transactionId = '';
let poller = null;

const fetchReservations = async () => {
   try {
     const user = JSON.parse(localStorage.getItem("user"));
     const userID = user.id;
     const response = await axios.get(`${ import.meta.env.VITE_API_URL}/getUserTransactions.php?user_id=${userID}` );
     if (response.data.success) {
      reservations.value = response.data.data;
    } else {
      console.error("Failed to fetch reservations:", response.data.message);
    }
  } catch (error) { console.error("Error fetching reservations:", error); } };


onMounted(() => {
  fetchReservations();
  poller = setInterval(checkPaymentStatus, pollingInterval);
});


const formatDate = (dateString) => {
  const date = new Date(dateString);
  return `${date.getMonth() + 1}/${date.getDate()}/${date.getFullYear()}`;
};

const getStatusClass = (status) => {
  return status === "confirmed" ? "green--text" : "red--text";
};

const showReservationDetails = (reservation) => {
  selectedReservation.value = reservation;
  detailsDialog.value = true;
};

const processPayment = async (item) => {
  loading.value = true;
  try {
    const res = await fetch(`${import.meta.env.VITE_API_URL}/createCheckoutSession.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ item })
    });
    
    const data = await res.json();
    transactionId = data.transaction_id;
    if (data.checkout_url) {
      window.open(data.checkout_url, '_blank');
    }
  } catch (error) {
    console.error("Payment Failed:", error);
  } finally {
    loading.value = false; 
  }
};

const checkPaymentStatus = async () => {
  try {
    const res = await fetch(`${import.meta.env.VITE_API_URL}/checkPaymentStatus.php?id=${transactionId}`);
    const data = await res.json();

    if (data.status === 'paid') {
      clearInterval(poller); 
      alert('Payment successful! Please refresh the page to update the status.');
    }
  } catch (error) {
    console.error('Error checking payment status:', error);
  }
};
</script>

<style>
.loader {
  border: 4px solid #f3f3f3;
  border-top: 4px solid #3498db;
  border-radius: 50%;
  width: 24px;
  height: 24px;
  animation: spin 1s linear infinite;
  margin-top: 10px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>