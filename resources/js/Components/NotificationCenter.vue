<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { 
  BellIcon, 
  CheckIcon, 
  XMarkIcon,
  TrashIcon,
  CheckCircleIcon,
  ExclamationTriangleIcon,
  InformationCircleIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  initialNotifications: Array,
  unreadCount: Number
})

const notifications = ref(props.initialNotifications || [])
const isOpen = ref(false)
const unreadCount = ref(props.unreadCount || 0)

const unreadNotifications = computed(() => 
  notifications.value.filter(n => !n.read_at)
)

const recentNotifications = computed(() => 
  notifications.value.slice(0, 5)
)

onMounted(() => {
  // Écouter les nouvelles notifications en temps réel
  if (window.Echo) {
    window.Echo.private(`App.Models.User.${props.userId}`)
      .notification((notification) => {
        notifications.value.unshift(notification)
        unreadCount.value++
        showToast(notification)
        playNotificationSound()
      })
  }

  // Charger les notifications
  loadNotifications()
})

const loadNotifications = async () => {
  try {
    const response = await fetch('/api/notifications')
    const data = await response.json()
    notifications.value = data.notifications
    unreadCount.value = data.unread_count
  } catch (error) {
    console.error('Erreur chargement notifications:', error)
  }
}

const markAsRead = async (notificationId) => {
  try {
    await axios.post(`/notifications/${notificationId}/read`)
    
    const notification = notifications.value.find(n => n.id === notificationId)
    if (notification && !notification.read_at) {
      notification.read_at = new Date().toISOString()
      unreadCount.value = Math.max(0, unreadCount.value - 1)
    }
  } catch (error) {
    console.error('Erreur:', error)
  }
}

const markAllAsRead = async () => {
  try {
    await axios.post('/notifications/mark-all-read')
    notifications.value.forEach(n => {
      n.read_at = new Date().toISOString()
    })
    unreadCount.value = 0
  } catch (error) {
    console.error('Erreur:', error)
  }
}

const deleteNotification = async (notificationId) => {
  try {
    await axios.delete(`/notifications/${notificationId}`)
    notifications.value = notifications.value.filter(n => n.id !== notificationId)
    if (!notifications.value.find(n => n.id === notificationId)?.read_at) {
      unreadCount.value = Math.max(0, unreadCount.value - 1)
    }
  } catch (error) {
    console.error('Erreur:', error)
  }
}

const handleNotificationClick = (notification) => {
  markAsRead(notification.id)
  if (notification.data.action_url) {
    router.visit(notification.data.action_url)
    isOpen.value = false
  }
}

const getNotificationIcon = (type) => {
  const icons = {
    'vehicle_validation': CheckCircleIcon,
    'new_vehicle': InformationCircleIcon,
    'new_user': InformationCircleIcon,
    'custom_fuel_price': ExclamationTriangleIcon,
  }
  return icons[type] || BellIcon
}

const getNotificationColor = (type) => {
  const colors = {
    'vehicle_validation': 'text-green-600 bg-green-100',
    'new_vehicle': 'text-blue-600 bg-blue-100',
    'new_user': 'text-indigo-600 bg-indigo-100',
    'custom_fuel_price': 'text-orange-600 bg-orange-100',
  }
  return colors[type] || 'text-gray-600 bg-gray-100'
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  const now = new Date()
  const diff = now - date
  
  const minutes = Math.floor(diff / 60000)
  const hours = Math.floor(diff / 3600000)
  const days = Math.floor(diff / 86400000)
  
  if (minutes < 1) return 'À l\'instant'
  if (minutes < 60) return `Il y a ${minutes} min`
  if (hours < 24) return `Il y a ${hours}h`
  if (days < 7) return `Il y a ${days}j`
  
  return date.toLocaleDateString('fr-FR', { 
    day: 'numeric', 
    month: 'short' 
  })
}

const showToast = (notification) => {
  const toast = document.createElement('div')
  toast.className = 'fixed top-4 right-4 bg-white shadow-xl rounded-lg p-4 max-w-sm z-[9999] animate-slide-in-right'
  toast.innerHTML = `
    <div class="flex items-start gap-3">
      <div class="flex-shrink-0">
        <div class="h-10 w-10 rounded-full ${getNotificationColor(notification.data.type)} flex items-center justify-center">
          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
          </svg>
        </div>
      </div>
      <div class="flex-1 min-w-0">
        <p class="text-sm font-semibold text-gray-900">${notification.data.title}</p>
        <p class="text-sm text-gray-600 mt-1">${notification.data.message}</p>
      </div>
      <button onclick="this.parentElement.parentElement.remove()" class="flex-shrink-0 text-gray-400 hover:text-gray-600">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
  `
  
  document.body.appendChild(toast)
  setTimeout(() => toast.remove(), 5000)
}

