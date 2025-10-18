<template>
    <div class="!space-y-6">
        <div>
            <h1 class="text-3xl font-semibold">Account Management</h1>
            <p class="text-muted-foreground">
                Manage your profile, security settings, and preferences
            </p>
        </div>
        <div class="flex flex-col gap-2 space-y-6">
            <div class="bg-muted text-muted-foreground h-9 items-center justify-center rounded-xl p-[3px] grid w-full grid-cols-4">
                <button
                    v-for="tab in tabs"
                    :key="tab.id"
                    role="tab"
                    :aria-selected="activeTab === tab.id"
                    :data-state="activeTab === tab.id ? 'active' : 'inactive'"
                    :aria-controls="`tab-content-${tab.id}`"
                    @click="activeTab = tab.id"
                    :class="[
                        'focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:outline-ring text-foreground inline-flex h-[calc(100%-1px)] flex-1 items-center justify-center gap-1.5 rounded-xl border !border-transparent px-2 py-1 text-sm font-medium whitespace-nowrap transition-[color,box-shadow] focus-visible:ring-[3px] focus-visible:outline-1 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*=\'size-\'])]:size-4',
                        activeTab === tab.id ? 'bg-white text-black shadow-md' : 'bg-transparent text-gray-400 hover:text-white'
                    ]"
                    >
                    {{ tab.label }}
                </button>
            </div>
        </div>
        <div class="flex-1 outline-none space-y-6">
            <div v-if="activeTab === 'profile'" id="tab-content-profile" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="bg-card text-card-foreground flex flex-col gap-6 rounded-xl border">
                    <div class="@container/card-header grid auto-rows-min grid-rows-[auto_auto] items-start gap-1.5 px-6 pt-6 has-data-[slot=card-action]:grid-cols-[1fr_auto] [.border-b]:pb-6">
                        <h4 class="leading-none">Profile Picture</h4>
                        <p class="text-muted-foreground">Update your profile photo</p>
                    </div>
                    <div class="px-6 [&:last-child]:pb-6 !space-y-4">
                        <div class="flex justify-center">
                            <img
                                :src="previewUrl"                                
                                alt="Profile placeholder"
                                class="w-32 h-32 rounded-full object-cover"
                            />
                        </div>
                        <input
                            ref="fileInput"
                            type="file"
                            accept="image/*"
                            class="hidden"
                            @change="handleFileChange"
                        />
                        <v-alert v-if="uploadError" type="error" class="mt-4 mx-2" dense>
                            {{ uploadError }}
                        </v-alert>
                        <v-alert v-if="uploadSuccess" type="success" class="mt-4 mx-2" dense>
                            {{ uploadSuccess }}
                        </v-alert>
                        <button 
                            class="flex items-center justify-center gap-2 rounded-md text-sm font-medium transition bg-primary text-foreground hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2 w-full"
                            @click="triggerFileInput"
                        >
                            <icons.Upload class="h-4 w-4 mr-3" />
                            Upload new photo
                        </button>
                    </div>
                </div>
                <div class="!p-6 bg-card text-card-foreground flex flex-col gap-6 rounded-xl border lg:col-span-2">
                    <div class="grid auto-rows-min grid-rows-[auto_auto] items-start gap-1.5 has-data-[slot=card-action]:grid-cols-[1fr_auto] [.border-b]:pb-6">
                        <h4 class="leading-none">Personal Information</h4>
                        <p class="text-muted-foreground">Update your personal details</p>
                    </div>
                    <div class="[&:last-child]:pb-6 !space-y-4">
                        <div class="grid grid-cols-2 !gap-4">
                            <div class="!space-y-2">
                                <label class="flex items-center gap-2 text-sm leading-none font-medium select-none group-data-[disabled=true]:pointer-events-none group-data-[disabled=true]:opacity-50 peer-disabled:cursor-not-allowed peer-disabled:opacity-50">
                                    First Name
                                </label>
                                <input 
                                    class="file:text-foreground placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground flex h-9 w-full min-w-0 !rounded-md px-3 py-1 text-base !bg-input-background transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 aria-invalid:border-destructive"
                                    :value="firstName"
                                    />
                            </div>
                            <div class="!space-y-2">
                                <label class="flex items-center gap-2 text-sm leading-none font-medium select-none group-data-[disabled=true]:pointer-events-none group-data-[disabled=true]:opacity-50 peer-disabled:cursor-not-allowed peer-disabled:opacity-50">
                                    Last Name
                                </label>
                                <input 
                                    class="file:text-foreground placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground flex h-9 w-full min-w-0 !rounded-md px-3 py-1 text-base !bg-input-background transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 aria-invalid:border-destructive"
                                    :value="lastName"
                                    />
                            </div>
                        </div>
                        <div class="!space-y-2">
                            <label class="flex items-center gap-2 text-sm leading-none font-medium select-none group-data-[disabled=true]:pointer-events-none group-data-[disabled=true]:opacity-50 peer-disabled:cursor-not-allowed peer-disabled:opacity-50">
                                Email Address
                            </label>
                            <div class="relative">
                                <icons.Mail class="absolute h-5 w-5 left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                                <input 
                                    type="email"
                                    placeholder="Enter your email"
                                    class="!rounded-md !bg-input-background w-full h-9 rounded-md !border-gray-300 bg-white pl-10 pr-3 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                                    :value="email"
                                />
                            </div>
                        </div>
                        <div class="!space-y-2">
                            <label class="flex items-center gap-2 text-sm leading-none font-medium select-none">
                                Phone Number
                            </label>
                            <div class="relative flex items-center">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">+63</span>
                                <input
                                    type="tel"
                                    class="!rounded-md !bg-input-background w-full h-9 rounded-md !border-gray-300 bg-white pl-12 pr-3 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                                    v-model="phoneNumber"
                                    @input="handlePhoneInput"
                                    placeholder="9123456789"
                                />
                            </div>
                        </div>
                        <div class="!space-y-2">
                            <label class="flex items-center gap-2 text-sm leading-none font-medium select-none group-data-[disabled=true]:pointer-events-none group-data-[disabled=true]:opacity-50 peer-disabled:cursor-not-allowed peer-disabled:opacity-50">
                                Address
                            </label>
                            <div class="relative">
                                <icons.MapPin class="absolute h-5 w-5 left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                                <input 
                                    class="!rounded-md !bg-input-background w-full h-9 rounded-md !border-gray-300 bg-white pl-10 pr-3 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                                    :value="address"
                                />
                            </div>
                        </div>
                        <v-alert v-if="error" type="error" class="mt-4 mx-2" dense>
                            {{ error }}
                        </v-alert>
                        <button @click="saveChanges" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-all disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg:not([class*='size-'])]:size-4 shrink-0 [&_svg]:shrink-0 outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive bg-primary text-primary-foreground hover:bg-primary/90 h-9 px-4 py-2 has-[>svg]:px-3 w-full">
                            <icons.Save class="h-4 w-4 mr-3" />
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>

            <div v-else-if="activeTab === 'security'" id="tab-content-security">
                <div class="!p-6 bg-card text-card-foreground flex flex-col gap-6 rounded-xl border lg:col-span-2">
                    <div class="grid auto-rows-min grid-rows-[auto_auto] items-start gap-1.5 has-data-[slot=card-action]:grid-cols-[1fr_auto] [.border-b]:pb-6">
                        <h4 class="leading-none">Change password</h4>
                        <p class="text-muted-foreground">Update your login password</p>
                    </div>
                    <div class="[&:last-child]:pb-6 !space-y-4">
                        <div class="!space-y-2">
                            <label class="flex items-center gap-2 text-sm leading-none font-medium select-none">
                                Current Password
                            </label>
                            <div class="relative">
                                <input 
                                    v-model="currentPassword"
                                    :type="showCurrent ? 'text' : 'password'"
                                    placeholder="Enter current password"
                                    class="!rounded-md !bg-input-background w-full h-9 rounded-md bg-white pl-3 pr-10 text-sm placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                                    autocomplete="current-password"
                                />
                                <button
                                    type="button"
                                    @click="showCurrent = !showCurrent"
                                    :aria-pressed="showCurrent"
                                    :aria-label="showCurrent ? 'Hide current password' : 'Show current password'"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 p-1"
                                    >
                                    <component :is="showCurrent ? icons.EyeOff : icons.Eye" class="h-5 w-5 text-gray-400" />
                                </button>
                            </div>
                        </div>
                        <div class="!space-y-2">
                            <label class="flex items-center gap-2 text-sm leading-none font-medium select-none">
                                New Password
                            </label>

                            <div class="relative">
                                <input
                                    v-model="newPassword"
                                    :type="showNew ? 'text' : 'password'"
                                    placeholder="Enter new password"
                                    class="!rounded-md !bg-input-background w-full h-9 rounded-md bg-white pl-3 pr-10 text-sm placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                                    autocomplete="new-password"
                                />
                                <button
                                    type="button"
                                    @click="showNew = !showNew"
                                    :aria-pressed="showNew"
                                    :aria-label="showNew ? 'Hide new password' : 'Show new password'"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 p-1"
                                >
                                    <component :is="showNew ? icons.EyeOff : icons.Eye" class="h-5 w-5 text-gray-400" />
                                </button>
                            </div>
                        </div>
                        <div class="!space-y-2">
                            <label class="flex items-center gap-2 text-sm leading-none font-medium select-none">
                                Confirm New Password
                            </label>
                            <div class="relative">
                                <input
                                    v-model="confirmPassword"
                                    :type="showConfirm ? 'text' : 'password'"
                                    placeholder="Confirm new password"
                                    class="!rounded-md !bg-input-background w-full h-9 rounded-md bg-white pl-3 pr-10 text-sm placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                                    autocomplete="new-password"
                                />
                                <button
                                    type="button"
                                    @click="showConfirm = !showConfirm"
                                    :aria-pressed="showConfirm"
                                    :aria-label="showConfirm ? 'Hide confirm password' : 'Show confirm password'"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 p-1"
                                >
                                    <component :is="showConfirm ? icons.EyeOff : icons.Eye" class="h-5 w-5 text-gray-400" />
                                </button>
                            </div>
                        </div>
                        <v-alert v-if="error" type="error" class="mt-4 mx-2" dense>
                            {{ error }}
                        </v-alert>
                        <v-alert v-if="success" type="success" class="mt-4 mx-2" dense>
                            {{ success }}
                        </v-alert>
                        <button @click="changePassword" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-all disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg:not([class*='size-'])]:size-4 shrink-0 [&_svg]:shrink-0 outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive bg-primary text-primary-foreground hover:bg-primary/90 h-9 px-4 py-2 has-[>svg]:px-3 w-full">
                            Change Password
                        </button>
                    </div>
                </div>
            </div>

            <div v-else-if="activeTab === 'notifications'" id="tab-content-notifications">
                <div class="!p-6 bg-card text-card-foreground flex flex-col gap-6 rounded-xl border lg:col-span-2">
                    <h3 class="font-semibold text-lg mb-2">Notification Settings</h3>
                    <p class="text-sm text-muted-foreground">Coming Soon...</p>
                </div>
            </div>

            <div v-else-if="activeTab === 'preferences'" id="tab-content-preferences">
                <div class="!p-6 bg-card text-card-foreground flex flex-col gap-6 rounded-xl border lg:col-span-2">
                    <h3 class="font-semibold text-lg mb-2">Preferences</h3>
                    <p class="text-sm text-muted-foreground">Coming Soon...</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted} from 'vue'
