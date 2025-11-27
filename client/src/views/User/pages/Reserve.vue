<template>
  <div>
    <h1 class="text-3xl font-semibold">Reserve Chapel</h1>
    <p class="text-muted-foreground">
      Book chapel services for your loved ones
    </p>
  </div>
  <form @submit.prevent="v$.$validate()">
    <v-container style="max-width: 800px" class="px-4">
      <v-row>
        <!-- Full Name of the Deceased -->
        <v-col cols="12">
          <h3 class="text-2xl font-semibold">Full name*</h3>
        </v-col>
        <v-col cols="12">
          <v-text-field
            v-model="state.name"
            :counter="99"
            :error-messages="v$.name.$errors.map((e) => e.$message)"
            label="Full Name of the Deceased"
            required
            @blur="v$.name.$touch"
            @input="v$.name.$touch"
          ></v-text-field>
        </v-col>

        <v-divider></v-divider>

        <!-- Casket Section -->
        <v-col cols="12">
          <h3 class="text-2xl font-semibold">Casket Package Selection*</h3>
        </v-col>
        <v-col cols="12">
          <v-btn
            prepend-icon="mdi-cog"
            variant="tonal"
            block
            height="56"
            @click="customizeDialog = true"
            :color="v$.casketSelect.$error ? 'error' : undefined"
          >
            Select a Casket Package
          </v-btn>
          <div v-if="state.casketSelect" class="mt-2 text-caption">
            <strong>Selected:</strong>
            {{ state.casketSelect.material }} |
            {{ state.casketSelect.casketType }} |
            {{ state.casketSelect.color }} |
            {{ state.casketSelect.chapelSelect?.name }}
          </div>
          <div v-if="v$.casketSelect.$error" class="text-error text-caption">
            {{ v$.casketSelect.$errors[0].$message }}
          </div>
        </v-col>

        <v-divider></v-divider>

        <!-- Address of Retrieval Section -->
        <v-col cols="12">
          <h3 class="text-2xl font-semibold">Retrieval Address*</h3>
        </v-col>
        <v-col cols="12">
          <v-btn
            block
            prepend-icon="mdi-map-marker"
            height="56"
            variant="tonal"
            @click="retrievalAddressDialog = true"
          >
            Address of Retrieval
          </v-btn>
          <div v-if="state.address" class="mt-2 text-caption">
            <strong>Selected:</strong>
            {{ state.address.city }} | {{ state.address.barangay }} |
            {{ state.address.street }} |
            {{ state.address.houseNo }}
          </div>
          <div v-if="v$.address.$error" class="text-error text-caption">
            {{ v$.address.$errors[0].$message }}
          </div>
        </v-col>

        <v-divider></v-divider>

        <!-- Contact Info -->
        <v-col cols="12">
          <h3 class="text-2xl font-semibold">Contact Info*</h3>
        </v-col>
        <v-col cols="12" md="4">
          <v-text-field
            v-model="state.contactName"
            :error-messages="v$.contactName.$errors.map((e) => e.$message)"
            label="Your Full Name"
            required
            @blur="v$.contactName.$touch"
            @input="v$.contactName.$touch"
          ></v-text-field>
        </v-col>
        <v-col cols="12" md="4">
          <v-text-field
            v-model="state.email"
            :error-messages="v$.email.$errors.map((e) => e.$message)"
            label="E-mail"
            required
            @blur="v$.email.$touch"
            @input="v$.email.$touch"
          ></v-text-field>
        </v-col>
        <v-col cols="12" md="4">
          <v-text-field
            v-model="state.phone"
            :error-messages="v$.phone.$errors.map((e) => e.$message)"
            label="Phone Number"
            required
            @blur="v$.phone.$touch"
            @input="v$.phone.$touch"
          ></v-text-field>
        </v-col>

        <v-divider></v-divider>

        <!-- Discount Eligibility Section -->
        <v-col cols="12">
          <h3 class="text-2xl font-semibold">Discount Eligibility</h3>
        </v-col>

        <v-col cols="12">
          <p class="text-body-2 mb-2">
            If applicable, please upload a valid Senior Citizen or PWD ID to
            avail of discounts.
          </p>
        </v-col>

        <v-col cols="12" md="6">
          <v-file-input
            v-model="state.discountIdFile"
            label="Upload ID (Senior Citizen / PWD)"
            accept="image/*"
            :error-messages="v$.discountIdFile?.$errors.map((e) => e.$message)"
            show-size
            @blur="v$.discountIdFile?.$touch"
            @change="v$.discountIdFile?.$touch"
            prepend-icon="mdi-upload"
            style="cursor: pointer"
          ></v-file-input>
        </v-col>

        <v-col cols="12" v-if="state.discountIdFile">
          <v-img
            :src="previewDiscountImage"
            alt="Uploaded Discount ID"
            max-height="200"
            contain
            class="mt-2"
          ></v-img>
        </v-col>

        <v-divider></v-divider>

        <v-col cols="12">
          <h3 class="text-2xl font-semibold">
            Total Price: ₱{{
              convertToNumber((Number(state.casketSelect?.casketPrice) || 0) + (Number(state.address?.addressPrice) || 0) + (Number(tempCasket.addOns.length * 1000)))
            }}
          </h3>

          <div v-if="state.casketSelect" class="mt-2 text-caption">
            <strong>Selected Casket Package: </strong>{{ state.casketSelect.casketType }} - ₱{{ convertToNumber(state.casketSelect.casketPrice) }}
          </div>

          <div v-if="state.address" class="mt-2 text-caption">
            <strong>Address: </strong>{{ state.address.city }} - ₱{{ convertToNumber(state.address.addressPrice)}}
          </div>

          <div v-if="tempCasket.addOns" class="mt-2 text-caption">
            <strong>Additional Features: </strong>{{ tempCasket.addOns }} - ₱{{ convertToNumber(tempCasket.addOns.length * 1000)}}
          </div>
        </v-col>

        <v-divider></v-divider>

        <!-- Error Section -->
        <v-alert
          v-if="state.showError"
          type="error"
          variant="outlined"
          class="my-4"
        >
          All <span class="font-weight-bold">*</span> sections are required.
        </v-alert>

        <v-divider></v-divider>

        <!-- Agreement & Actions -->
        <v-col cols="12" md="12">
          <div class="d-flex align-start">
            <v-checkbox
              v-model="state.checkbox"
              :error-messages="v$.checkbox.$errors.map((e) => e.$message)"
              required
              @blur="v$.checkbox.$touch"
              @change="v$.checkbox.$touch"
              class="me-2"
            ></v-checkbox>
            <div style="margin-top: 16px">
              I agree to the
              <a href="https://cosmogensan.com/privacy-policy/" target="_blank">
                Terms and Conditions and Privacy Policy
              </a>
            </div>
          </div>
          <v-btn class="me-4" @click="handleSubmit">Submit</v-btn>
        </v-col>
      </v-row>
    </v-container>
  </form>

  <!-- Dialog for Casket Customization -->
  <v-dialog v-model="customizeDialog" max-width="700">
    <v-card title="Casket Package" prepend-icon="mdi-cog">
      <v-card-text>
        <v-row dense>
          <v-col cols="12" sm="6">
            <v-select
              v-model="tempCasket.material"
              :items="casketKinds"
              label="Casket Kind*"
              @update:modelValue="fetchCasketTypes"
              required
            />
          </v-col>
          <v-col cols="12" sm="6">
            <v-select
              v-model="tempCasket.casketType"
              :items="caskets"
              item-title="name"
              label="Casket Type*"
              @update:modelValue="fetchChapelsByCasketType"
              :disabled="!tempCasket.material"
              required
            />
          </v-col>
          <v-col cols="12">
            <v-btn
              block
              prepend-icon="mdi-church"
              height="56"
              variant="tonal"
              @click="chapelDialog = true"
              :color="v$.chapelSelect?.$error ? 'error' : undefined"
              :disabled="!tempCasket.casketType"
              required
            >
              Select Chapel
            </v-btn>
            <div v-if="tempCasket.chapelSelect" class="mt-2 text-caption">
              <strong>Selected:</strong> {{ tempCasket.chapelSelect.name }}
            </div>
          </v-col>
          <v-col cols="12" md="6">
            <v-text-field
              v-model="state.reservationStart"
              :error-messages="v$.reservationStart.$errors.map((e) => e.$message)"
              label="Start Date"
              type="date"
              required
              :min="minDate"
              :disabled="!state.chapelID"
              :rules="[isDateAvailable, isStartDateValid]"
              @blur="v$.reservationStart.$touch"
              @input="v$.reservationStart.$touch"
            ></v-text-field>
          </v-col>
          <v-col cols="12" md="6">
            <v-text-field
              v-model="state.reservationEnd"
              :error-messages="v$.reservationEnd.$errors.map((e) => e.$message)"
              label="End Date"
              type="date"
              required
              :min="state.reservationStart || minDate"
              :disabled="!state.chapelID"
              :rules="[isDateAvailable, isEndDateValid]"
              @blur="v$.reservationEnd.$touch"
              @input="v$.reservationEnd.$touch"
            ></v-text-field>
          </v-col>
          <v-col cols="12" sm="4">
            <v-btn
              block
              height="56"
              variant="tonal"
              @click="colorDialog = true"
              required
            >
              Change Color*
            </v-btn>
          </v-col>
          <v-col cols="12" sm="4">
            <v-combobox
              v-model="tempCasket.addOns"
              :items="addOns"
              label="Additional Features"
              multiple
              chips
              hide-selected
              small-chips
              allow-new
              clearable
            >
              <template v-slot:selection="{ item, index }">
                <v-chip :key="index" close @click:close="removeAddOn(index)">
                  {{ item }}
                </v-chip>
              </template>
            </v-combobox>
          </v-col>
          <v-col cols="12" sm="4">
            <v-select
              v-model="tempCasket.size"
              :items="casketSizes"
              item-title="name"
              item-value="name"
              label="Casket Size*"
              class="text-center"
              hide-details
            >
              <!-- Custom dropdown list items -->
              <template v-slot:item="{ props, item }">
                <v-list-item v-bind="props" class="py-2">
                  <div class="flex flex-col items-center text-center w-full">
                    <span class="text-xs text-gray-600">{{ item.raw?.size || '' }}</span>
                  </div>
                </v-list-item>
              </template>

              <!-- Custom selected display -->
              <template v-slot:selection="{ item }">
                <div class="flex flex-col items-center text-center w-full">
                  <span class="text-base font-medium text-gray-800">{{ item.raw?.name || item.name }}</span>
                </div>
              </template>
            </v-select>

            <!-- Show input if custom is selected -->
            <v-text-field
              v-if="tempCasket.size === 'Custom'"
              v-model="tempCasket.customSize"
              label="Enter custom size"
              dense
              outlined
              class="mt-2 text-center"
            ></v-text-field>
          </v-col>
          <!-- <v-col cols="12" class="d-flex justify-center">
            <v-img
              v-if="tempCasket.color"
              :src="getColorImage(tempCasket.color)"
              height="140"
              max-width="200"
              cover
              class="rounded-t border cursor-pointer"
              style="border: 1px solid #ccc"
              @click="state.imageDialog = true"
            ></v-img> -->
            <!-- Dialog for Image Click-->
            <!-- <v-dialog v-model="state.imageDialog" max-width="600">
              <v-card>
                <v-img
                  :src="getColorImage(tempCasket.color)"
                  aspect-ratio="16/9"
                  contain
                ></v-img>
              </v-card>
            </v-dialog>
          </v-col> -->
        
          <model-viewer
            v-if="tempCasket.color"
            id="casketViewer"
            ref="viewer"
            src="/models/casket.glb"
            background-color="#f5f5f5"
            camera-controls
            class="mb-6"
            style="width: 100%; height: 300px; border-radius: 12px; background: gray; box-shadow: 0 4px 12px rgba(0,0,0,0.2);"
            @load="() => applyColor(customColor)"
          />
          <v-col cols="12">
            <v-textarea
              v-model="tempCasket.additionalInstructions"
              label="Additional Instructions"
              hint="Optional"
            ></v-textarea>
          </v-col>
          <v-col cols="12">
            <v-alert v-if="casketError" type="error" class="mt-4" dense>
              {{ casketError }}
            </v-alert>
          </v-col>
          <v-divider></v-divider>
          <v-col cols="12">
            <h3 class="text-2xl font-semibold">
              Casket Package Price: ₱{{ convertToNumber(tempCasket.casketPrice) }}
            </h3>
          </v-col>
        </v-row>
      </v-card-text>
      <v-divider></v-divider>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn variant="plain" @click="customizeDialog = false">Cancel</v-btn>
        <v-btn color="primary" variant="tonal" @click="saveCasket">Save</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>

  <!-- Dialog for Color Selection -->
  <v-dialog v-model="colorDialog" max-width="400px">
    <v-card>
      <v-card-title class="text-h6">Casket Color Options</v-card-title>

      <v-card-text>
        <model-viewer
          id="casketViewer"
          ref="viewer"
          src="/models/casket.glb"
          background-color="#f5f5f5"
          camera-controls
          style="width: 100%; max-width: 600px; height: 400px; border-radius: 12px; background: gray; box-shadow: 0 4px 12px rgba(0,0,0,0.2);"
          @load="() => applyColor(customColor)"
        />

        <!-- Preset Colors -->
        <div class="mt-4">
          <h3 class="mb-2">Preset Colors</h3>
          <v-row>
            <v-col
              v-for="preset in presetColors"
              :key="preset.name"
              cols="4"
              class="d-flex justify-center"
            >
              <v-btn
                class="!bg-[var(--dynamic-bg)]"
                :style="{ '--dynamic-bg': preset.color }"
                block
                @click="applyColor(preset.color)"
              >
                {{ preset.name }}
              </v-btn>
            </v-col>
          </v-row>
        </div>

        <!-- Custom Picker -->
        <div class="mt-6">
          <h3 class="mb-2">Custom Color</h3>
          <v-color-picker
            v-model="customColor"
            hide-inputs
            @update:model-value="applyColor(customColor)"
          ></v-color-picker>
        </div>
      </v-card-text>

      <v-card-actions>
        <v-spacer />
        <v-btn text @click="colorDialog = false">Close</v-btn>
        <v-btn color="primary" @click="saveColor">Apply Selection</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>

  <!-- Dialog for Chapel Selection -->
  <v-dialog v-model="chapelDialog" max-width="800px">
    <v-card>
      <v-card-title class="text-h5"> Select a Chapel </v-card-title>
      <v-card-text>
        <v-row dense>
          <v-col
            v-for="chapel in chapels"
            :key="chapel.name"
            cols="12"
            sm="6"
            md="4"
          >
            <v-card
              :elevation="2"
              :outlined="tempCasket.chapelSelect?.name !== chapel.name"
              color="primary-lighten5"
              class="hover:scale-105 transition"
              @click="selectChapel(chapel)"
            >
              <v-img
                :src="getChapelImageUrl(chapel)"
                height="180"
                cover
                class="rounded-t"
              ></v-img>
              <v-card-title class="text-h6">{{ chapel.name }}</v-card-title>
              <v-card-text class="text-body-2">
                <p><strong>Capacity:</strong> {{ chapel.capacity }} People</p>
                <ul class="ml-3 mt-2">
                  <div class="text-gray-700 text-sm text-left mb-4" style="white-space: pre-line;">
                    {{ chapel.description }}
                  </div>
                </ul>
                <p class="mt-2">
                  <strong>Duration:</strong> 3-5 Days
                </p>
                <p>
                  <strong>Status:</strong>
                  <span
                    :class="
                      chapel.status === 'available'
                        ? 'text-success'
                        : 'text-error'
                    "
                  >
                    {{ chapel.status }}
                  </span>
                </p>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </v-card-text>
      <v-card-actions>
        <v-spacer />
        <v-btn color="primary" text @click="chapelDialog = false">Close</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>

  <!-- Dialog for Retrieval Address -->
  <v-dialog v-model="retrievalAddressDialog" max-width="700">
    <v-card title="Address of Retrieval" prepend-icon="mdi-map-marker">
      <v-card-text>
        <v-row dense>
          <v-col cols="12" md="6">
            <v-select
              v-model="tempAddress.city"
              :items="cities"
              label="City/Municipality"
              @update:modelValue="fetchBarangays"
              required
            />
          </v-col>

          <v-col cols="12" md="6">
            <v-select
              v-model="tempAddress.barangay"
              :items="barangays"
              label="Barangay"
              :disabled="!tempAddress.city"
              required
            />
          </v-col>

          <v-col cols="12" md="6">
            <v-text-field
              v-model="tempAddress.street"
              label="Street Name"
              required
            ></v-text-field>
          </v-col>

          <v-col cols="12" md="6">
            <v-text-field
              v-model="tempAddress.houseNo"
              label="House No. / Building Name"
              required
            ></v-text-field>
          </v-col>
        </v-row>
      </v-card-text>

      <v-divider></v-divider>

      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn variant="plain" @click="retrievalAddressDialog = false"
          >Cancel</v-btn
        >
        <v-btn color="primary" variant="tonal" @click="saveRetrievalAddress"
          >Save</v-btn
        >
      </v-card-actions>
    </v-card>
  </v-dialog>

  <!-- Dialog for Submission Customization -->
  <v-dialog v-model="state.submitDialog" max-width="400">
    <v-card>
      <v-card-title class="text-h6">Confirm Submission</v-card-title>
      <v-card-text>Are you sure you want to submit the form?</v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn text @click="state.submitDialog = false">Cancel</v-btn>
        <v-btn :loading="state.loading" color="primary" text @click="submitForm">Yes, Submit</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>

  <!-- Dialog for Successful Submission -->
  <v-dialog v-model="state.successDialog" width="500">
    <v-card>
      <v-card-title>Reservation Submitted</v-card-title>
      <v-card-text
        >Your reservation has been successfully submitted.</v-card-text
      >
      <v-card-actions>
        <v-btn color="success" @click="redirectToReservations"
          >Go to My Reservations</v-btn
        >
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { reactive, ref, computed, onMounted, watch} from "vue";
import { useVuelidate } from "@vuelidate/core";
import { required, email, minLength } from "@vuelidate/validators";
import { useRouter } from 'vue-router';
import axios from "axios";
import { VSelect } from "vuetify/components";
import { convertToNumber } from "../../../composables/globalfuncs";

