<script setup>
defineProps({
    statut: { type: String, required: true },
    label: { type: String, required: true },
});

const styles = {
    a_jour: 'bg-green-100 text-green-800 border border-green-200',
    approche: 'bg-yellow-100 text-yellow-800 border border-yellow-200',
    due: 'bg-orange-100 text-orange-800 border border-orange-200',
    retard: 'bg-red-100 text-red-800 border border-red-200',
};

const emojis = {
    a_jour: '✅',
    approche: '⚡',
    due: '⚠️',
    retard: '❌',
};
</script>

<template>
    <span :class="['inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full', styles[statut] || styles.a_jour]">
        {{ emojis[statut] || '' }}&nbsp;{{ label }}
    </span>
</template>
