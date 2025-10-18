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
        item-value="id"
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
        <!-- Format Status -->
        <template v-slot:item.status="{ item }">
          <td :class="getStatusClass(item.status)">{{ item.status }}</td>
        </template>

        <!-- Actions based on status -->
        <template class="text-right" v-slot:item.actions="{ item }">
          <td>
            <v-btn
              v-if="item.status === 'pending'"
              color="blue"
              @click="proceedToPayment(item)"
              class="!m-1"
            >
              Proceed to Payment
            </v-btn>
            <v-btn
              v-if="item.status === 'confirming payment'"
              color="green"
              @click="confirmPayment(item)"
              class="!m-1"
            >
              Confirm Payment
            </v-btn>
            <v-btn 
              v-if="item.status !== 'rejected' && item.status !== 'approved'"
              color="red"
              @click="cancelReservation(item.id)"
              class="!m-1"
            >
              Cancel Reservation
            </v-btn>
          </td>
        </template>
      </v-data-table>
    </v-container>
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
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { convertToNumber } from "../../../composables/globalfuncs";


const formatDate = (dateString) => {
  const date = new Date(dateString);
  return `${date.getMonth() + 1}/${date.getDate()}/${date.getFullYear()}`;
};

const headers = [
  { title: "Reservation ID", value: "id" },
  { title: "Name", value: "deceased_name" },
  { title: "Start Date", value: "start_date" },
  { title: "End Date", value: "end_date" },
  { title: "Status", value: "status" },
  { title: "Actions", value: "actions", width: "200px", sortable: false },
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

const showReservationDetails = (reservation) => {
  selectedReservation.value = reservation;
  detailsDialog.value = true;
};
</script>
