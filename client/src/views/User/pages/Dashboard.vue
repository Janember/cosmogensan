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
            :src="getChapelImageUrl(chapel)"
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

              <div class="text-gray-700 text-sm text-left mb-4" style="white-space: pre-line;">
                {{ chapel.description }}
              </div>

              <p class="text-sm mb-4">
                <span class="font-medium text-gray-700">Status: </span>
                <span
                  :class="chapel.status === 'available' ? 'text-green-600 font-bold' : 'text-red-600 font-bold'"
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
import { ref, onMounted} from "vue";
import { useRouter } from 'vue-router'
import {
  CalendarCheck,
} from 'lucide-vue-next'
import { inject } from 'vue'
import axios from "axios";

const setCurrentSection = inject('setCurrentSection')
const router = useRouter()

const getChapelImageUrl = (chapel) => {
  console.log(chapel.image)
  return chapel.previewImage 
    ? chapel.previewImage 
    : chapel.image 
      ? `${import.meta.env.VITE_IMAGE_URL}/chapels/${chapel.image}`
      : null
}

const fetchChapels = async () => {
  try {
    const response = await axios.get(
      `${import.meta.env.VITE_API_URL}/getAllChapels.php`
    );
    if (response.data.success) {
      chapels.value = response.data.data.map((chapel) => ({
        ...chapel,
        previewImage: chapel.image
          ? `${import.meta.env.VITE_IMAGE_URL}/chapels/${chapel.image}`
          : "",
      }));
    } else {
      console.error("Failed to fetch:", response.data.message);
    }
  } catch (err) {
    console.error("Error:", err);
  }
};

const chapels = ref(null);

const reserveNow = () => {
  router.push('/User/Reserve')
}

onMounted(() => {
  fetchChapels();
  console.log(chapels)
});
</script>
