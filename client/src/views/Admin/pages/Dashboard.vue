<template>
  <div class="!space-y-6">
    <div>
      <h1 class="text-3xl font-semibold">Dashboard</h1>
      <p class="text-muted-foreground">
        Welcome to Cosmopolitan Memorial Chapels Management System
      </p>
    </div>
    <v-row class="px-3">
      <v-col cols="12" md="3" class="!p-0 pl-3">
        <v-card 
          height="150px" 
          class="mx-auto !shadow-none !bg-card !text-card-foreground !flex flex-col gap-6 !rounded-xl !border"
          >
          <div class="auto-rows-min grid-rows-[auto_auto] gap-1.5 px-6 pt-6 has-data-[slot=card-action]:grid-cols-[1fr_auto] [.border-b]:pb-6 flex flex-row items-center justify-between space-y-0 pb-2">
            <h4 data-slot="card-title" class="text-sm">Total Reservations</h4>
            <icons.Calendar class="text-muted-foreground h-5 w-5"/>
          </div>

          <div data-slot="card-content" class="px-6 [&amp;:last-child]:pb-6">
            <div class="text-2xl">{{ convertToNumber(totalReservations) }}</div>
          </div>
        </v-card>
      </v-col>
      <v-col cols="12" md="3" class="!p-0 !pl-3">
        <v-card 
          height="150px" 
          class="mx-auto !shadow-none !bg-card !text-card-foreground !flex flex-col gap-6 !rounded-xl !border"
          >
          <div class="auto-rows-min grid-rows-[auto_auto] gap-1.5 px-6 pt-6 has-data-[slot=card-action]:grid-cols-[1fr_auto] [.border-b]:pb-6 flex flex-row items-center justify-between space-y-0 pb-2">
            <h4 data-slot="card-title" class="text-sm">Pending Reservations</h4>
            <icons.UsersRound class="text-muted-foreground h-5 w-5"/>
          </div>

          <div data-slot="card-content" class="px-6 [&amp;:last-child]:pb-6">
            <div class="text-2xl">{{ convertToNumber(statusCounts.pending) }}</div>
            <!-- <p class="text-xs text-muted-foreground">+2 from last week</p> -->
          </div>
        </v-card>
      </v-col>
      <v-col cols="12" md="3" class="!p-0 !pl-3">
        <v-card 
          height="150px" 
          class="mx-auto !shadow-none !bg-card !text-card-foreground !flex flex-col gap-6 !rounded-xl !border"
          >
          <div class="auto-rows-min grid-rows-[auto_auto] gap-1.5 px-6 pt-6 has-data-[slot=card-action]:grid-cols-[1fr_auto] [.border-b]:pb-6 flex flex-row items-center justify-between space-y-0 pb-2">
            <h4 data-slot="card-title" class="text-sm">Total Monthly Sales</h4>
            <icons.DollarSign class="text-muted-foreground h-5 w-5"/>
          </div>

          <div data-slot="card-content" class="px-6 [&amp;:last-child]:pb-6">
            <div class="text-2xl">₱{{ convertToNumber(totalSales) }}</div>
            <!-- <p class="text-xs text-muted-foreground">+2 from last week</p> -->
          </div>
        </v-card>
      </v-col>
      <v-col cols="6" md="3" class="!p-0 !pl-3">
        <v-card 
          height="150px" 
          class="mx-auto !shadow-none !bg-card !text-card-foreground !flex flex-col gap-6 !rounded-xl !border"
          >
          <div class="auto-rows-min grid-rows-[auto_auto] gap-1.5 px-6 pt-6 has-data-[slot=card-action]:grid-cols-[1fr_auto] [.border-b]:pb-6 flex flex-row items-center justify-between space-y-0 pb-2">
            <h4 data-slot="card-title" class="text-sm">Monthly Casket Count</h4>
            <icons.Building class="text-muted-foreground h-5 w-5"/>
          </div>

          <div data-slot="card-content" class="px-6 [&amp;:last-child]:pb-6">
            <div class="text-2xl">{{ convertToNumber(casketCount) }}</div>
            <!-- <p class="text-xs text-muted-foreground">+2 from last week</p> -->
          </div>
        </v-card>
      </v-col>
    </v-row>
    <v-row class="px-3">
      <v-col cols="6" class="grid !px-0 !gap-6">
        <div class="bg-card text-card-foreground flex flex-col !pb-6 gap-6 rounded-xl border">

          <div class="@container/card-header grid auto-rows-min grid-rows-[auto_auto] items-start gap-1.5 px-6 pt-6 has-data-[slot=card-action]:grid-cols-[1fr_auto] [.border-b]:pb-6">
            <h4 class="leading-none">Recent Reservations</h4>
            <p class="text-muted-foreground">Latest chapel reservations and bookings</p>
          </div>
          
          <div class="px-6 [&:last-child]:pb-6">
            <div v-if="reservations.length" class="!space-y-4">
              <div
                v-for="(res, index) in reservations"
                :key="index"
                class="flex items-center justify-between !p-3 border rounded-lg"
              >
                <div>
                  <p class="font-medium">{{ res.customer_name }}</p>
                  <p class="text-sm text-muted-foreground">{{ res.casket_name }}</p>
                  <p class="text-xs text-muted-foreground">{{ formatDate(res.start_date) }}</p>
                </div>
                <span
                  :class="[
                    'inline-flex items-center justify-center rounded-md border px-2 py-0.5 text-xs font-medium w-fit whitespace-nowrap shrink-0 transition-[color,box-shadow] overflow-hidden border-transparent',
                    res.status === 'approved'
                      ? 'bg-primary text-primary-foreground'
                      : 'bg-secondary text-secondary-foreground'
                  ]"
                >
                  {{ res.status }}
                </span>
              </div>
            </div>

            <p v-else class="text-center text-muted-foreground text-sm py-4">
              No recent reservations found.
            </p>
          </div>
        </div>
      </v-col>
      <v-col cols="6" class="grid !pr-0 !gap-6">
        <div data-slot="card" class="bg-card text-card-foreground flex flex-col gap-6 rounded-xl border">
          <div data-slot="card-header" class="@container/card-header grid auto-rows-min grid-rows-[auto_auto] items-start gap-1.5 px-6 pt-6 has-data-[slot=card-action]:grid-cols-[1fr_auto] [.border-b]:pb-6">
            <h4 data-slot="card-title" class="leading-none">Today's Services</h4>
            <p data-slot="card-description" class="text-muted-foreground">Scheduled services for today (Placeholder)</p>
          </div>
          <div data-slot="card-content" class="px-6 [&amp;:last-child]:pb-6">
            <div class="space-y-4">
              <div class="flex items-center justify-between !p-3 border rounded-lg">
                <div>
                  <p class="font-medium">St. Peter Chapel</p>
                  <p class="text-sm text-muted-foreground">Santos Family</p>
                </div>
                <div class="flex items-center text-sm text-muted-foreground">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock h-4 w-4 mr-1" aria-hidden="true"><path d="M12 6v6l4 2"></path>
                    <circle cx="12" cy="12" r="10"></circle>
                  </svg>
                  10:00 AM
                </div>
              </div>
            </div>
          </div>
        </div>
      </v-col>
    </v-row>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { convertToNumber } from "../../../composables/globalfuncs";
import axios from "axios";
import {
  Calendar,
  UsersRound,
  DollarSign,
  Building
} from 'lucide-vue-next'

const icons = {
  Calendar,
  UsersRound,
  DollarSign,
  Building
}

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

const reservations = ref([])

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

const fetchReservations = async () => {
  try {
    const res = await axios.get(
      `${import.meta.env.VITE_API_URL}/getRecentReservations.php`
    );
    if (res.data.success) {
      reservations.value = res.data.data
    } else {
      console.error('Error fetching reservations:', data.error)
    }
  } catch (err) {
    console.error('Fetch error:', err)
  }
}

const formatDate = (dateStr) => {
  console.log(dateStr);
  const date = new Date(dateStr)
  return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}

onMounted(() => {
  fetchDashboardStats()
  fetchReservations()
});
</script>
