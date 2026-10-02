<script setup>
defineProps({
    // 'approaching' | 'expired' | 'a_jour' | 'approche' | 'due' | 'retard' | null
    statut: { type: String, default: null },
});

const isJaune = (s) => s === 'approaching' || s === 'approche';
const isRouge = (s) => s === 'expired' || s === 'due' || s === 'retard';
</script>

<template>
    <span v-if="isJaune(statut) || isRouge(statut)" class="relative inline-flex h-2.5 w-2.5">
        <span
            :class="['animate-ping absolute inline-flex h-full w-full rounded-full opacity-75',
                     isRouge(statut) ? 'bg-red-500' : 'bg-yellow-500']"
        />
        <span
            :class="['relative inline-flex rounded-full h-2.5 w-2.5',
                     isRouge(statut) ? 'bg-red-600' : 'bg-yellow-600']"
        />
    </span>
</template>