const router = useRouter()

const labelWithLink = `I agree to the <a href='https://cosmogensan.com/privacy-policy/' target='_blank'>Terms and Conditions and Privacy Policy</a>`;

const viewer = ref(null);

const customColor = ref("#8b4513");

const casketError = ref("");
const addressError = ref("");

const customizeDialog = ref(false);
const chapelDialog = ref(false);
const colorDialog = ref(false);
const retrievalAddressDialog = ref(false);

const casketKinds = ref([]);
const caskets = ref([]);
const chapels = ref([]);

const cities = ref([]);
const barangays = ref([]);

const fetchCasketKinds = async () => {
  fetch(`${import.meta.env.VITE_API_URL}/getCasketKinds.php`)
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        casketKinds.value = data.data;
      } else {
        console.error(data.message);
      }
    })
    .catch((err) => {
      console.error("Fetch error:", err);
    });
};

const fetchCasketTypes = async () => {
  tempCasket.casketType = "";
  try {
    const res = await fetch(
      `${import.meta.env.VITE_API_URL}/getCasketTypes.php?kind=${tempCasket.material}`
    );
    const data = await res.json();

    if (data.success) {
      caskets.value = data.data.map((item) => ({
        id: item.id,
        name: item.name,
        package_price: item.package_price,
      }));
    } else {
      console.error(data.message);
    }
  } catch (err) {
    console.error("Fetch error:", err);
  }
};

