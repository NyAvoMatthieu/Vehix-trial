<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import {
  BellIcon,
  CheckCircleIcon,
  ExclamationTriangleIcon,
  InformationCircleIcon,
  TrashIcon,
  CheckIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  notifications: Object,
  unreadCount: Number
})

const filter = ref('all') // all, unread, read

const markAsRead = async (notificationId) => {
  try {
    await axios.post(`/notifications/${notificationId}/read`)
    router.reload({ only: ['notifications', 'unreadCount'] })
  } catch (error) {
    console.error('Erreur:', error)
  }
}

const markAllAsRead = async () => {
  try {
    await axios.post('/notifications/mark-all-read')
    router.reload({ only: ['notifications', 'unreadCount'] })
  } catch (error) {
    console.error('Erreur:', error)
  }
}

const deleteNotification = async (notificationId) => {
  if (!confirm('Supprimer cette notification ?')) return

  try {
    await axios.delete(`/notifications/${notificationId}`)
    router.reload({ only: ['notifications', 'unreadCount'] })
  } catch (error) {
    console.error('Erreur:', error)
  }
}

const deleteAll = async () => {
  if (!confirm('Supprimer toutes les notifications ?')) return

  try {
    await axios.delete('/notifications')
    router.reload({ only: ['notifications', 'unreadCount'] })
  } catch (error) {
    console.error('Erreur:', error)
  }
}

const handleNotificationClick = (notification) => {
  markAsRead(notification.id)
  if (notification.data.action_url) {
    router.visit(notification.data.action_url)
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
  return new Date(dateString).toLocaleDateString('fr-FR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>

<template>
  <AppLayout title="Notifications">
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Notifications
          </h2>
          <p class="text-sm text-gray-600 mt-1">
            {{ unreadCount }} non lue(s) sur {{ notifications.total }}
          </p>
        </div>

        <div class="flex items-center gap-3">
          <!-- Filter -->
          <select
            v-model="filter"
            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
          >
            <option value="all">Toutes</option>
            <option value="unread">Non lues</option>
            <option value="read">Lues</option>
          </select>

          <!-- Mark all as read -->
          <button
            v-if="unreadCount > 0"
            @click="markAllAsRead"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
          >
            <CheckIcon class="h-4 w-4 mr-2" />
            Tout marquer lu
          </button>

          <!-- Delete all -->
          <button
            v-if="notifications.total > 0"
            @click="deleteAll"
            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700"
          >
            <TrashIcon class="h-4 w-4 mr-2" />
            Tout supprimer
          </button>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-indigo-100 rounded-lg p-3">
                <BellIcon class="h-6 w-6 text-indigo-600" />
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total</p>
                <p class="text-2xl font-semibold text-gray-900">
                  {{ notifications.total }}
                </p>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                <CheckCircleIcon class="h-6 w-6 text-green-600" />
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Lues</p>
                <p class="text-2xl font-semibold text-gray-900">
                  {{ notifications.total - unreadCount }}
                </p>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-orange-100 rounded-lg p-3">
                <ExclamationTriangleIcon class="h-6 w-6 text-orange-600" />
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Non lues</p>
                <p class="text-2xl font-semibold text-gray-900">
                  {{ unreadCount }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Notifications List -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div v-if="notifications.data.length === 0" class="p-12 text-center">
            <BellIcon class="h-16 w-16 text-gray-300 mx-auto mb-4" />
            <h3 class="text-lg font-semibold text-gray-900 mb-2">
              Aucune notification
            </h3>
            <p class="text-gray-600">
              Vous n'avez pas encore de notifications.
            </p>
          </div>

          <div v-else class="divide-y divide-gray-200">
            <div
              v-for="notification in notifications.data"
              :key="notification.id"
              :class="[
                'p-6 hover:bg-gray-50 transition-colors cursor-pointer',
                !notification.read_at ? 'bg-indigo-50' : ''
              ]"
              @click="handleNotificationClick(notification)"
            >
              <div class="flex items-start gap-4">
                <!-- Icon -->
                <div
                  :class="[
                    'flex-shrink-0 h-12 w-12 rounded-full flex items-center justify-center',
                    getNotificationColor(notification.data.type)
                  ]"
                >
                  <component
                    :is="getNotificationIcon(notification.data.type)"
                    class="h-6 w-6"
                  />
                </div>

                <!-- Content -->
                <div class="flex-1 min-w-0">
                  <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                      <div class="flex items-center gap-2 mb-1">
                        <h3 class="text-base font-semibold text-gray-900">
                          {{ notification.data.title }}
                        </h3>
                        <span
                          v-if="!notification.read_at"
                          class="flex-shrink-0 h-2 w-2 bg-indigo-600 rounded-full"
                        ></span>
                      </div>

                      <p class="text-sm text-gray-600 mb-3">
                        {{ notification.data.message }}
                      </p>

                      <!-- Additional Details -->
                      <div v-if="notification.data.type === 'custom_fuel_price'" class="bg-orange-50 border border-orange-200 rounded-lg p-3 mb-3">
                        <div class="grid grid-cols-2 gap-2 text-xs">
                          <div>
                            <span class="text-gray-600">Prix officiel:</span>
                            <span class="ml-2 font-semibold">{{ notification.data.official_price }} Ar/L</span>
                          </div>
                          <div>
                            <span class="text-gray-600">Prix payé:</span>
                            <span class="ml-2 font-semibold">{{ notification.data.custom_price }} Ar/L</span>
                          </div>
                          <div>
                            <span class="text-gray-600">Différence:</span>
                            <span class="ml-2 font-semibold text-orange-600">
                              {{ notification.data.difference > 0 ? '+' : '' }}{{ notification.data.difference }} Ar/L
                              ({{ notification.data.percent_difference }}%)
                            </span>
                          </div>
                          <div>
                            <span class="text-gray-600">Station:</span>
                            <span class="ml-2 font-semibold">{{ notification.data.station }}</span>
                          </div>
                        </div>
                      </div>

                      <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">
                          {{ formatDate(notification.created_at) }}
                        </span>

                        <div class="flex items-center gap-2">
                          <button
                            v-if="!notification.read_at"
                            @click.stop="markAsRead(notification.id)"
                            class="text-xs text-indigo-600 hover:text-indigo-800 font-medium"
                          >
                            Marquer comme lu
                          </button>

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
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="notifications.data.length > 0" class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-700">
                Affichage de
                <span class="font-medium">{{ notifications.from }}</span>
                à
                <span class="font-medium">{{ notifications.to }}</span>
                sur
                <span class="font-medium">{{ notifications.total }}</span>
                notifications
              </div>

              <div class="flex gap-2">
                <Link
                  v-for="link in notifications.links"
                  :key="link.label"
                  :href="link.url"
                  :class="[
                    'px-3 py-2 text-sm rounded-md',
                    link.active
                      ? 'bg-indigo-600 text-white'
                      : 'bg-white text-gray-700 hover:bg-gray-50',
                    !link.url ? 'opacity-50 cursor-not-allowed' : ''
                  ]"
                  v-html="link.label"
                ></Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
