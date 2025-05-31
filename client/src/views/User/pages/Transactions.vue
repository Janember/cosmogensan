<template>
  <div style="margin-bottom: 40px">
    <h2 class="text-2xl font-semibold mb-4">User Reservations</h2>
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
            @click="openDialog(item)"
            v-if="item.status === 'waiting payment'"
          >
            Pay Balance
          </v-btn>
        </td>
      </template>
    </v-data-table>
  </v-container>

  <!-- Dialog for payment and downpayment -->
  <v-dialog v-model="dialog" max-width="500px">
    <v-card>
      <v-card-title class="headline">Make a Downpayment</v-card-title>
      <v-card-text>
        <v-form>
          <v-text-field
            v-model="gcashNumber"
            label="Enter your G-Cash number"
            outlined
            required
          ></v-text-field>
          <p><strong>Downpayment (50%):</strong> ₱{{ downpayment }}</p>
        </v-form>
      </v-card-text>
      <v-card-actions>
        <v-btn color="green" @click="generateReceipt">Generate Receipt</v-btn>
        <v-btn color="red" @click="dialog = false">Cancel</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>

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
import { ref, computed, onMounted } from "vue";
import axios from "axios";
import jsPDF from "jspdf";

const dialog = ref(false);
const gcashNumber = ref("");
const selectedReservation = ref(null);
const detailsDialog = ref(false);
const headers = ref([
  { title: "Reservation ID", align: "start", key: "id", value: "id" },
  { title: "Price", value: "price" },
  { title: "Status", value: "status" },
  { title: "Actions", value: "actions", width: "150px" },
]);
const reservations = ref([]);

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

const openDialog = (item) => {
  selectedReservation.value = item;
  dialog.value = true;
};

const downpayment = computed(() => {
  return (selectedReservation.value?.price * 0.5).toFixed(2);
});

// Generate PDF receipt
const generateReceipt = async () => {
  if (!gcashNumber.value) {
    alert("Please enter your G-Cash number.");
    return;
  }

  const doc = new jsPDF();
  doc.setFontSize(16);
  doc.text("Reservation Downpayment Receipt", 20, 20);
  doc.text(`Reservation ID: ${selectedReservation.value.id}`, 20, 30);
  doc.text(`Price: ₱${selectedReservation.value.price}`, 20, 40);
  doc.text(`Downpayment (50%): ₱${downpayment.value}`, 20, 50);
  doc.text(`G-Cash Number: ${gcashNumber.value}`, 20, 60);
  doc.text(`Date: ${new Date().toLocaleDateString()}`, 20, 70);

  doc.save(`Receipt_${selectedReservation.value.id}.pdf`);
  try {
    const response = await axios.post(
      `${import.meta.env.VITE_API_URL}/payBalance.php`,
      {
        reservation_id: selectedReservation.value.id,
        user_id: JSON.parse(localStorage.getItem("user")).id,
        downpayment: downpayment.value,
        status: "confirming payment",
      }
    );
    if (response.data.success) {
      selectedReservation.value.status = "confirming payment";
    } else {
      console.error(
        "Failed to update reservation status:",
        response.data.message
      );
    }
  } catch (error) {
    console.error("Error updating reservation status:", error);
  }

  dialog.value = false;
};
const showReservationDetails = (reservation) => {
  selectedReservation.value = reservation;
  detailsDialog.value = true;
};
</script>