const fetchChapelsByCasketType = async (casketType) => {
  fetchCasketPrice();
  try {
    const response = await axios.get(
      `${import.meta.env.VITE_API_URL}/getChapels.php?name=${casketType}`
    );
    if (response.data.success) {
      console.log(response)
      chapels.value = response.data.data.map((chapel) => ({
        ...chapel,
        previewImage: chapel.image
          ? `${import.meta.env.VITE_IMAGE_URL}/chapels/${chapel.image}`
          : "",
      }));
    } else {
      console.error("Error fetching chapels:", response.data.message);
    }
  } catch (err) {
    console.error("Fetch error:", err);
  }
};

const fetchCasketPrice = async () => {
  try {
    const res = await fetch(
      `${import.meta.env.VITE_API_URL}/getCasketPrice.php?name=${encodeURIComponent(
        tempCasket.casketType
      )}`
    );
    const data = await res.json();

    if (data.success) {
      tempCasket.casketPrice = data.price;
    } else {
      console.error(data.message);
      tempCasket.casketPrice = null;
    }
  } catch (err) {
    console.error("Fetch error:", err);
    tempCasket.casketPrice = null;
  }
};

const fetchCities = async () => {
  fetch(`${import.meta.env.VITE_API_URL}/getCities.php`)
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        cities.value = data.data;
      } else {
        console.error(data.message);
      }
    })
    .catch((err) => {
      console.error("Fetch error:", err);
    });
};
onMounted(fetchCasketKinds);
onMounted(fetchCities);

