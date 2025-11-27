<template>
  <div class="!space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-semibold">Chapel Management</h1>
        <p class="text-gray-500">Manage chapel details, capacity, and availability</p>
      </div>
    </div>

    <!-- Chapel List -->
    <div class="grid grid-cols-1 gap-6">
      <div
        v-for="chapel in chapels"
        :key="chapel.id"
        class="border !rounded-lg shadow-sm"
      >
        <div class="!p-4 flex items-center justify-between">
          <div class="flex items-center !space-x-3">
            <span class="text-2xl">⛪</span>
            <div>
              <h2 class="text-lg font-semibold">{{ chapel.name }}</h2>
            </div>
          </div>

          <div class="flex items-center !space-x-3">
            <span
              class="px-3 !py-1 text-sm !rounded-full"
              :class="chapel.status === 'available'  ? '!bg-green-100 text-green-700' : '!bg-gray-200 text-gray-600'"
            >
              {{ chapel.status === 'available' ? 'available' : 'not available' }}
            </span>

            <button
              class="border !rounded-lg px-3 !py-1 hover:!bg-gray-100"
              @click="toggleExpand(chapel.id)"
            >
              {{ expandedChapel === chapel.id ? 'Collapse' : 'Edit Details' }}
            </button>
          </div>
        </div>

        <div v-if="expandedChapel === chapel.id" class="!p-6 border-t !space-y-6 !bg-gray-50">
          <div class="grid grid-cols-1 md:grid-cols-2 !gap-6">
            <div class="!space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Chapel Name</label>
                <input
                  v-model="chapel.name"
                  type="text"
                  class="w-full border !rounded-md px-3 !py-2"
                  placeholder="Enter chapel name"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Capacity (guests)</label>
                <input
                  v-model="chapel.capacity"
                  type="number"
                  class="w-full border !rounded-md px-3 !py-2"
                  placeholder="Maximum capacity"
                />
              </div>

              <div class="flex items-center justify-between !p-4 border !rounded-lg !bg-white">
                <div>
                  <label class="font-medium text-gray-700">Availability</label>
                  <p class="text-sm text-gray-500">Toggle if this chapel is open for booking</p>
                </div>
                <label class="inline-flex items-center cursor-pointer">
                  <input
                    type="checkbox"
                    :checked="chapel.status === 'available'"
                    @change="toggleChapelStatus(chapel)"
                    class="hidden"
                  />
                  <span
                    class="w-10 h-5 flex items-center !bg-gray-300 !rounded-full !p-1 duration-300"
                    :class="{ '!bg-green-500': chapel.status === 'available' }"
                  >
                    <span
                      class="!bg-white w-4 h-4 !rounded-full shadow-md transform duration-300"
                      :class="{ 'translate-x-5': chapel.status === 'available' }"
                    ></span>
                  </span>
                </label>
              </div>
            </div>

            <div class="!space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Chapel Image</label>
                <div
                  class="relative h-64 border-2 border-dashed rounded-lg overflow-hidden bg-gray-100 flex items-center justify-center group"
                >
                  <img
                    v-if="chapel.previewImage || chapel.image"
                    :src="chapel.previewImage ? chapel.previewImage : getChapelImageUrl(chapel)"
                    alt="Chapel Image"
                    class="absolute top-1/2 left-1/2 w-full h-full max-w-full max-h-full object-contain transform -translate-x-1/2 -translate-y-1/2 transition-opacity duration-300"
                  />
                  <div
                    v-else
                    class="absolute inset-0 flex items-center justify-center text-gray-500 text-sm bg-gray-50"
                  >
                    No image uploaded
                  </div>
                  <div
                    class="absolute inset-0 flex items-center justify-center transition duration-300"
                    :class="{
                      'bg-black/40': isHovering === chapel.id,
                      'bg-transparent': isHovering !== chapel.id
                    }"
                    @mouseenter="isHovering = chapel.id"
                    @mouseleave="isHovering = null"
                  >
                    <label
                      :for="`upload-${chapel.id}`"
                      class="cursor-pointer bg-white rounded-lg px-4 !py-2 shadow-md opacity-0 transition-opacity duration-300"
                      :class="{ 'opacity-100': isHovering === chapel.id }"
                    >
                      📤 Upload Image
                    </label>
                    <input
                      :id="`upload-${chapel.id}`"
                      type="file"
                      accept="image/*"
                      class="hidden"
                      @change="uploadImage(chapel.id, $event)"
                    />
                  </div>
                </div>
                <div class="flex items-center justify-between mt-2">
                  <p class="text-xs text-gray-500">Recommended size: 800x600px</p>
                  <button
                    class="text-xs text-red-600 hover:text-red-700"
                    @click="resetImage(chapel.id)"
                  >
                    ❌ Reset
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Full Description</label>
            <textarea
              v-model="chapel.description"
              rows="5"
              class="w-full border !rounded-md px-3 !py-2"
              placeholder="Detailed description..."
            ></textarea>
          </div>

          <div class="!p-4 border border-blue-200 !bg-blue-50 !rounded-lg">
            <div class="flex items-start !space-x-3">
              <span class="text-blue-600 text-lg">ℹ️</span>
              <div>
                <p class="font-medium text-blue-800">Chapel Information</p>
                <p class="text-sm text-blue-700">
                  Ensure all information is accurate and up-to-date. High-quality images and detailed descriptions help customers make informed decisions.
                </p>
              </div>
            </div>
          </div>

          <div class="flex justify-end">
            <button
              class="!bg-blue-600 text-white px-4 !py-2 !rounded-lg hover:!bg-blue-700 flex items-center"
              @click="saveChapel(chapel.id)"
            >
              💾 <span class="ml-2">Save Changes</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed} from "vue";
