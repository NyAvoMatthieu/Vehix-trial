<script setup>
import { ref, onMounted } from 'vue'

const notificationPermission = ref(Notification.permission)
const isPWA = ref(false)

onMounted(() => {
  // Détecter si l'app est en mode PWA
  isPWA.value = window.matchMedia('(display-mode: standalone)').matches ||
                window.navigator.standalone === true

  // Demander la permission automatiquement en mode PWA
  if (isPWA.value && notificationPermission.value === 'default') {
    setTimeout(() => {
      requestNotificationPermission()
    }, 3000) // Attendre 3s après le chargement
  }

  // Écouter les nouvelles notifications
  if (window.Echo) {
    window.Echo.private(`App.Models.User.${window.Laravel.user?.id}`)
      .notification((notification) => {
        showPWANotification(notification)
      })
  }
})

const requestNotificationPermission = async () => {
  if ('Notification' in window) {
    const permission = await Notification.requestPermission()
    notificationPermission.value = permission
    
    if (permission === 'granted') {
      showTestNotification()
    }
  }
}

const showTestNotification = () => {
  if ('serviceWorker' in navigator && notificationPermission.value === 'granted') {
    navigator.serviceWorker.ready.then((registration) => {
      registration.showNotification('Vehix', {
        body: '✅ Notifications activées ! Vous recevrez maintenant les alertes importantes.',
        icon: '/icon-192x192.png',
        badge: '/icon-96x96.png',
        vibrate: [200, 100, 200],
        tag: 'vehix-welcome',
        requireInteraction: false,
        actions: [
          {
            action: 'close',
            title: 'Fermer',
          }
        ]
      })
    })
  }
}

const showPWANotification = (notification) => {
  if ('serviceWorker' in navigator && notificationPermission.value === 'granted') {
    navigator.serviceWorker.ready.then((registration) => {
      const options = {
        body: notification.data.message,
        icon: '/icon-192x192.png',
        badge: '/icon-96x96.png',
        vibrate: [200, 100, 200],
        tag: `vehix-${notification.data.type}-${notification.id}`,
        requireInteraction: true,
        data: {
          url: notification.data.action_url,
          notificationId: notification.id
        },
        actions: [
          {
            action: 'view',
            title: notification.data.action_text || 'Voir',
          },
          {
            action: 'close',
            title: 'Fermer',
          }
        ]
      }

      // Ajouter un emoji selon le type
      const emojis = {
        'vehicle_validation': '✅',
        'new_vehicle': '🚗',
        'new_user': '👤',
        'custom_fuel_price': '⛽',
      }

      const emoji = emojis[notification.data.type] || '🔔'
      options.body = `${emoji} ${options.body}`

      registration.showNotification(notification.data.title, options)
    })
  }
}

const testNotification = () => {
  showPWANotification({
    id: 'test-' + Date.now(),
    data: {
      type: 'vehicle_validation',
      title: '🧪 Test de notification',
      message: 'Ceci est une notification de test pour vérifier le bon fonctionnement du système.',
      action_url: '/dashboard',
      action_text: 'Aller au tableau de bord'
    }
  })
}
</script>

<template>
  <div>
    <!-- Prompt pour activer les notifications -->
    <Transition name="slide-down">
      <div
        v-if="isPWA && notificationPermission === 'default'"
        class="fixed top-0 left-0 right-0 bg-gradient-to-r from-indigo-600 to-purple-600 text-white p-4 shadow-lg z-[9999]"
      >
        <div class="max-w-4xl mx-auto flex items-center justify-between gap-4">
          <div class="flex items-center gap-3">
            <div class="flex-shrink-0">
              <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-bold">Activer les notifications</h3>
              <p class="text-sm opacity-90">Restez informé des événements importants</p>
            </div>
          </div>

          <div class="flex gap-2">
            <button
              @click="requestNotificationPermission"
              class="px-6 py-2 bg-white text-indigo-600 rounded-lg font-semibold hover:bg-gray-100 transition-colors"
            >
              Activer
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Badge statut -->
    <div v-if="isPWA" class="fixed bottom-4 left-4 z-50">
      <Transition name="fade">
        <div
          v-if="notificationPermission === 'granted'"
          class="bg-green-500 text-white px-3 py-2 rounded-full shadow-lg flex items-center gap-2 text-sm"
        >
          <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
          </svg>
          <span>Notifications ON</span>
        </div>
      </Transition>
    </div>

    <!-- Bouton de test (dev only) -->
    <button
      v-if="import.meta.env.DEV && notificationPermission === 'granted'"
      @click="testNotification"
      class="fixed bottom-4 right-4 z-50 bg-purple-600 text-white px-4 py-2 rounded-lg shadow-lg hover:bg-purple-700 transition-colors text-sm font-medium"
    >
      🧪 Test Notification
    </button>
  </div>
</template>

<style scoped>
.slide-down-enter-active,
.slide-down-leave-active {
  transition: transform 0.3s ease-out;
}

.slide-down-enter-from {
  transform: translateY(-100%);
}

.slide-down-leave-to {
  transform: translateY(-100%);
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>