function applyColor(hex) {
  if (!viewer.value?.model) return
  const material = viewer.value.model.materials[0]
  if (material?.pbrMetallicRoughness) {
    const r = parseInt(hex.slice(1, 3), 16) / 255
    const g = parseInt(hex.slice(3, 5), 16) / 255
    const b = parseInt(hex.slice(5, 7), 16) / 255
    material.pbrMetallicRoughness.setBaseColorFactor([r, g, b, 1])
  }
  customColor.value = hex
}

function saveColor() {
  tempCasket.color = customColor.value;
  colorDialog.value = false;
}

async function fetchBarangays() {
  fetchAddressPrice();
  const citiesRes = await fetch(
    "https://psgc.gitlab.io/api/cities-municipalities/"
  );
  const citiesData = await citiesRes.json();

  const selectedCity = citiesData.find(
    (city) => city.name === tempAddress.city
  );

  if (selectedCity) {
    state.cityCode = selectedCity.code;
    const barangayRes = await fetch(
      `https://psgc.gitlab.io/api/cities-municipalities/${state.cityCode}/barangays/`
    );
    const barangayData = await barangayRes.json();
    barangays.value = barangayData.map((b) => b.name);
  }
  tempAddress.barangay = "";
}

const fetchAddressPrice = async () => {
  try {
    const res = await fetch(
      `${import.meta.env.VITE_API_URL}/getAddressPrice.php?name=${encodeURIComponent(
        tempAddress.city
      )}`
    );
    const data = await res.json();

    if (data.success) {
      state.address.addressPrice = data.price;
    } else {
      console.error(data.message);
      state.address.addressPrice = null;
    }
  } catch (err) {
    console.error("Fetch error:", err);
    state.address.addressPrice = null;
  }
};

