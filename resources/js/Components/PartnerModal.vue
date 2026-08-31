<template>
  <Teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
      @click="closeModal"
    >
      <div
        class="bg-white rounded-lg max-w-4xl w-full max-h-screen overflow-y-auto"
        @click.stop
      >
        <div class="p-6">
          <div class="flex justify-between items-start mb-4">
            <h3 class="text-2xl font-bold text-gray-900">{{ currentPartner?.title }}</h3>
            <div class="flex items-center space-x-2">
              <button
                @click="prevPartner"
                class="p-2 rounded-full bg-gray-200 hover:bg-gray-300"
                :disabled="!hasPrevPartner"
              >
                <i class="bi bi-chevron-left"></i>
              </button>
              <button
                @click="nextPartner"
                class="p-2 rounded-full bg-gray-200 hover:bg-gray-300"
                :disabled="!hasNextPartner"
              >
                <i class="bi bi-chevron-right"></i>
              </button>
              <button @click="closeModal" class="text-gray-500 hover:text-gray-700 text-2xl ml-2">&times;</button>
            </div>
          </div>

          <!-- Carousel -->
          <div class="relative mb-6">
            <div class="overflow-hidden rounded-lg">
              <div class="flex transition-transform duration-300 ease-in-out" :style="{ transform: `translateX(-${currentSlideIndex * 100}%)` }">
                <div
                  v-for="(image, index) in currentPartner?.images"
                  :key="index"
                  class="min-w-full"
                >
                  <img
                    :src="image.url"
                    :alt="image.caption"
                    class="w-full h-64 object-cover"
                  >
                  <p class="text-center mt-2 text-gray-600">{{ image.caption }}</p>
                </div>
              </div>
            </div>

            <!-- Contrôles du carousel -->
            <button
              @click="prevSlide"
              class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-70 hover:bg-opacity-100 rounded-full p-2 shadow-md"
            >
              <i class="bi bi-chevron-left text-gray-800"></i>
            </button>
            <button
              @click="nextSlide"
              class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-70 hover:bg-opacity-100 rounded-full p-2 shadow-md"
            >
              <i class="bi bi-chevron-right text-gray-800"></i>
            </button>

            <!-- Indicateurs -->
            <div class="flex justify-center mt-4 space-x-2">
              <button
                v-for="(image, index) in currentPartner?.images"
                :key="index"
                @click="goToSlide(index)"
                :class="[
                  'w-3 h-3 rounded-full',
                  index === currentSlideIndex ? 'bg-blue-500' : 'bg-gray-300'
                ]"
              ></button>
            </div>
          </div>

          <!-- Description -->
          <div class="text-gray-600 mb-6">{{ currentPartner?.description }}</div>

          <!-- Boutons -->
          <div class="flex space-x-4">
            <a
              v-if="currentPartner?.link"
              :href="currentPartner.link"
              target="_blank"
              class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition-colors"
            >
              Visiter le site
            </a>
            <button @click="closeModal" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors">
              Fermer
            </button>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed, onUnmounted } from 'vue'

const isOpen = ref(false)
const currentPartnerKey = ref('')
const currentSlideIndex = ref(0)