import axios from "axios";

import { 
    Save, 
    Upload, 
    Mail,
    Phone,
    MapPin,
    Eye,
    EyeOff
} from 'lucide-vue-next'

const icons = {
    Save,
    Upload,
    Mail,
    Phone,
    MapPin,
    Eye,
    EyeOff
}
const user = {}
const imageBaseUrl = import.meta.env.VITE_IMAGE_URL

const fileInput = ref(null);
const firstName = ref('');
const lastName = ref('');
const phoneNumber = ref('');
const email = ref('');
const address = ref('');
const previewUrl = ref(null)
const error = ref("");
const success = ref("");
const uploadError = ref("");
const uploadSuccess = ref("");
let originalUser = {}

const currentPassword = ref('')
const newPassword = ref('')
const confirmPassword = ref('')

const showCurrent = ref(false)
const showNew = ref(false)
const showConfirm = ref(false)

const triggerFileInput = () => {
  fileInput.value.click();
};

const handlePhoneInput = (event) => {
  phoneNumber.value = event.target.value.replace(/\D/g, '')
  if (phoneNumber.value.length > 10) {
    phoneNumber.value = phoneNumber.value.slice(0, 10)
  }
}

const handleFileChange = async (e) => {
  const file = e.target.files[0];
  if (!file) return;

  uploadSuccess.value = "";

  previewUrl.value = URL.createObjectURL(file);

  const formData = new FormData();
  formData.append("profile_picture", file);
  formData.append("user_id", user.value.id);

  try {
    const { data } = await axios.post(
      `${import.meta.env.VITE_API_URL}/uploadProfilePicture.php`,
      formData,
      {
        headers: { "Content-Type": "multipart/form-data" },
      }
    );

    if (data.success) {
      user.value.profile_picture = data.filename || "profilepicture.png";
      previewUrl.value = `${imageBaseUrl}/uploads/profile_pictures/${user.value.profile_picture}`;
      const updatedUser = {
        ...user.value,
        previewUrl: user.value.profile_picture || "profilepicture.png",
      };
      localStorage.setItem("user", JSON.stringify(updatedUser));
      uploadSuccess.value = "Profile picture updated successfully!";

    } else {
      uploadError.value = "Upload failed: " + data.message;
      alert("Upload failed: " + data.message);
    }
  } catch (err) {
    console.error(err);
    alert("Upload failed, check console.");
  }
};