const tempAddress = reactive({
  city: "",
  barangay: "",
  street: "",
  houseNo: "",
  addressPrice: null,
});

const tempCasket = reactive({
  id: "",
  material: "",
  casketType: "",
  chapelSelect: null,
  color: "",
  interior: "",
  handles: "",
  engraving: "",
  addOns: [],
  size: "",
  customSize: "",
  casketPrice: null,
});

const state = reactive({
  name: "",
  email: "",
  phone: "",
  contactName: "",
  reservationStart: "",
  reservationEnd: "",
  casketSelect: null,
  address: null,
  checkbox: false,
  discountIdFile: null,
  imageDialog: false,
  submitDialog: false,
  showError: false,
  successDialog: false,
  casketID: "",
  chapelID: "",
  customColor: "",
  colorDialog: false,
});

const minDate = computed(() => {
  const today = new Date();
  today.setDate(today.getDate() + 1);
  return today.toISOString().split("T")[0];
});

const bookedRanges = ref([]);

watch(colorDialog, (open) => {
  if (open && viewer.value) {
    viewer.value.addEventListener("load", () => {
      applyColor(customColor.value)
    }, { once: true })
  }
})

watch(() => state.chapelID, async (newId) => {
  try {
    const res = await axios.get(`${import.meta.env.VITE_API_URL}/getBookedDates.php`, {
      params: { chapel_id: newId },
    });
    bookedRanges.value = res.data || [];
  } catch (e) {
    console.error("Failed to fetch booked dates:", e);
  }
});

