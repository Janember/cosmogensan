<template>
  <div>
    <h2 class="text-2xl font-semibold mb-4">Admin Dashboard</h2>
  </div>
  <v-row>
    <v-col cols="12" md="3">
      <v-card height="250px" style="padding: 25px;">
        <div style="font-size: 40px; font-weight: bold;">
          {{ convertToNumber(totalReservations) }}
        </div>
        <div style="font-size: 18px;">
          Monthly Reservations
        </div>
      </v-card>
    </v-col>
    <v-col cols="12" md="3">
      <v-card height="250px" style="padding: 25px;">
        <div style="font-size: 40px; color: orangered; font-weight: bold;">
          {{ convertToNumber(statusCounts.pending) }}
        </div>
        <div style="font-size: 18px;">
          Pending Reservations
        </div>
      </v-card>
    </v-col>
    <v-col cols="12" md="3">
      <v-card height="250px" style="padding: 25px;">
        <div>
          <span style="font-size: 40px; color: green; font-weight: bold;">
            ₱{{ convertToNumber(totalSales) }}
          </span>
        </div>
        <div style="font-size: 18px;">
          Total Monthly Sales
        </div>
      </v-card>
    </v-col>
    <v-col cols="12" md="3">
      <v-card height="250px" style="padding: 25px;">
        <div>
          <span style="font-size:40px; color: green; font-weight: bold;">
            {{ convertToNumber(casketCount) }}
          </span>
        </div>
        <div style="font-size: 18px;">
          Monthly Casket Count
        </div>
      </v-card>
    </v-col>
  </v-row>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { convertToNumber } from "../../../composables/globalfuncs";
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
const casketCount = ref(0);

const fetchDashboardStats = async () => {
  try {
    const response = await axios.get(
      `${import.meta.env.VITE_API_URL}/getDashboardStats.php`
    );
    if (response.data.success) {
      totalReservations.value = response.data.total_reservations;
      statusCounts.value = response.data.status_counts;
      totalSales.value = response.data.total_sales;
      casketCount.value = response.data.casket_count;
    }
  } catch (error) {
    console.error("Error fetching dashboard stats:", error);
  }
};

onMounted(fetchDashboardStats);
</script>
