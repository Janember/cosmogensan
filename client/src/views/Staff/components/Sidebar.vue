<template>
  <div class="hidden lg:flex lg:!w-80 lg:flex-col lg:fixed lg:inset-y-0 bg-white border-r">
    <!-- User Info -->
    <div class="flex flex-col flex-grow pt-5 pb-4 overflow-y-auto">
      <!-- Logo -->
      <div class="flex items-center flex-shrink-0 px-6 mb-8">
        <img
          src="/cosmopolitanLogo.png"
          alt="Cosmopolitan Memorial Chapels"
          class="h-12 w-auto"
        />
      </div>
      <nav class="flex-1 px-4 space-y-2">
        <button
          v-for="item in navigationItems"
          :key="item.id"
          class="h-12 mb-2 w-full flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium"
          :class="currentSection === item.destination ? 'bg-black text-white' : 'hover:!bg-gray-100'"
          @click="handleSectionChange(item.destination)"
        >
          <component :is="item.icon" class="h-5 w-5 mr-3" />
          <div class="flex-1 text-left">
            <div class="font-medium text-sm">{{ item.label }}</div>
            <div class="text-xs text-gray-500">{{ item.description }}</div>
          </div>
          <span
            v-if="item.badge"
            class="text-xs bg-gray-200 px-2 py-0.5 rounded-full"
          >
            {{ item.badge }}
          </span>
        </button>
      </nav>
      <hr class="mx-4 my-4 border-gray-200" />

      <div class="px-4 space-y-2 text-sm">
        <button 
          :class="currentSection === '/Staff/notifications' ? 'bg-black text-white' : 'hover:!bg-gray-100'"
          class="w-full flex items-center space-x-3 px-3 py-2 mb-2 rounded-md"
          @click="handleSectionChange('/Staff/notifications')"
        >
          <icons.Bell class="mr-3 h-5 w-5" />
          <span>Notifications</span>
          <span v-if="unreadCount > 0"
                class="ml-auto text-xs bg-red-200 text-red-700 px-2 py-0.5 rounded-full">
            {{ unreadCount > 9 ? '9+' : unreadCount }}
          </span>
        </button>
        <!-- <button
          class="w-full flex items-center space-x-3 px-3 py-2 hover:!bg-gray-100 rounded-md"
          @click="handleSectionChange('')"
        >
          <icons.Settings class="mr-3 h-5 w-5" />
          <span>Settings</span>
        </button> -->
      </div>

      <hr class="mx-4 my-4 border-gray-200" />

      <div class="!px-4">
        <div class="flex items-center space-x-3 !p-3 !mb-2 rounded-lg bg-gray-50">
          <img
            :src="profile_picture"
            alt="Profile placeholder"
            class="w-10 h-10 mr-3 rounded-full object-cover"
          />
          <div class="flex-1">
            <p class="font-medium text-sm">{{ username }}</p>
            <p class="text-xs text-gray-500">{{ email }}</p>
          </div>
        </div>

        <button
          class="w-full flex items-center space-x-3 !px-4 !py-2 rounded-md !text-red-600 hover:text-red-700 hover:!bg-red-50 !text-sm"
          @click="onLogout"
        >
          <icons.LogOut class="h-4 w-4 mr-3" />
          <span>Logout</span>
        </button>
      </div>

      <!-- <v-list density="compact" nav>
        <v-list-item
          prepend-icon="mdi-logout"
          title="Logout"
          @click="handleLogout"
        ></v-list-item>
      </v-list> -->
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'
import {
  LayoutDashboard,
  Calendar,
  Church,
  CalendarCheck,
  CreditCard,
  User,
  LogOut,
  Menu,
  X,
  Bell,
  Settings
} from 'lucide-vue-next'

const router = useRouter()
const user = {}
const username = ref('')
const email = ref('')
const profile_picture = ref('')
const imageBaseUrl = import.meta.env.VITE_IMAGE_URL
const unreadCount = ref(0);
const lastUnread = ref(0);
const role = 'staff';
const user_id = JSON.parse(localStorage.getItem("user")).id;
let interval;

const notificationSound = new Audio('/sounds/notification.mp3'); 

const icons = {
  LogOut,
  Menu,
  X,
  Bell,
  Settings
}

const props = defineProps({
  currentSection: { type: String, required: true },
  onSectionChange: { type: Function, required: true },
  onLogout: { type: Function, required: true }
})

onBeforeUnmount(() => {
  clearInterval(interval);
});

onMounted(() => {
  user.value = JSON.parse(localStorage.getItem('user'))
  username.value = user ? user.value.username : 'Default User'
  email.value = user ? user.value.email : 'Default User'
  profile_picture.value = user.value.profile_picture
  ? `${imageBaseUrl}/uploads/profile_pictures/${user.value.profile_picture}`
  : `${imageBaseUrl}/uploads/profile_pictures/profilepicture.png`
  fetchUnreadCount();
  interval = setInterval(fetchUnreadCount, 15000);
})

const handleLogout = () => {
  localStorage.removeItem('user')
  router.push('/login')
}

if (!localStorage.getItem('user')) {
  router.push('/login') 
}

const navigationItems = [
  {
    id: 'dashboard',
    label: 'Dashboard',
    icon: LayoutDashboard,
    description: 'Overview and statistics',
    destination: '/Staff/dashboard'
  },
  {
    id: 'chapels',
    label: 'Chapels',
    icon: Church,
    description: 'Change chapel status',
    destination: '/Staff/chapels'
  },
  {
    id: 'reserve-chapel',
    label: 'Reserve Chapel',
    icon: Calendar,
    description: 'Book chapel services',
    destination: '/Staff/reserve'
  },
  {
    id: 'reservations',
    label: 'Reservations',
    icon: CalendarCheck,
    description: 'Manage bookings',
    destination: '/Staff/reservations'
  },
  {
    id: 'transactions',
    label: 'Transactions',
    icon: CreditCard,
    description: 'Financial records',
    destination: '/Staff/transactions'
  },
  {
    id: 'manage-account',
    label: 'Manage Account',
    icon: User,
    description: 'Profile settings',
    destination: '/Staff/account'
  }
]

const handleSectionChange = (itemDestination) => {
  props.onSectionChange(itemDestination);
  router.push(itemDestination);
}

const fetchUnreadCount = async () => {
  try {
    const res = await fetch(`${import.meta.env.VITE_API_URL}/getNotifications.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ role, user_id })
    });
    const data = await res.json();
    if (data.success) {
      // count unread notifications
      const count = data.notifications.filter(n => !n.is_read).length;

      // Play sound if new notifications arrived
      if (count > lastUnread.value) {
        notificationSound.play().catch(() => {}); // ignore play errors
      }

      unreadCount.value = count;
      lastUnread.value = count;
    }
  } catch (err) {
    console.error('Error fetching notifications:', err);
  }
};
</script>