const isDateAvailable = (date) => {
  if (!date) return true;

  const picked = new Date(date);
  for (const range of bookedRanges.value) {
    const start = new Date(range.start_date);
    const end = new Date(range.end_date);
    if (picked >= start && picked <= end) {
      return "This date is already booked for this chapel";
    }
  }
  return true;
};

const isStartDateValid = (date) => {
  if (!date) return true;
  const today = new Date();
  today.setHours(0, 0, 0, 0);

  if (new Date(date) < today) {
    return "Start date cannot be earlier than today";
  }
  return true;
};

const isEndDateValid = (date) => {
  if (!date) return true;

  if (state.reservationStart) {
    const start = new Date(state.reservationStart);
    const end = new Date(date);

    const minEnd = new Date(start);
    minEnd.setDate(start.getDate() + 3);

    const maxEnd = new Date(start);
    maxEnd.setDate(start.getDate() + 5);

    if (end < minEnd) {
      return "End date must be at least 3 days after start date";
    }
    if (end > maxEnd) {
      return "End date cannot be more than 5 days after start date";
    }
  }

  return true;
};

const fetchCasketID = async () => {
  try {
    const res = await fetch(
      `${import.meta.env.VITE_API_URL}/getCasketID.php?name=${encodeURIComponent(
        tempCasket.casketType
      )}`
    );
    const data = await res.json();

    if (data.success) {
      state.casketID = data.id;
    } else {
      console.error(data.message);
      state.casketID = null;
    }
  } catch (err) {
    console.error("Fetch error:", err);
    state.casketID = null;
  }
};

