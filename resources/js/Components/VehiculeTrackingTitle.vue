<template>
    <p class="text-lg md:text-xl mb-8">
      Application de Suivi 
      <span ref="mainSpan" class="text-white-400"></span>
    </p>
</template>

<script setup>
import { onMounted, onUnmounted, ref } from 'vue'
import Typed from 'typed.js'

const mainSpan = ref(null)
let typedInstance = null

// Textes à afficher en boucle
const texts = [
  "d'état de votre véhicule",
  "maintenance"
]

onMounted(() => {
  let currentIndex = 0

  const initializeTyped = () => {
    if (typedInstance) {
      typedInstance.destroy()
    }

    typedInstance = new Typed(mainSpan.value, {
      strings: [texts[currentIndex]],
      typeSpeed: 60,
      backSpeed: 40,
      backDelay: 1500,
      startDelay: 500,
      showCursor: true,
      cursorChar: '|',
      onComplete: (self) => {
        // Attendre un moment avant de supprimer le texte
        setTimeout(() => {
          // Passer au prochain texte
          currentIndex = (currentIndex + 1) % texts.length
          // Recommencer automatiquement
          initializeTyped()
        }, 1500)
      }
    })
  }

  // Démarrer la première fois
  initializeTyped()
})

onUnmounted(() => {
  if (typedInstance) {
    typedInstance.destroy()
  }
})
</script>