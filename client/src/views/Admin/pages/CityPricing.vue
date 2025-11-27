<template>
  <div class="!space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-semibold">City Pricing</h1>
        <p class="text-gray-500">
          Manage service pricing by city or origin location
        </p>
      </div>
    </div>

    <div class="border !rounded-lg !p-4 bg-white">
      <div class="flex items-center !space-x-2 mb-4">
        <MapPin class="h-5 w-5" />
        <h2 class="text-lg font-medium">City Pricing List</h2>
      </div>
      <p class="text-sm text-gray-500 mb-6">
        Set base service rates for different cities and locations
      </p>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div
          v-for="city in cities"
          :key="city.id"
          class="!p-4 border rounded-lg space-y-3"
        >
          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-700">City Name</label>
            <div class="w-full py-2 text-gray-800">
                {{ city.place }}
            </div>
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-700">Price (₱)</label>
            <div class="relative">
              <PhilippinePeso
                class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400"
              />
              <input
                type="number"
                v-model="city.price"
                class="w-full border !rounded-md pl-9 pr-3 py-2 !bg-gray-50 focus:ring focus:ring-blue-100"
              />
            </div>
          </div>
        </div>
      </div>

      <hr class="my-6" />

      <div class="flex justify-between items-center">
        <div class="text-sm text-gray-500">
          Total cities configured:
          <span class="font-medium text-gray-800">{{ cities.length }}</span>
        </div>
        <button
          @click="saveAll"
          :disabled="loading"
          class="flex items-center px-4 py-2 !bg-blue-600 text-white rounded-md hover:bg-blue-700 transition disabled:opacity-50"
        >
          <Save class="h-4 w-4 mr-2" />
          <span v-if="!loading">Save All Changes</span>
          <span v-else>Saving...</span>
        </button>
      </div>
    </div>

    <div class="!p-4 border border-blue-200 bg-blue-50 rounded-lg">
      <div class="flex items-start !space-x-3">
        <MapPin class="h-5 w-5 text-blue-600 mt-0.5" />
        <div>
          <p class="font-medium text-blue-800">Pricing Information</p>
          <p class="text-sm text-blue-700 mt-1">
            These prices represent the base service rates for each city. Actual
            charges may vary based on selected chapel, service type, and
            additional options. All prices are in Philippine Pesos (₱).
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from "axios";
import { ref, onMounted } from "vue";
import { MapPin, Save, PhilippinePeso } from "lucide-vue-next";

const cities = ref([]);
const loading = ref(false);

onMounted(async () => {
  try {
    const res = await axios.get(`${import.meta.env.VITE_API_URL}/getCityPricing.php`);
    cities.value = res.data;
  } catch (err) {
    console.error("Error loading cities:", err);
  }
});

const saveAll = async () => {
  loading.value = true;
  try {
    await axios.post(`${import.meta.env.VITE_API_URL}/saveCityPricing.php`, { cities: cities.value });
    alert("City pricing updated successfully!");
  } catch (err) {
    console.error("Error saving prices:", err);
    alert("Failed to save city pricing.");
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
hr {
  border-color: #e5e7eb;
}
</style>
