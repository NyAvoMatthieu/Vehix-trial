<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
    <Link :href="'/'">
        <div
            class="flex justify-center items-center <!--gap-[60px] sm:gap-[100px] md:gap-[150px] flex-wrap-->
        >
            <!--<img
                src="icons/hasnreziga.png"
                alt="hasnreziga logo"
                class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24"
            />-->
            <img
                src="icons/logo_vehix_final.ico"
                alt="vehix logo"
                class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24"
            />
        </div>
    </Link>
</template>
