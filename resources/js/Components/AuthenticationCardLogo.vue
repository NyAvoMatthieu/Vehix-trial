<script setup>
import { Link } from '@inertiajs/vue3'
import logoImage from '@/assets/logo_vehix_final.png'
</script>

<template>
    <Link :href="'/'">
        <div class="flex justify-center items-center">
            <img
                :src="logoImage"
                alt="vehix logo"
                class="h-16 sm:h-20 md:h-24 w-auto object-contain"
            />
        </div>
    </Link>
</template>