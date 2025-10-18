<template>
  <div class="!space-y-6">
    <div>
      <h1 class="text-3xl font-semibold">Dashboard</h1>
      <p class="text-muted-foreground">
        Welcome to Cosmopolitan Memorial Chapels Management System
      </p>
    </div>
    <div data-slot="card" class="bg-card text-card-foreground flex flex-col !gap-6 rounded-xl border !p-6 !mb-8">
      <div class="!mb-6">
        <h3>
          Chapel Reservation
        </h3>
        <p class="text-sm text-muted-foreground mt-1">
          Choose from our available chapel options
        </p>
      </div>
      <div class="grid lg:grid-cols-4 gap-8 px-6 lg:px-20">
        <div
          v-for="chapel in chapels"
          :key="chapel.id"
          class="!p-7 grid !gap-6 bg-white rounded-xl shadow-md overflow-hidden border border-gray-200 hover:shadow-lg transition-all duration-200"
        >
          <img
            :src="chapel.image"
            :alt="chapel.name"
            class="w-full h-48 object-cover rounded-lg"
          />

          <div class="p-6 flex flex-col justify-between">
            <div class="text-center">
              <h2 class="text-xl font-semibold text-gray-800 uppercase mb-2">
                {{ chapel.name }}
              </h2>
              <p class="text-gray-600 mb-4">
                Capacity: {{ chapel.capacity }} People
              </p>

              <ul class="text-gray-700 text-sm text-left list-disc list-inside mb-4">
                <li v-for="feature in chapel.features" :key="feature">{{ feature }}</li>
              </ul>

              <p class="text-sm mb-4">
                <span class="font-medium text-gray-700">Status:</span>
                <span
                  :class="chapel.status === 'Available' ? 'text-green-600 font-bold' : 'text-red-600 font-bold'"
                >
                  {{ chapel.status }}
                </span>
              </p>
            </div>

            <button 
              @click="setCurrentSection('/User/reserve')"
              class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-all disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg:not([class*='size-'])]:size-4 shrink-0 [&_svg]:shrink-0 outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive bg-primary text-primary-foreground hover:bg-primary/90 h-9 px-4 py-2 has-[>svg]:px-3 w-full"
            >
              <CalendarCheck class="h-4 w-4 mr-3" />
              Reserve Now
            </button>
          </div>
        </div>
      </div>
      <div class="grid lg:grid-cols-3 gap-8 px-6 lg:px-20 justify-center place-items-center mx-auto w-fit">
        <div
          v-for="chapel in chapels2"
          :key="chapel.id"
          class="!p-7 max-w-sm grid !gap-6 bg-white rounded-xl shadow-md overflow-hidden border border-gray-200 hover:shadow-lg transition-all duration-200"
        >
          <img
            :src="chapel.image"
            :alt="chapel.name"
            class="w-full h-48 object-cover rounded-lg"
          />

          <div class="p-6 flex flex-col justify-between">
            <div class="text-center">
              <h2 class="text-xl font-semibold text-gray-800 uppercase mb-2">
                {{ chapel.name }}
              </h2>
              <p class="text-gray-600 mb-4">
                Capacity: {{ chapel.capacity }} People
              </p>

              <ul class="text-gray-700 text-sm text-left list-disc list-inside mb-4">
                <li v-for="feature in chapel.features" :key="feature">{{ feature }}</li>
              </ul>

              <p class="text-sm mb-4">
                <span class="font-medium text-gray-700">Status:</span>
                <span
                  :class="chapel.status === 'Available' ? 'text-green-600 font-bold' : 'text-red-600 font-bold'"
                >
                  {{ chapel.status }}
                </span>
              </p>
            </div>
            <button 
              @click="setCurrentSection('/User/reserve')"
              class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-all disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg:not([class*='size-'])]:size-4 shrink-0 [&_svg]:shrink-0 outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive bg-primary text-primary-foreground hover:bg-primary/90 h-9 px-4 py-2 has-[>svg]:px-3 w-full"
            >
              <CalendarCheck class="h-4 w-4 mr-3" />
              Reserve Now
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from 'vue-router'
import {
  CalendarCheck,
} from 'lucide-vue-next'
import { inject } from 'vue'

const setCurrentSection = inject('setCurrentSection')
const router = useRouter()

const chapels = ref([
  {
    id: 1,
    name: "Chapel A",
    capacity: 50,
    features: [
      "Fully air-conditioned chapel",
      "Water Dispenser",
      "24/7 security and staff assistance",
      "Use of chapel for 3–5 days",
    ],
    status: "Available",
    image: "/chapels/ChapelA.avif",
  },
  {
    id: 2,
    name: "Chapel B",
    capacity: 50,
    features: [
      "Fully air-conditioned chapel",
      "Water Dispenser",
      "24/7 security and staff assistance",
      "Use of chapel for 3–5 days",
    ],
    status: "Available",
    image: "/chapels/ChapelB.jpg",
  },
  {
    id: 3,
    name: "Chapel C",
    capacity: 50,
    features: [
      "Fully air-conditioned chapel",
      "Water Dispenser",
      "24/7 security and staff assistance",
      "Use of chapel for 3–5 days",
    ],
    status: "Available",
    image: "/chapels/ChapelC.jpg",
  },
  {
    id: 4,
    name: "Chapel D",
    capacity: 50,
    features: [
      "Fully air-conditioned chapel",
      "Water Dispenser",
      "24/7 security and staff assistance",
      "Use of chapel for 3–5 days",
    ],
    status: "Available",
    image: "/chapels/ChapelD.jpg",
  },
]);

const chapels2 = ref([
  {
    id: 4,
    name: "Chapel Presidential",
    capacity: 50,
    features: [
      "Fully air-conditioned chapel",
      "Water Dispenser",
      "24/7 security and staff assistance",
      "Use of chapel for 3–5 days",
    ],
    status: "Available",
    image: "/chapels/ChapelE.jpg",
  },
  {
    id: 5,
    name: "Residence",
    capacity: 50,
    features: [
      "Fully air-conditioned chapel",
      "Water Dispenser",
      "24/7 security and staff assistance",
      "Use of chapel for 3–5 days",
    ],
    status: "Available",
    image: "/chapels/ChapelF.jpg",
  },
  {
    id: 6,
    name: "Semi-Private",
    capacity: 50,
    features: [
      "Fully air-conditioned chapel",
      "Water Dispenser",
      "24/7 security and staff assistance",
      "Use of chapel for 3–5 days",
    ],
    status: "Available",
    image: "/chapels/ChapelC.jpg",
  },
]);

const reserveNow = () => {
  router.push('/User/Reserve')
}
</script>