const fetchUserInfo = async () => {
  try {
    const storedUser = JSON.parse(localStorage.getItem('user'))
    if (!storedUser || !storedUser.id) {
      console.warn('No user ID found in localStorage')
      return
    }

    const userId = storedUser.id

    const response = await axios.get(`${import.meta.env.VITE_API_URL}/getUserData.php`, {
      params: { id: userId }
    })

    if (response.data.success) {
      const user = response.data.user
      firstName.value = user.first_name || ''
      lastName.value = user.last_name || ''
      email.value = user.email || ''
      phoneNumber.value = user.phone_number ? user.phone_number.replace(/^\+63/, '') : ''
      address.value = user.address || ''

      originalUser = { ...user }
    } else {
      console.error('Failed to fetch user:', response.data.message)
    }
  } catch (err) {
    console.error('Error fetching user info:', err)
  }
}

const tabs = [
  { id: 'profile', label: 'Profile' },
  { id: 'security', label: 'Security' },
  { id: 'notifications', label: 'Notifications' },
  { id: 'preferences', label: 'Preferences' }
]

const activeTab = ref('profile')

const isValidPhone = (phone) => {
  if (!phone) return false
  const cleaned = phone.replace(/[\s-]/g, '')
  if (/^9\d{9}$/.test(cleaned)) return true
  if (/^\+639\d{9}$/.test(cleaned)) return true
  return false
}

