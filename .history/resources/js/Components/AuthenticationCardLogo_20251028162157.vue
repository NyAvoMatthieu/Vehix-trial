<!--<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <Link :href="'/'">
        <svg
            class="size-16"
            viewBox="0 0 48 48"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
        >
            <path d="M11.395 44.428C4.557 40.198 0 32.632 0 24 0 10.745 10.745 0 24 0a23.891 23.891 0 0113.997 4.502c-.2 17.907-11.097 33.245-26.602 39.926z" fill="#6875F5" />
            <path d="M14.134 45.885A23.914 23.914 0 0024 48c13.255 0 24-10.745 24-24 0-3.516-.756-6.856-2.115-9.866-4.659 15.143-16.608 27.092-31.75 31.751z" fill="#6875F5" />
        </svg>
    </Link>
</template>-->

<script setup>
import { Link } from '@inertiajs/vue3';

const baseUrl = window.location.origin;
</script>

<template>
    <Link :href="'/'">
        <div class="flex items-center space-x-12">
            <img
                :src="`${baseUrl}/gestion_auto/public/icons/hasnreziga.png`"
                alt="hasnreziga logo"
                class="size-16"
            />
            <img
                :src="`${baseUrl}/gestion_auto/public/icons/logo_vehix_final.ico`"
                alt="vehix logo"
                class="size-16 ml-12"
                ml-12
            />
        </div>
    </Link>
</template>