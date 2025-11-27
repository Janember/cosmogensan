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
              class="flex items-center text-blue-600 hover:text-blue-800 underline hover:underline-offset-2 transition duration-150 gap-1"
            >
              <Eye class="w-4 h-4" />
              <span>RES-{{ item.id }}</span>
            </a>
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
      <div class="flex flex-col gap-4 bg-muted/50 rounded-lg !p-4">
        <div class="text-sm font-semibold mb-2">Price Breakdown</div>

        <div class="flex justify-between">
          <span>Casket Price:</span>
          <span>₱{{ convertToNumber(priceBreakdown?.casketPrice || 0) }}</span>
        </div>

        <div class="flex justify-between">
          <span>Address Price:</span>
          <span>₱{{ convertToNumber(priceBreakdown?.addressPrice || 0) }}</span>
        </div>

        <div class="flex justify-between">
          <span>Additional Features:</span>
          <span>₱{{ convertToNumber(priceBreakdown?.addOnsPrice || 0) }}</span>
        </div>

        <div class="flex justify-between" v-if="priceBreakdown?.discount > 0">
          <span>Discount ({{ priceBreakdown.discount }}%):</span>
          <span>-₱{{ convertToNumber((priceBreakdown.casketPrice + priceBreakdown.addressPrice + priceBreakdown.addOnsPrice) * 0.2) }}</span>
        </div>

        <div class="flex justify-between border-t border-gray-200 pt-2 font-semibold text-lg">
          <span>Total Price:</span>
          <span>₱{{ convertToNumber(priceBreakdown?.total || 0) }}</span>
        </div>

        <div class="flex items-center justify-between border-t border-gray-200 pt-3">
          <div class="text-xs text-muted-foreground">Remaining Balance</div>
          <div class="text-lg font-semibold text-gray-800">
            ₱{{ convertToNumber(selectedReservation.rem_balance) }}
          </div>
        </div>
      </div>
        <v-card-actions>
          <v-spacer />
          <v-btn color="primary" @click="printReservation">
            Print
          </v-btn>
          <v-btn color="primary" @click="detailsDialog = false">
            Close
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import { convertToNumber } from "../../../composables/globalfuncs";
import { Eye } from 'lucide-vue-next'

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
const casketPriceMap = ref({});
const deliveryPriceMap = ref({});

const fetchCasketPrices = async () => {
  try {
    const response = await axios.get(`${import.meta.env.VITE_API_URL}/getCaskets.php`);
    response.data.data.forEach(c => {
      casketPriceMap.value[c.id] = Number(c.package_price);
    });
  } catch (err) {
    console.error("Failed to fetch casket prices", err);
  }
};

const fetchDeliveryPrices = async () => {
  try {
    const response = await axios.get(`${import.meta.env.VITE_API_URL}/getDeliveryRates.php`);
    response.data.data.forEach(d => {
      deliveryPriceMap.value[d.place] = Number(d.price);
    });
  } catch (err) {
    console.error("Failed to fetch delivery prices", err);
  }
};

const priceBreakdown = computed(() => {
  if (!selectedReservation.value) return null;

  const casketPrice = casketPriceMap.value[selectedReservation.value.casket_id] || 0;
  const addressPrice = deliveryPriceMap.value[selectedReservation.value.city] || 0;
  const addOnsCount = selectedReservation.value.additional_features
    ? selectedReservation.value.additional_features.split(',').length
    : 0;
  const addOnsPrice = addOnsCount * 1000;

  let total = casketPrice + addressPrice + addOnsPrice;

  const discount = selectedReservation.value.discounted === 1 ? 0.8 : 1;
  total = total * discount;

  return {
    casketPrice,
    addressPrice,
    addOnsPrice,
    discount: discount < 1 ? 20 : 0, 
    total
  };
});

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
  fetchCasketPrices();
  fetchDeliveryPrices();
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

const printReservation = () => {
  if (!selectedReservation.value) return;

  const printContents = document.querySelector('.v-card').innerHTML;

  const printWindow = window.open('', '', 'height=800,width=600');
  printWindow.document.write('<html><head><title>Reservation Details</title>');
  printWindow.document.write('<style>body{font-family: Arial, sans-serif;} .text-muted-foreground{color:#6b7280;} .text-xs{font-size:0.75rem;} .text-lg{font-size:1.125rem;} .text-xl{font-size:1.25rem;} .mt-0\\.5{margin-top:0.125rem;} .font-semibold{font-weight:600;} .rounded-lg{border-radius:0.5rem;} .border{border:1px solid #e5e7eb;} .p-4{padding:1rem;} .grid{display:grid;grid-template-columns:repeat(2,1fr);gap:0.75rem;} .col-span-2{grid-column:span 2;} </style>');
  printWindow.document.write('</head><body>');
  printWindow.document.write(printContents);
  printWindow.document.write('</body></html>');

  printWindow.document.close();
  printWindow.focus();
  printWindow.print();
};

const showReservationDetails = (reservation) => {
  selectedReservation.value = reservation;
  detailsDialog.value = true;
};
</script>
