<script setup>
import { Link } from '@inertiajs/vue3'
import logoImage from '@/assets/logo_vehix_final.png'
</script>

<template>
    <Link :href="'/'">
        <div class="flex items-center justify-center">
            <img
                :src="logoImage"
                alt="vehix logo"
                class="h-9 w-auto object-contain"
            />
        </div>
    </Link>
</template>