const fetchChapelID = async () => {
  try {
    const res = await fetch(
      `${import.meta.env.VITE_API_URL}/getChapelID.php?name=${encodeURIComponent(
        tempCasket.chapelSelect?.name
      )}`
    );
    const data = await res.json();

    if (data.success) {
      state.chapelID = data.id;
    } else {
      console.error(data.message);
      state.chapelID = null;
    }
  } catch (err) {
    console.error("Fetch error:", err);
    state.chapelID = null;
  }
};

const casketSizes = [
  { name: "Junior", size: "(169 cm length x 55 cm width)"},
  { name: "Standard", size: "(180 cm length x 60 cm width)"},
  { name: "Oversized", size: "(213 cm length x 71 cm width)"},
  { name: "Custom" }
];

const addOns = [
  "Memory Drawer",
  "Rosary Holder",
  "Photo Frame",
  "Hidden Compartment",
];
const presetColors = [
  { color: "#8b4513" },
  { color: "#a0452e" },
  { color: "#000000" },
  { color: "#c0c0c0" },
  { color: "#d4af37" },
  { color: "#1a2a6c" }
];



const getChapelImageUrl = (chapel) => {
  console.log(chapel.image)
  return chapel.previewImage 
    ? chapel.previewImage 
    : chapel.image 
      ? `${import.meta.env.VITE_IMAGE_URL}/chapels/${chapel.image}`
      : null
}

const getChapelImage = (imageName) => {
  return new URL(`../../../assets/chapels/${imageName}`, import.meta.url).href;
};

/* const getColorImage = (imageName) => {
  return new URL(
    `../../../assets/caskets/${imageName}_Casket.jpg`,
    import.meta.url
  ).href;
}; */

function selectChapel(chapel) {
  tempCasket.chapelSelect = chapel;
  chapelDialog.value = false;
  fetchChapelID();
}

const previewDiscountImage = computed(() => {
  return state.discountIdFile
    ? URL.createObjectURL(state.discountIdFile)
    : null;
});

