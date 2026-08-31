<template>
  <div ref="counterWrapper" class="counter-wrapper">
    <span ref="counterElement" class="counter-value">{{ displayValue }}</span>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch, computed } from 'vue';
import { CountUp } from 'countup.js';

// Props avec valeurs par défaut optimisées
const props = defineProps({
  target: {
    type: Number,
    required: true,
    validator: (value) => !isNaN(value) // Validation du nombre
  },
  duration: {
    type: Number,
    default: 2.5,
    validator: (value) => value > 0 // Durée positive
  },
  prefix: {
    type: String,
    default: ''
  },
  suffix: {
    type: String,
    default: ''
  },
  // Nombre de décimales (0 par défaut pour les entiers)
  decimals: {
    type: Number,
    default: 0,
    validator: (value) => value >= 0 && value <= 10
  },
  // Séparateur des milliers
  separator: {
    type: String,
    default: ' '
  },
  // Démarrer uniquement quand visible (optimise les performances)
  startOnVisible: {
    type: Boolean,
    default: true
  },
  // Seuil de visibilité (0.5 = 50% visible)
  visibilityThreshold: {
    type: Number,
    default: 0.5,
    validator: (value) => value >= 0 && value <= 1
  },
  // Animation avec easing (courbe d'accélération)
  useEasing: {
    type: Boolean,
    default: true
  },
  // Démarrer depuis une valeur spécifique (au lieu de 0)
  startValue: {
    type: Number,
    default: 0
  }
});

// Événements émis vers le parent
const emit = defineEmits(['started', 'completed', 'error']);

// Références
const counterElement = ref(null);
const counterWrapper = ref(null);
const isAnimating = ref(false);
const hasAnimated = ref(false); // Pour éviter les animations multiples
let observer = null;
let countUp = null;

// Valeur affichée initialement
const displayValue = ref(props.prefix + props.startValue + props.suffix);

// Computed pour vérifier si l'animation peut démarrer
const canAnimate = computed(() => {
  return counterElement.value && !isAnimating.value;
});

/**
 * Fonction principale pour démarrer l'animation
 */
const startCounting = () => {
  // Vérifications de sécurité
  if (!canAnimate.value) {
    console.warn('AnimatedCounter: Cannot start animation - element not ready or already animating');
    return;
  }

  // Éviter les animations multiples si déjà animé
  if (hasAnimated.value && props.startOnVisible) {
    return;
  }

  try {
    isAnimating.value = true;

    // Configuration de CountUp
    const options = {
      startVal: props.startValue,
      duration: props.duration,
      separator: props.separator,
      prefix: props.prefix,
      suffix: props.suffix,
      decimalPlaces: props.decimals,
      useEasing: props.useEasing,
      useGrouping: true, // Grouper les milliers
      enableScrollSpy: false // Désactiver le scroll spy interne de CountUp
    };

    // Création de l'instance CountUp avec l'élément DOM réel
    countUp = new CountUp(counterElement.value, props.target, options);

    // Vérification d'erreur
    if (countUp.error) {
      console.error('CountUp initialization error:', countUp.error);
      emit('error', countUp.error);
      isAnimating.value = false;
      return;
    }

    // Callback de fin d'animation
    const onComplete = () => {
      isAnimating.value = false;
      hasAnimated.value = true;
      emit('completed', props.target);
    };

    // Démarrer l'animation avec callback
    countUp.start(onComplete);
    emit('started', props.target);

  } catch (error) {
    console.error('AnimatedCounter error:', error);
    emit('error', error);
    isAnimating.value = false;
  }
};

/**
 * Réinitialiser et redémarrer l'animation
 */
const restart = () => {
  if (countUp) {
    countUp.reset();
  }
  hasAnimated.value = false;
  startCounting();
};

/**
 * Mettre en pause l'animation
 */
const pause = () => {
  if (countUp) {
    countUp.pauseResume();
  }
};

/**
 * Mettre à jour vers une nouvelle valeur
 */
const updateValue = (newValue) => {
  if (countUp && counterElement.value) {
    countUp.update(newValue);
  }
};

/**
 * Hook de montage - Configuration de l'IntersectionObserver
 */
onMounted(() => {
  // Si on ne veut pas attendre la visibilité, démarrer immédiatement
  if (!props.startOnVisible) {
    // Petit délai pour s'assurer que le DOM est prêt
    setTimeout(() => {
      startCounting();
    }, 100);
    return;
  }

  // Créer l'IntersectionObserver pour détecter la visibilité
  observer = new IntersectionObserver(
    (entries) => {
      entries.forEach(entry => {
        // L'élément entre dans la zone visible
        if (entry.isIntersecting && !hasAnimated.value) {
          startCounting();
          // Arrêter d'observer après le déclenchement
          observer.unobserve(entry.target);
        }
      });
    },
    {
      threshold: props.visibilityThreshold, // Seuil de visibilité
      rootMargin: '0px' // Marge autour de la zone de détection
    }
  );

  // Observer le wrapper
  if (counterWrapper.value) {
    observer.observe(counterWrapper.value);
  } else {
    console.warn('AnimatedCounter: counterWrapper not found');
  }
});

/**
 * Hook de démontage - Nettoyage
 */
onUnmounted(() => {
  // Déconnecter l'observer
  if (observer) {
    observer.disconnect();
    observer = null;
  }

  // Nettoyer CountUp
  if (countUp) {
    countUp.reset();
    countUp = null;
  }
});

/**
 * Watcher pour les changements de valeur cible
 */
watch(() => props.target, (newTarget, oldTarget) => {
  // Ne rien faire si c'est la même valeur
  if (newTarget === oldTarget) return;

  // Si CountUp existe, mettre à jour
  if (countUp && counterElement.value) {
    countUp.update(newTarget);
  } else if (!hasAnimated.value) {
    // Si pas encore animé, redémarrer avec la nouvelle valeur
    startCounting();
  }
});

/**
 * Watcher pour les changements de startValue
 */
watch(() => props.startValue, (newStartValue) => {
  displayValue.value = props.prefix + newStartValue + props.suffix;
});

// Exposer les méthodes publiques pour le parent
defineExpose({
  restart,
  pause,
  updateValue,
  isAnimating,
  hasAnimated
});
</script>

<style scoped>
.counter-wrapper {
  display: inline-block;
}

.counter-value {
  /* Alignement numérique tabulaire pour éviter les sauts */
  font-variant-numeric: tabular-nums;
  display: inline-block;
  /* Largeur minimale pour éviter le décalage pendant l'animation */
  min-width: 2ch;
  /* Transition douce si la valeur change instantanément */
  transition: opacity 0.3s ease;
}

/* Animation de chargement optionnelle */
.counter-wrapper.loading .counter-value {
  opacity: 0.5;
}
</style>