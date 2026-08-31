<!--<template>
    <img
        src="/icons/hasnreziga.png"
        alt="hasnreziga logo"
    />
</template>-->
<template>
    <img
        :src="`${baseUrl}/icons/hasnreziga.png`"
        alt="hasnreziga logo"
    />
</template>

<script setup>
const baseUrl = window.location.origin + '/gestion_auto/public';
</script>