const playNotificationSound = () => {
  const audio = new Audio('/sounds/notification.mp3')
  audio.volume = 0.5
  audio.play().catch(() => {}) // Ignore errors if sound fails
}

onUnmounted(() => {
  if (window.Echo) {
    window.Echo.leave(`App.Models.User.${props.userId}`)
  }
})
</script>

<template>
  <div class="relative">
    <!-- Notification Bell -->
    <button
      @click="isOpen = !isOpen"
      class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-lg transition-colors"
    >
      <BellIcon class="h-6 w-6" />
      
      <!-- Unread Badge -->
      <span
        v-if="unreadCount > 0"
        class="absolute top-0 right-0 inline-flex items-center justify-center h-5 w-5 text-xs font-bold text-white bg-red-600 rounded-full animate-pulse"
      >
        {{ unreadCount > 99 ? '99+' : unreadCount }}
      </span>
    </button>

    <!-- Dropdown Panel -->
    <Transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="transform opacity-0 scale-95"
      enter-to-class="transform opacity-100 scale-100"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="transform opacity-100 scale-100"
      leave-to-class="transform opacity-0 scale-95"
    >
      <div
        v-if="isOpen"
        class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-2xl border border-gray-200 z-50 max-h-[600px] overflow-hidden flex flex-col"
        @click.stop
      >
        <!-- Header -->
        <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-blue-50">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">
              Notifications
              <span v-if="unreadCount > 0" class="ml-2 text-sm text-indigo-600">
                ({{ unreadCount }})
              </span>
            </h3>
            <div class="flex items-center gap-2">
              <button
                v-if="unreadCount > 0"
                @click="markAllAsRead"
                class="text-xs text-indigo-600 hover:text-indigo-800 font-medium"
              >
                Tout marquer lu
              </button>
              <button
                @click="isOpen = false"
                class="text-gray-400 hover:text-gray-600"
              >
                <XMarkIcon class="h-5 w-5" />
              </button>
            </div>
          </div>
        </div>

        <!-- Notifications List -->
        <div class="overflow-y-auto flex-1">
          <div v-if="notifications.length === 0" class="p-8 text-center">
            <BellIcon class="h-12 w-12 text-gray-300 mx-auto mb-3" />
            <p class="text-gray-500 text-sm">Aucune notification</p>
          </div>

          <div
            v-for="notification in recentNotifications"
            :key="notification.id"
            @click="handleNotificationClick(notification)"
            :class="[
              'p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors',
              !notification.read_at ? 'bg-indigo-50' : ''
            ]"
          >
            <div class="flex items-start gap-3">
              <div :class="['flex-shrink-0 h-10 w-10 rounded-full flex items-center justify-center', getNotificationColor(notification.data.type)]">
                <component :is="getNotificationIcon(notification.data.type)" class="h-5 w-5" />
              </div>

              <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2">
                  <p class="text-sm font-semibold text-gray-900">
                    {{ notification.data.title }}
                  </p>
                  <span v-if="!notification.read_at" class="flex-shrink-0 h-2 w-2 bg-indigo-600 rounded-full"></span>
                </div>
                
                <p class="text-sm text-gray-600 mt-1 line-clamp-2">
                  {{ notification.data.message }}
                </p>

                <div class="flex items-center justify-between mt-2">
                  <span class="text-xs text-gray-500">
                    {{ formatDate(notification.created_at) }}
                  </span>
                  
                  <button
                    @click.stop="deleteNotification(notification.id)"
                    class="text-gray-400 hover:text-red-600 transition-colors"
                  >
                    <TrashIcon class="h-4 w-4" />
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="p-3 border-t border-gray-200 bg-gray-50">
          <Link
            :href="route('notifications.index')"
            class="block text-center text-sm text-indigo-600 hover:text-indigo-800 font-medium"
            @click="isOpen = false"
          >
            Voir toutes les notifications
          </Link>
        </div>
      </div>
    </Transition>

    <!-- Overlay -->
    <div
      v-if="isOpen"
      @click="isOpen = false"
      class="fixed inset-0 z-40"
    ></div>
  </div>
</template>

<style scoped>
@keyframes slide-in-right {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

.animate-slide-in-right {
  animation: slide-in-right 0.3s ease-out;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>