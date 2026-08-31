<script setup>
import { Link } from '@inertiajs/vue3';

const baseUrl = window.location.origin;
</script>

<template>
    <Link :href="'/'">
        <div class="flex items-center space-x-2">
            <img
                :src="`${baseUrl}/gestion_auto/public/icons/hasnreziga.png`"
                alt="hasnreziga logo"
                class="size-16"
            />
            <img
                :src="`${baseUrl}/gestion_auto/public/icons/logo_vehix_final.ico`"
                alt="vehix logo"
                class="size-16"
            />
        </div>
    </Link>
</template>