<template>
  <div class="p-8">
    <div class="mb-6">
      <div class="flex items-center justify-between mb-2">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
            <Bell class="w-6 h-6 text-blue-600" />
          </div>
          <div>
            <h1 class="text-gray-900">Notifications</h1>
            <p class="text-sm text-gray-600">{{ unreadCount }} Unread Notifications</p>
          </div>
        </div>
        <button
          @click="markAllAsRead"
          class="justify-center whitespace-nowrap rounded-md text-sm font-medium transition-all border bg-background text-foreground h-9 px-4 py-2 flex items-center gap-2"
        >
          <Check class="w-4 h-4" />
          Mark all as read
        </button>
      </div>
    </div>

    <div class="!space-y-3">
      <div
        v-for="notif in notifications"
        :key="notif.id"
        :class="[
          'text-card-foreground flex flex-col gap-6 rounded-xl border !p-4 transition-all hover:shadow-md',
          notif.is_read ? 'bg-white border-gray-200' : 'bg-blue-50 border-blue-200'
        ]"
      >
        <div class="flex gap-4">
          <div class="flex-shrink-0 mt-1">
            <CheckCheck class="w-5 h-5 text-green-500" />
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-3 mb-2">
              <div class="flex items-center gap-2 flex-wrap">
                <h3 class="text-gray-900">{{ notif.title }}</h3>
                <span v-if="!notif.is_read" class="w-2 h-2 bg-blue-500 rounded-full"></span>
              </div>
              <div class="flex items-center gap-2">
                <button
                  @click="markAsRead(notif.id)"
                  class="text-blue-600 hover:text-blue-700"
                  title="Mark as read"
                >
                  <Check class="w-4 h-4" />
                </button>
              </div>
            </div>
            <p class="text-sm text-gray-600 mb-3">{{ notif.message }}</p>
            <span class="text-xs text-gray-500">
              {{ timeAgo(notif.created_at) }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { Bell, Check, CheckCheck } from "lucide-vue-next";

const notifications = ref([]);
const role = "user";
const user_id = JSON.parse(localStorage.getItem("user")).id;

const fetchNotifications = async () => {
  const res = await fetch(`${import.meta.env.VITE_API_URL}/getNotifications.php`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ role, user_id }),
  });
  const data = await res.json();
  if (data.success) notifications.value = data.notifications;
};

const markAsRead = async (id) => {
  const res = await fetch(`${import.meta.env.VITE_API_URL}/markAsRead.php`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ action: "single", role, id, user_id }),
  });
  console.log(await res.json());
  fetchNotifications();
};

const markAllAsRead = async () => {
  await fetch(`${import.meta.env.VITE_API_URL}/markAsRead.php`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ action: "all", role, user_id }),
  });
  fetchNotifications();
};

const unreadCount = computed(() =>
  notifications.value.filter(n => n.is_read == 0).length
);

const timeAgo = (timestamp) => {
  const diff = (Date.now() - new Date(timestamp)) / 1000;
  if (diff < 60) return "Just now";
  if (diff < 3600) return Math.floor(diff / 60) + " minutes ago";
  if (diff < 86400) return Math.floor(diff / 3600) + " hours ago";
  return new Date(timestamp).toLocaleDateString();
};

onMounted(fetchNotifications);
</script>
