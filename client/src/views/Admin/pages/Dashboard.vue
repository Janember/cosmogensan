<template>
  <div>
    <h2 class="text-2xl font-semibold mb-4">Admin Dashboard</h2>
    <p>This is the main page for the Admin.</p>
  </div>
  <v-row>
    <v-col cols="12" md="3">
      <v-card title="Total Reservations" height="250px">
        <div style="padding-left: 10%">
          {{ totalReservations }}
        </div>
      </v-card>
    </v-col>
    <v-col cols="12" md="3">
      <v-card subtitle="" title="Status" height="250px">
        <div style="padding-left: 10%">
          Approved:
          <span style="color: green; font-weight: bold">{{
            statusCounts.approved
          }}</span>
        </div>
        <div style="padding-left: 10%">
          Waiting Approval:
          <span style="color: yellowgreen; font-weight: bold">{{
            statusCounts.waiting_approval
          }}</span>
        </div>
        <div style="padding-left: 10%">
          Confirming Payment:
          <span style="color: orange; font-weight: bold">{{
            statusCounts.confirming_payment
          }}</span>
        </div>
        <div style="padding-left: 10%">
          Waiting Payment:
          <span style="color: orange; font-weight: bold">{{
            statusCounts.waiting_payment
          }}</span>
        </div>
        <div style="padding-left: 10%">
          Pending:
          <span style="color: orangered; font-weight: bold">{{
            statusCounts.pending
          }}</span>
        </div>
        <div style="padding-left: 10%">
          Rejected:
          <span style="color: red; font-weight: bold">{{
            statusCounts.rejected
          }}</span>
        </div>
      </v-card>
    </v-col>
    <v-col cols="12" md="3">
      <v-card title="Total Sales/Income" height="250px">
        <div style="padding-left: 10%">
          {{ totalSales.value }}
        </div>
      </v-card>
    </v-col>
    <v-col cols="12" md="3">
      <v-card title="Caskets/Chapels" height="250px">
        {{ casketChapelCount.value }}
      </v-card>
    </v-col>
  </v-row>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

const totalReservations = ref(0);
const statusCounts = ref({
  approved: 0,
  waiting_approval: 0,
  confirming_payment: 0,
  waiting_payment: 0,
  pending: 0,
  rejected: 0,
});
const totalSales = ref(0);
const casketChapelCount = ref(0);

const fetchDashboardStats = async () => {
  try {
    const response = await axios.get(
      `${import.meta.env.VITE_API_URL}/getDashboardStats.php`
    );
    if (response.data.success) {
      totalReservations.value = response.data.total_reservations;
      statusCounts.value = response.data.status_counts;
      totalSales.value = response.data.total_sales;
      casketChapelCount.value = response.data.casket_chapel_count;
    }
  } catch (error) {
    console.error("Error fetching dashboard stats:", error);
  }
};

onMounted(fetchDashboardStats);
</script>