function saveCasket() {
  if (
    !tempCasket.material ||
    !tempCasket.color ||
    !tempCasket.casketType ||
    !tempCasket.chapelSelect?.name
  ) {
    casketError.value = "All * fields are required";
    return;
  }

  const startCheck = isStartDateValid(state.reservationStart);
  if (startCheck !== true) {
    casketError.value = startCheck;
    return;
  }

  const endCheck = isEndDateValid(state.reservationEnd);
  if (endCheck !== true) {
    casketError.value = endCheck;
    return;
  }

  const startDate = new Date(state.reservationStart);
  const endDate = new Date(state.reservationEnd);

  const isOverlapping = bookedRanges.value.some(({ start_date, end_date }) => {
    const bookedStart = new Date(start_date);
    const bookedEnd = new Date(end_date);
    return startDate <= bookedEnd && endDate >= bookedStart;
  });

  if (isOverlapping) {
    casketError.value = "Selected dates overlap with an existing reservation.";
    return;
  }

  state.casketSelect = { ...tempCasket };
  fetchCasketID();
  fetchChapelID();
  fetchCasketPrice();
  customizeDialog.value = false;
}

function saveRetrievalAddress() {
  if (
    !tempAddress.city ||
    !tempAddress.barangay ||
    !tempAddress.street ||
    !tempAddress.houseNo
  ) {
    addressError.value = "All * fields are required";
    return;
  }
  state.address = { ...tempAddress };
  fetchAddressPrice();
  retrievalAddressDialog.value = false;
}

const rules = {
  casketSelect: {
    required: () => (state.casketSelect !== null ? true : "Casket selection is required"),
  },
  address: {
    required: () => (state.address !== null ? true : "Retrieval Address is required"),
  },
  name: { required, minLength: minLength(3) },
  contactName: { required, minLength: minLength(3) },
  email: { required, email },
  phone: { required },
  reservationStart: { required },
  reservationEnd: { required },
  checkbox: { required },
};

async function handleSubmit() {
  const isValid = await v$.value.$validate()
  if (isValid) {
    state.showError = false;
    state.submitDialog = true;
  } else {
    state.showError = true;
  }
}

async function submitForm() {
  const id = JSON.parse(localStorage.getItem("user")).id
  state.loading = true;
  const formData = new FormData();

  const form = reactive({
    user_id: id,
    fullName: state.name,
    startDate: state.reservationStart,
    endDate: state.reservationEnd,
    casketID: state.casketID,
    chapelID: state.chapelID,
    color: tempCasket.color,
    additionalFeatures: tempCasket.addOns,
    customColor: tempCasket.customColor,
    instructions: tempCasket.additionalInstructions,
    city: tempAddress.city,
    barangay: tempAddress.barangay,
    street: tempAddress.street,
    houseNo: tempAddress.houseNo,
    userName: state.contactName,
    email: state.email,
    phone: state.phone,
    idUploaded: state.discountIdFile,
    price: Number(state.casketSelect.casketPrice) + Number(state.address.addressPrice) + Number(tempCasket.addOns.length * 1000),
    size: tempCasket.customSize ? tempCasket.customSize : tempCasket.size,
  });

  for (const key in form) {
    formData.append(key, form[key]);
  }

  try {
    const response = await fetch(`${import.meta.env.VITE_API_URL}/submitReservation.php`, {
      method: "POST",
      body: formData,
    });

    const result = await response.json();
    if (result.success) {

      const notifPayload = {
        title: "New Reservation Submitted",
        message: `${state.contactName} just made a reservation from ${state.reservationStart} to ${state.reservationEnd}.`,
        type: "role",
        role_target: "admin",
      };

      await fetch(`${import.meta.env.VITE_API_URL}/createNotification.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(notifPayload),
      });

      notifPayload.role_target = "staff";
      await fetch(`${import.meta.env.VITE_API_URL}/createNotification.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(notifPayload),
      });

      state.successDialog = true;
    } else {
      alert("Something went wrong!");
    }
  } catch (error) {
    console.error("Submission failed:", error);
    alert("Failed to submit reservation.");
  } finally {
    state.loading = false;
  }
}

function redirectToReservations() {
  router.push("/User/Reservations");
}

const v$ = useVuelidate(rules, state);
</script>
