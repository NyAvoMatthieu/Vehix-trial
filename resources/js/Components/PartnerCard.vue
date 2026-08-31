<template>
  <div class="relative group overflow-hidden rounded-lg">
    <!-- Badge en haut à gauche 
    <span class="absolute top-2 left-2 bg-blue-500 text-white text-xs font-bold px-2 py-1 rounded z-10">
      {{-- badge --}}
    </span>-->
    
    <!-- Conteneur principal -->
    <div class="bg-gray-50 p-6 rounded-lg flex items-center justify-center relative h-48 transition-all duration-300 group-hover:shadow-lg">
      <!-- Logo -->
      <img 
        :src="logoSrc" 
        :alt="name" 
        class="max-w-full h-20 object-contain opacity-70 transition-transform duration-300 group-hover:opacity-100 group-hover:scale-110"
      >
      
      <!-- Texte au centre en bas au survol -->
      <div class="absolute bottom-0 left-0 right-0 bg-[#4f5c50] text-white text-center py-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
        <span class="text-sm font-medium">{{ details }}</span>
      </div>
      
      <!-- Icônes à droite -->
      <div class="absolute top-2 right-2 flex space-x-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
        <!-- Icône Plus -->
        <button 
          @click="$emit('open-modal', partnerKey)" 
          class="bg-[#4f5c50] text-white p-2 rounded-full hover:bg-[#eb912b] transition-colors"
        >
          <i class="bi bi-plus text-sm"></i>
        </button>
        <!-- Icône Lien -->
        <a 
          :href="link" 
          target="_blank" 
          class="bg-gray-500 text-white p-2 rounded-full hover:bg-gray-600 transition-colors"
        >
          <i class="bi bi-link text-sm"></i>
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  logoSrc: String,
  name: String,
  badge: String,
  link: String,
  partnerKey: String,
  details: String // Nouvelle propriété pour les détails
})

defineEmits(['open-modal'])
</script>