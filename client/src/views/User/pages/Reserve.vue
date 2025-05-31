<template>
  <div>
    <h2 class="text-2xl font-semibold mb-4">Chapel Reservation Form</h2>
    <p>Reserve a peaceful place for remembrance and reflection.</p>
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

        <!-- Reservation Dates -->
        <v-col cols="12">
          <h3 class="text-2xl font-semibold">Reservation Dates*</h3>
        </v-col>
        <v-col cols="12" md="6">
          <v-text-field
            v-model="state.reservationStart"
            :error-messages="v$.reservationStart.$errors.map((e) => e.$message)"
            label="Start Date"
            type="date"
            required
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
            @blur="v$.reservationEnd.$touch"
            @input="v$.reservationEnd.$touch"
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
              (Number(state.casketPrice) || 0) +
              (Number(state.addressPrice) || 0)
            }}
          </h3>

          <div v-if="state.casketSelect" class="mt-2 text-caption">
            <strong>Selected Casket Package: </strong
            >{{ state.casketSelect.casketType }} - ₱{{ state.casketPrice }}
          </div>

          <div v-if="state.address" class="mt-2 text-caption">
            <strong>Address: </strong>{{ state.address.city }} - ₱{{
              state.addressPrice
            }}
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
          <v-col cols="12" sm="6">
            <v-select
              v-model="tempCasket.color"
              :items="colors"
              item-title="name"
              label="Select a Color"
              required
            >
              <template #selection="{ item }">
                <v-icon
                  size="small"
                  :style="{ color: item.raw.color, marginRight: '8px' }"
                >
                  mdi-circle
                </v-icon>
                {{ item.raw.name }}
              </template>
            </v-select>
          </v-col>
          <v-col cols="12" sm="6">
            <v-autocomplete
              v-model="tempCasket.addOns"
              :items="addOns"
              label="Additional Features"
              multiple
              chips
            ></v-autocomplete>
          </v-col>
          <v-col cols="12" class="d-flex justify-center">
            <v-img
              v-if="tempCasket.color"
              :src="getColorImage(tempCasket.color)"
              height="140"
              max-width="200"
              cover
              class="rounded-t border cursor-pointer"
              style="border: 1px solid #ccc"
              @click="state.imageDialog = true"
            ></v-img>
            <!-- Dialog for Image Click-->
            <v-dialog v-model="state.imageDialog" max-width="600">
              <v-card>
                <v-img
                  :src="getColorImage(tempCasket.color)"
                  aspect-ratio="16/9"
                  contain
                ></v-img>
              </v-card>
            </v-dialog>
          </v-col>
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
              Casket Package Price: ₱{{ state.casketPrice }}
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
                :src="getChapelImage(chapel.image)"
                height="180"
                cover
                class="rounded-t"
              ></v-img>
              <v-card-title class="text-h6">{{ chapel.name }}</v-card-title>
              <v-card-text class="text-body-2">
                <p><strong>Capacity:</strong> {{ chapel.capacity }}</p>
                <ul class="ml-3 mt-2">
                  <li v-for="(feature, i) in chapel.features" :key="i">
                    • {{ feature }}
                  </li>
                </ul>
                <p class="mt-2">
                  <strong>Duration:</strong> {{ chapel.duration }}
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
import { reactive, ref, computed, onMounted } from "vue";
import { useVuelidate } from "@vuelidate/core";
import { required, email, minLength } from "@vuelidate/validators";
import { useRouter } from 'vue-router';
import axios from "axios";
import { VSelect } from "vuetify/components";

const router = useRouter()

const labelWithLink = `I agree to the <a href='https://cosmogensan.com/privacy-policy/' target='_blank'>Terms and Conditions and Privacy Policy</a>`;

const casketError = ref("");
const addressError = ref("");

const customizeDialog = ref(false);
const chapelDialog = ref(false);
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

onMounted(fetchCasketKinds);

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
    const res = await fetch(
      `${import.meta.env.VITE_API_URL}/getChapels.php?name=${casketType}`
    );
    const data = await res.json();

    if (data.success) {
      chapels.value = data.data.map((chapel) => ({
        name: chapel.name,
        image: chapel.image,
        capacity: "50 People",
        features: [
          "Fully air-conditioned chapel",
          "Water dispenser",
          "24/7 security and staff assistance",
        ],
        duration: "3-5 days",
        status: chapel.status,
      }));
    } else {
      console.error("Error fetching chapels:", data.message);
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
      state.casketPrice = data.price;
    } else {
      console.error(data.message);
      state.casketPrice = null;
    }
  } catch (err) {
    console.error("Fetch error:", err);
    state.casketPrice = null;
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

onMounted(fetchCities);

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
      state.addressPrice = data.price;
    } else {
      console.error(data.message);
      state.addressPrice = null;
    }
  } catch (err) {
    console.error("Fetch error:", err);
    state.addressPrice = null;
  }
};

const tempAddress = reactive({
  city: "",
  barangay: "",
  street: "",
  houseNo: "",
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
  casketPrice: "",
  addressPrice: "",
  imageDialog: false,
  submitDialog: false,
  showError: false,
  successDialog: false,
  casketID: "",
  chapelID: "",
});

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

const addOns = [
  "Memory Drawer",
  "Rosary Holder",
  "Photo Frame",
  "Hidden Compartment",
];
const colors = [
  { name: "Silver", color: "#C0C0C0", image: "Silver_Casket.jpg" },
  { name: "Gold", color: "#D4AF37", image: "Gold_Casket.jpg" },
  { name: "Brown", color: "#8B4513", image: "Brown_Casket.jpg" },
  { name: "Light Brown", color: "#CD853F" },
];

const getChapelImage = (imageName) => {
  return new URL(`../../../assets/chapels/${imageName}`, import.meta.url).href;
};

const getColorImage = (imageName) => {
  return new URL(
    `../../../assets/caskets/${imageName}_Casket.jpg`,
    import.meta.url
  ).href;
};

function selectChapel(chapel) {
  tempCasket.chapelSelect = chapel;
  chapelDialog.value = false;
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
  state.casketSelect = { ...tempCasket };
  fetchCasketID()
  fetchChapelID()
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
    instructions: tempCasket.additionalInstructions,
    city: tempAddress.city,
    barangay: tempAddress.barangay,
    street: tempAddress.street,
    houseNo: tempAddress.houseNo,
    userName: state.contactName,
    email: state.email,
    phone: state.phone,
    idUploaded: state.discountIdFile,
    price: Number(state.casketPrice) + Number(state.addressPrice)
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