const saveChanges = async () => {
  const phoneValue = phoneNumber.value.startsWith('9') ? '+63' + phoneNumber.value : phoneNumber.value
  const updates = {}
  if (firstName.value !== originalUser.first_name) updates.first_name = firstName.value
  if (lastName.value !== originalUser.last_name) updates.last_name = lastName.value
  if (email.value !== originalUser.email) updates.email = email.value
  if (phoneValue !== originalUser.phone_number) updates.phone_number = phoneValue
  if (address.value !== originalUser.address) updates.address = address.value

  if (Object.keys(updates).length === 0) {
    error.value = 'No changes detected'
    return
  }

  if (updates.phone_number && !isValidPhone(updates.phone_number)) {
    error.value = 'Phone number must be 10 digits and start with 9'
    return
  }

  try {
    const storedUser = JSON.parse(localStorage.getItem('user'))
    const response = await axios.post(`${import.meta.env.VITE_API_URL}/updateUserData.php`, {
      user_id: storedUser.id,
      ...updates
    })

    if (response.data.success) {
      originalUser = { ...originalUser, ...updates }
      error.value = ''
    } else {
      error.value = response.data.message || 'Failed to update profile'
    }
  } catch (err) {
    console.error(err)
    error.value = 'An error occurred while updating profile'
  }
}

const changePassword = async () => {
  error.value = ''
  success.value = ''

  if (!currentPassword.value) {
    error.value = 'Current password is required.'
    return
  }
  if (!newPassword.value || newPassword.value.length < 8) {
    error.value = 'New password must be at least 8 characters.'
    return
  }
  if (newPassword.value !== confirmPassword.value) {
    error.value = 'Confirm password does not match new password.'
    return
  }

  try {
    const user = JSON.parse(localStorage.getItem('user'))
    const response = await axios.post(`${import.meta.env.VITE_API_URL}/changePassword.php`, {
      user_id: user.id,
      current_password: currentPassword.value,
      new_password: newPassword.value,
    })

    if (response.data.success) {
      success.value = 'Password changed successfully.'
      currentPassword.value = ''
      newPassword.value = ''
      confirmPassword.value = ''
    } else {
      error.value = response.data.message || 'Failed to change password.'
    }
  } catch (err) {
    console.error(err)
    error.value = 'An error occurred while updating your password.'
  }
}

onMounted(() => {
  fetchUserInfo();
  const storedUser = localStorage.getItem('user');
  if (storedUser) {
    user.value = JSON.parse(storedUser);
    previewUrl.value = user.value.profile_picture
      ? `${imageBaseUrl}/uploads/profile_pictures/${user.value.profile_picture}`
      : `${imageBaseUrl}/uploads/profile_pictures/profilepicture.png`
  } else {
    previewUrl.value = `${imageBaseUrl}/uploads/profile_pictures/profilepicture.png`
  }
})
</script>