<template>
  <div class="!space-y-6">
    <div>
      <h1 class="text-3xl font-semibold">Reservations</h1>
      <p class="text-muted-foreground">
        Manage all chapel bookings
      </p>
    </div>
    <v-container class="!p-0 mx-auto !shadow-none !bg-card !text-card-foreground !flex flex-col gap-6 !rounded-xl !border">
      <div class="flex items-center justify-between mb-4 gap-4">
        <v-select
          v-model="selectedChapel"
          :items="chapelOptions"
          label="Filter by Chapel"
          dense
          clearable
          outlined
        ></v-select>

        <v-btn color="green" @click="exportToExcel">
          Export to Excel
        </v-btn>
      </div>
      <v-data-table
        :headers="headers"
        :items="filteredReservations"
        item-value="id"
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

        <template v-slot:item.status="{ item }">
          <td :class="getStatusClass(item.status)">{{ item.status }}</td>
        </template>

        <template class="text-right" v-slot:item.actions="{ item }">
          <td class="flex flex-wrap gap-1">
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

            <v-btn
              v-if="item.status === 'pending' && item.id_uploaded_path && !item.discounted"
              color="purple"
              @click="openDiscountDialog(item)"
              class="!m-1"
            >
              Apply Discount
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
    <v-dialog v-model="discountDialog.show" max-width="516">
      <v-card>
        <v-card-title class="text-h6">Confirm Discount</v-card-title>
        <v-card-text class="flex flex-col items-center">
          <p>Apply discount for reservation RES-{{ discountDialog.item?.id }}?</p>
          <img
            v-if="discountDialog.item?.id_uploaded_path"
            :src="discountImageUrl"
            alt="ID Uploaded"
            class="max-w-full rounded-md shadow mt-4"
          />
        </v-card-text>
        <v-card-actions class="justify-end">
          <v-btn text @click="closeDiscountDialog">Cancel</v-btn>
          <v-btn color="purple" @click="confirmDiscount">Apply</v-btn>
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
import * as XLSX from "xlsx";


const formatDate = (dateString) => {
  const date = new Date(dateString);
  return `${date.getMonth() + 1}/${date.getDate()}/${date.getFullYear()}`;
};

const headers = [
  { title: "Reservation ID", value: "id", sortable: true },
  { title: "Customer", value: "deceased_name", sortable: true },
  { title: "Start Date", value: "start_date", sortable: true },
  { title: "End Date", value: "end_date", sortable: true },
  { title: "Chapel", value: "chapel_name", sortable: true },
  { title: "Status", value: "status", sortable: true },
  { title: "Actions", value: "actions", width: "200px", sortable: true },
];

const reservations = ref([]);
const selectedReservation = ref(null);
const detailsDialog = ref(false);
const selectedChapel = ref(null);
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
    discount: discount < 1 ? 20 : 0, // percentage
    total
  };
});

const discountDialog = ref({
  show: false,
  item: null
});

const openDiscountDialog = (item) => {
  discountDialog.value.item = item;
  discountDialog.value.show = true;
};

const closeDiscountDialog = () => {
  discountDialog.value.show = false;
  discountDialog.value.item = null;
};

const discountImageUrl = computed(() => {
  if (!discountDialog.value.item?.id_uploaded_path) return "";
  return `${import.meta.env.VITE_IMAGE_URL}/${discountDialog.value.item.id_uploaded_path}`;
});

const filteredReservations = computed(() => {
  if (!selectedChapel.value) return reservations.value;
  return reservations.value.filter(
    r => r.chapel_name === selectedChapel.value
  );
});

const chapelOptions = computed(() => {
  const unique = new Set(reservations.value.map(r => r.chapel_name));
  return Array.from(unique);
});

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
  fetchCasketPrices();
  fetchDeliveryPrices();
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
      const notifPayload = {
        title: "Payment Opened",
        message: `Your reservation ID: ${item.id} is now open for payment. Please pay the down payment within 5 days.`,
        type: "user",
        user_target: item.user_id,
        email: item.email,
      };

      await fetch(`${import.meta.env.VITE_API_URL}/createNotification.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(notifPayload),
      });
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
      const notifPayload = {
        title: "Reservation Cancelled",
        message: `Your reservation ID: ${item.id} has been cancelled by staff`,
        type: "user",
        user_target: item.user_id,
        email: item.email,
      };

      await fetch(`${import.meta.env.VITE_API_URL}/createNotification.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(notifPayload),
      });
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
      const notifPayload = {
        title: "Payment Confirmed",
        message: `Your reservation ID: ${item.id} payment has been confirmed, please wait for approval from staff`,
        type: "user",
        user_target: item.user_id,
        email: item.email,
      };

      await fetch(`${import.meta.env.VITE_API_URL}/createNotification.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(notifPayload),
      });

      notifPayload = {
        title: "Payment Confirmed",
        message: `reservation ID: ${item.id} payment has been confirmed, please approve when ready.`,
        type: "role",
        role_target: "staff",
      };

      await fetch(`${import.meta.env.VITE_API_URL}/createNotification.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(notifPayload),
      });

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

const confirmDiscount = async () => {
  if (!discountDialog.value.item) return;

  const item = discountDialog.value.item;
  const discountedPrice = item.price * 0.8;

  try {
    const response = await axios.post(`${import.meta.env.VITE_API_URL}/applyDiscount.php`, {
      reservation_id: item.id,
      new_price: discountedPrice
    });
    
    const notifPayload = {
      title: "Discount Applied",
      message: `Your reservation ID: ${item.id} has discount applied`,
      type: "user",
      user_target: item.user_id,
      email: item.email,
    };

    await fetch(`${import.meta.env.VITE_API_URL}/createNotification.php`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(notifPayload),
    });

    if (response.data.success) {
      item.price = discountedPrice; 
      alert('Discount applied successfully!');
      fetchReservations();
    } else {
      alert('Failed to apply discount');
    }
  } catch (err) {
    console.error(err);
    alert('Error applying discount');
  } finally {
    closeDiscountDialog();
  }
};

const printReservation = () => {
  if (!selectedReservation.value) return;

  // Clone the content of the dialog
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

const exportToExcel = () => {
  const dataToExport = filteredReservations.value.map(r => ({
    "Reservation ID": r.id,
    "Customer": r.deceased_name,
    "Start Date": formatDate(r.start_date),
    "End Date": formatDate(r.end_date),
    "Chapel": r.chapel_name,
    "Casket ID": r.casket_id,
    "Color": r.color,
    "Size": r.size,
    "Additional Features": r.additional_features,
    "Special Instructions": r.additional_instructions,
    "Contact Person": r.user_name,
    "Phone": r.phone,
    "Email": r.email,
    "Address": `${r.house_no} ${r.street}, ${r.barangay}, ${r.city}`,
    "Total Price": r.price,
    "Remaining Balance": r.rem_balance,
    "Status": r.status
  }));

  const worksheet = XLSX.utils.json_to_sheet(dataToExport);
  const workbook = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(workbook, worksheet, "Reservations");

  XLSX.writeFile(workbook, "Reservations.xlsx");
};
</script>