// Données des partenaires (vous pouvez aussi les passer en props ou les récupérer via API)
const partnersData = ref({
  'hasnreziga': {
    title: 'HASNREZIGA Informatique',
    description: 'Spécialiste en développement d\'applications web et mobiles, avec expertise en intelligence artificielle et gestion de véhicules.',
    images: [
      { url: 'assets/img/hasnreziga.png', caption: 'Logo HASNREZIGA' },
      { url: 'assets/img/hasnreziga.png', caption: 'Interface Dashboard' },
      { url: 'assets/img/hasnreziga.png', caption: 'Application Mobile' }
    ],
    link: 'https://www.hasnreziga.com'
  },
  'Test1': {
    title: 'HASNREZIGA Informatique 1',
    description: 'Partenaire premium de niveau Or avec un engagement fort dans la qualité de service.',
    images: [
      { url: 'assets/img/hasnreziga.png', caption: 'Logo HASNREZIGA' },
      { url: 'assets/img/hasnreziga.png', caption: 'Interface Dashboard' },
      { url: 'assets/img/hasnreziga.png', caption: 'Application Mobile' }
    ],
    link: '#'
  },
  'Test2': {
    title: 'HASNREZIGA Informatique 2',
    description: 'Partenaire de niveau Argent avec un excellent rapport qualité-service.',
    images: [
      { url: 'assets/img/hasnreziga.png', caption: 'Logo HASNREZIGA' },
      { url: 'assets/img/hasnreziga.png', caption: 'Interface Dashboard' },
      { url: 'assets/img/hasnreziga.png', caption: 'Application Mobile' }
    ],
    link: '#'
  },
  'Test3': {
    title: 'HASNREZIGA Informatique 3',
    description: 'Partenaire de niveau Bronze avec un bon niveau de service.',
    images: [
      { url: 'assets/img/hasnreziga.png', caption: 'Logo HASNREZIGA' },
      { url: 'assets/img/hasnreziga.png', caption: 'Interface Dashboard' },
      { url: 'assets/img/hasnreziga.png', caption: 'Application Mobile' }
    ],
    link: '#'
  },
  'Test4': {
    title: 'HASNREZIGA Informatique 4',
    description: 'Partenaire premium de niveau Or avec un engagement fort dans la qualité de service.',
    images: [
      { url: 'assets/img/hasnreziga.png', caption: 'Logo HASNREZIGA' },
      { url: 'assets/img/hasnreziga.png', caption: 'Interface Dashboard' },
      { url: 'assets/img/hasnreziga.png', caption: 'Application Mobile' }
    ],
    link: '#'
  },
  'Test5': {
    title: 'HASNREZIGA Informatique 5',
    description: 'Partenaire de niveau Argent avec un excellent rapport qualité-service.',
    images: [
      { url: 'assets/img/hasnreziga.png', caption: 'Logo HASNREZIGA' },
      { url: 'assets/img/hasnreziga.png', caption: 'Interface Dashboard' },
      { url: 'assets/img/hasnreziga.png', caption: 'Application Mobile' }
    ],
    link: '#'
  },
  'Test6': {
    title: 'HASNREZIGA Informatique 6',
    description: 'Partenaire de niveau Bronze avec un bon niveau de service.',
    images: [
      { url: 'assets/img/hasnreziga.png', caption: 'Logo HASNREZIGA' },
      { url: 'assets/img/hasnreziga.png', caption: 'Interface Dashboard' },
      { url: 'assets/img/hasnreziga.png', caption: 'Application Mobile' }
    ],
    link: '#'
  },
  'Test7': {
    title: 'HASNREZIGA Informatique 7',
    description: 'Partenaire de niveau Bronze avec un bon niveau de service.',
    images: [
      { url: 'assets/img/hasnreziga.png', caption: 'Logo HASNREZIGA' },
      { url: 'assets/img/hasnreziga.png', caption: 'Interface Dashboard' },
      { url: 'assets/img/hasnreziga.png', caption: 'Application Mobile' }
    ],
    link: '#'
  }
})

// Obtenir toutes les clés des partenaires
const partnerKeys = computed(() => Object.keys(partnersData.value))

// Index du partenaire actuel
const currentPartnerIndex = computed(() => {
  return partnerKeys.value.indexOf(currentPartnerKey.value)
})

// Vérifier s'il y a un partenaire précédent
const hasPrevPartner = computed(() => {
  return currentPartnerIndex.value > 0
})

// Vérifier s'il y a un partenaire suivant
const hasNextPartner = computed(() => {
  return currentPartnerIndex.value < partnerKeys.value.length - 1
})

// Partenaire actuel
const currentPartner = computed(() => {
  return partnersData.value[currentPartnerKey.value]
})

// Navigation entre les partenaires
const prevPartner = () => {
  if (hasPrevPartner.value) {
    const newIndex = currentPartnerIndex.value - 1
    currentPartnerKey.value = partnerKeys.value[newIndex]
    currentSlideIndex.value = 0 // Réinitialiser le carousel
  }
}

const nextPartner = () => {
  if (hasNextPartner.value) {
    const newIndex = currentPartnerIndex.value + 1
    currentPartnerKey.value = partnerKeys.value[newIndex]
    currentSlideIndex.value = 0 // Réinitialiser le carousel
  }
}

const openModal = (partnerKey) => {
  currentPartnerKey.value = partnerKey
  currentSlideIndex.value = 0
  isOpen.value = true
  document.body.style.overflow = 'hidden'
}

const closeModal = () => {
  isOpen.value = false
  document.body.style.overflow = ''
}

const goToSlide = (index) => {
  currentSlideIndex.value = index
}

const nextSlide = () => {
  if (currentPartner.value && currentSlideIndex.value < currentPartner.value.images.length - 1) {
    currentSlideIndex.value++
  } else if (currentPartner.value) {
    currentSlideIndex.value = 0
  }
}

const prevSlide = () => {
  if (currentPartner.value && currentSlideIndex.value > 0) {
    currentSlideIndex.value = currentSlideIndex.value - 1
  } else if (currentPartner.value) {
    currentSlideIndex.value = currentPartner.value.images.length - 1
  }
}

// Gestion des événements clavier
const handleKeyDown = (e) => {
  if (!isOpen.value) return

  if (e.key === 'Escape') {
    closeModal()
  } else if (e.key === 'ArrowRight') {
    // Si Shift est pressé, naviguer entre les partenaires
    if (e.shiftKey) {
      nextPartner()
    } else {
      nextSlide()
    }
  } else if (e.key === 'ArrowLeft') {
    // Si Shift est pressé, naviguer entre les partenaires
    if (e.shiftKey) {
      prevPartner()
    } else {
      prevSlide()
    }
  }
}

// Écouter les événements clavier
document.addEventListener('keydown', handleKeyDown)

// Nettoyer les événements
onUnmounted(() => {
  document.removeEventListener('keydown', handleKeyDown)
})

defineExpose({
  openModal,
  closeModal
})
</script>