import axios from "axios";

const expandedChapel = ref(null);
const isHovering = ref(null);
const chapels = ref([]);
const pendingImages = ref({});

const toggleExpand = (id) => {
  expandedChapel.value = expandedChapel.value === id ? null : id
}

const uploadImage = (id, event) => {
  const file = event.target.files[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = (e) => {
    const chapel = chapels.value.find((c) => c.id === id);
    if (chapel) {
      chapel.previewImage = e.target.result;
      pendingImages.value[id] = file;
    }
  };
  reader.readAsDataURL(file);
};

const resetImage = (id) => {
  const chapel = chapels.value.find((c) => c.id === id);
  if (!chapel) return;
  chapel.previewImage = "";
  chapel.image = "";
  delete pendingImages.value[id];
};

const saveChapel = async (id) => {
  const chapel = chapels.value.find((c) => c.id === id);
  if (!chapel) return;

  try {
    let imageName = chapel.image;

    if (pendingImages.value[id]) {
      const file = pendingImages.value[id];
      const formData = new FormData();
      formData.append("image", file);
      formData.append("id", id);

      const uploadResponse = await axios.post(
        `${import.meta.env.VITE_API_URL}/uploadChapelImage.php`,
        formData,
        {
          headers: { "Content-Type": "multipart/form-data" },
        }
      );

      if (uploadResponse.data.success) {
        imageName = uploadResponse.data.filename; 
        chapel.image = imageName;
        delete pendingImages.value[id];
      } else {
        alert("❌ " + uploadResponse.data.message);
        return;
      }
    }

    const response = await axios.post(
      `${import.meta.env.VITE_API_URL}/updateChapel.php`,
      {
        id: chapel.id,
        name: chapel.name,
        shortDescription: chapel.shortDescription,
        capacity: chapel.capacity,
        description: chapel.description,
        status: chapel.status,
        image: imageName, 
      }
    );

    if (response.data.success) {
      alert("✅ Chapel changes saved successfully!");
      fetchChapels(); 
    } else {
      alert("❌ " + response.data.message);
    }
  } catch (err) {
    console.error("Error saving chapel:", err);
    alert("⚠️ Something went wrong while saving.");
  }
};

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

const toggleChapelStatus = async (chapel) => {
  chapel.status = chapel.status === 'available' ? 'not available' : 'available'

  try {
    await axios.post(`${import.meta.env.VITE_API_URL}/updateChapelStatus.php`, {
      id: chapel.id,
      status: chapel.status,
    })
  } catch (err) {
    console.error('Error updating status:', err)
  }
}


onMounted(() => {
  fetchChapels();
  console.log(chapels)
});

</script>
