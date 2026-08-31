<!-- resources/js/Components/VehiculeStatusBadge.vue -->
<template>
  <span :class="badgeClass">
    <component
      :is="statusIcon"
      v-if="showIcon"
      :class="iconClass"
    />
    {{ statusLabel }}
  </span>
</template>

<script setup>
import { computed } from 'vue'
import {
  CheckCircleIcon,
  ClockIcon,
  XCircleIcon,
  ExclamationTriangleIcon,
  DocumentDuplicateIcon,
  QuestionMarkCircleIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  status: {
    type: [String, Object],
    required: true
  },
  showIcon: {
    type: Boolean,
    default: true
  },
  customColors: {
    type: Object,
    default: () => ({})
  },
  customLabels: {
    type: Object,
    default: () => ({})
  },
  size: {
    type: String,
    default: 'default', // 'sm', 'default', 'lg'
    validator: (value) => ['sm', 'default', 'lg'].includes(value)
  }
})

const statusValue = computed(() => {
  return typeof props.status === 'object' ? props.status.value : props.status
})

const statusLabel = computed(() => {
  const defaultLabels = {
    'valide': 'Validé',
    'en_attente': 'En attente',
    'refuse': 'Refusé',
    'a_corriger': 'À corriger',
    'doublon': 'Doublon'
  }
  return props.customLabels[statusValue.value] || defaultLabels[statusValue.value] || 'Inconnu'
})

const statusIcon = computed(() => {
  const iconMap = {
    'valide': CheckCircleIcon,
    'en_attente': ClockIcon,
    'refuse': XCircleIcon,
    'a_corriger': ExclamationTriangleIcon,
    'doublon': DocumentDuplicateIcon
  }
  return iconMap[statusValue.value] || QuestionMarkCircleIcon
})

const sizeClasses = computed(() => {
  switch (props.size) {
    case 'sm':
      return 'text-xs px-2 py-0.5'
    case 'lg':
      return 'text-sm px-3 py-1'
    default:
      return 'text-xs px-2.5 py-0.5'
  }
})

const badgeClass = computed(() => {
  const baseClass = `inline-flex items-center rounded-full font-medium ${sizeClasses.value}`
  const defaultColors = {
    'valide': 'bg-green-100 text-green-800',
    'en_attente': 'bg-yellow-100 text-yellow-800',
    'refuse': 'bg-red-100 text-red-800',
    'a_corriger': 'bg-orange-100 text-orange-800',
    'doublon': 'bg-purple-100 text-purple-800'
  }
  const colors = props.customColors[statusValue.value] || defaultColors[statusValue.value] || 'bg-gray-100 text-gray-800'
  return `${baseClass} ${colors}`
})

const iconClass = computed(() => {
  const baseIconClass = 'mr-1'
  switch (props.size) {
    case 'sm':
      return `${baseIconClass} h-3 w-3`
    case 'lg':
      return `${baseIconClass} h-5 w-5`
    default:
      return `${baseIconClass} h-4 w-4`
  }
})
</script>
