<template>
  <div class="!space-y-6">
    <div>
      <h1 class="text-3xl font-semibold">Transactions</h1>
      <p class="text-muted-foreground">
        View your transaction history and payment records
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
            >
              {{ item.id }}
            </a>
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
  </div>

  <!-- Reservation Detail Dialog -->
  <v-dialog v-model="detailsDialog" max-width="512px" >
    <v-card class="w-full max-w-3xl sm:max-w-lg max-h-[90vh] !p-6 rounded-lg border shadow-lg overflow-y-auto !gap-4">
      <button
        @click="detailsDialog = false"
        class="absolute top-3 right-3 text-gray-500 hover:text-gray-700"
        aria-label="Close dialog"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
      <div data-slot="dialog-header" class="flex flex-col gap-2 sm:text-left">
        <h2 id="radix-:r7d:" data-slot="dialog-title" class="text-lg leading-none font-semibold">
          Reservation Details
        </h2>
        <p id="radix-:r7e:" data-slot="dialog-description" class="text-muted-foreground text-sm">
          View complete information about this reservation
        </p>
      </div>
      <div class="space-y-6">
        <div class="bg-muted/50 rounded-lg !p-4">
          <h4 class="text-sm text-muted-foreground mb-3">
            Service Details
          </h4>
          <div class="grid grid-cols-2 gap-x-6 gap-y-3">
            <div>
              <div class="text-xs text-muted-foreground">
                Reservation ID
              </div>
              <div class="mt-0.5">
                {{ selectedReservation.id }}
              </div>
            </div>
            <div>
              <div class="text-xs text-muted-foreground">
                Deceased Name
              </div>
              <div class="mt-0.5">
                {{ selectedReservation.deceased_name }}
              </div>
            </div>
            <div>
              <div class="text-xs text-muted-foreground">
                Start Date
              </div>
              <div class="mt-0.5">
                {{ formatDate(selectedReservation.start_date) }}
              </div>
            </div>
            <div>
              <div class="text-xs text-muted-foreground">
                End Date
              </div>
              <div class="mt-0.5">
                {{ formatDate(selectedReservation.end_date) }}
              </div>
            </div>
            <div>
              <div class="text-xs text-muted-foreground">
                Chapel ID
              </div>
              <div class="mt-0.5">
                {{ selectedReservation.chapel_id }}
              </div>
            </div>
            <div>
              <div class="text-xs text-muted-foreground">
                Casket ID
              </div>
              <div class="mt-0.5">
                {{ selectedReservation.casket_id }}
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="bg-muted/50 rounded-lg !p-4">
        <h4 class="text-sm text-muted-foreground mb-3">
          Casket Specifications
        </h4>
        <div class="grid grid-cols-2 gap-x-6 gap-y-3">
          <div>
            <div class="text-xs text-muted-foreground">
              Color
            </div>
            <div class="mt-0.5 flex items-center gap-2">
              <div class="w-4 h-4 rounded" :style="{ backgroundColor: selectedReservation.color }"></div>
              {{ selectedReservation.color }}
            </div>
          </div>
        <div>
          <div class="text-xs text-muted-foreground">
            Size
          </div>
          <div class="mt-0.5">
            {{ selectedReservation.size }}
          </div>
        </div>
        <div class="col-span-2">
          <div class="text-xs text-muted-foreground">
            Additional Features
          </div>
          <div class="mt-1.5 flex flex-wrap gap-1.5">
            <span
              v-for="(feature, index) in selectedReservation.additional_features.split(',')"
              :key="index"
              class="inline-flex items-center gap-1 rounded-md bg-secondary text-secondary-foreground px-2 py-0.5 text-xs font-medium"
            >
              {{ feature.trim() }}
            </span>
          </div>
        </div>
        <div class="col-span-2">
          <div class="text-xs text-muted-foreground">
            Special Instructions
          </div>
          <div class="mt-0.5">
            {{ selectedReservation.additional_instructions }}
          </div>
        </div>
      </div>
    </div>
    <div class="bg-muted/50 rounded-lg !p-4">
      <h4 class="text-sm text-muted-foreground mb-3">
        Retrieval Address &amp; Contact
      </h4>
      <div class="grid grid-cols-2 gap-x-6 gap-y-3">
        <div>
          <div class="text-xs text-muted-foreground">
            Contact Person
          </div>
          <div class="mt-0.5">
            {{ selectedReservation.user_name }}
          </div>
        </div>
        <div>
          <div class="text-xs text-muted-foreground">
            Phone
          </div>
          <div class="mt-0.5">
            {{ selectedReservation.phone }}
          </div>
        </div>
        <div class="col-span-2">
          <div class="text-xs text-muted-foreground">
            Email
          </div>
          <div class="mt-0.5">
            {{ selectedReservation.email }}
          </div>
        </div>
        <div class="col-span-2">
          <div class="text-xs text-muted-foreground">
            Address
          </div>
          <div class="mt-0.5">
            {{ selectedReservation.house_no }} {{ selectedReservation.street }}, {{ selectedReservation.barangay }}, {{ selectedReservation.city }}
          </div>
        </div>
      </div>
    </div>
    <div class="flex items-center justify-between bg-muted/50 rounded-lg !p-4">
      <div>
        <div class="text-xs text-muted-foreground">
          Total Price
        </div>
        <div class="mt-0.5 text-xl">
          ₱{{ convertToNumber(selectedReservation.price) }}
        </div>
      </div>
      <div class="text-right">
        <div class="text-xs text-muted-foreground mb-1.5">
          Status
        </div>
        <span
          data-slot="badge"
          class="inline-flex items-center justify-center rounded-md px-2 py-0.5 text-xs font-medium w-fit gap-1 transition"
          :class="selectedReservation.status === 'approved' 
            ? 'bg-green-600 text-green-50' 
            : 'bg-blue-600 text-blue-50'"
        >
          {{ selectedReservation.status }}
        </span>
      </div>
    </div>
      <v-card-actions>
        <v-spacer />
        <v-btn color="primary" @click="detailsDialog = false">
          Close
        </v-btn>
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