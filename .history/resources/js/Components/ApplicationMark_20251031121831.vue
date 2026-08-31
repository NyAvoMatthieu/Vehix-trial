<script setup>
    import { Link } from '@inertiajs/vue3'
</script>

<template>
    <Link :href="'/'">
        <div
            class="flex justify-center"
        >
            <img
                src="icons/logo_vehix_final.ico"
                alt="vehix logo"
                class="w-16 h-16 sm:w-20 sm:h-10 md:w-10 md:h-10"
            />
        </div>
    </Link>
</template>
