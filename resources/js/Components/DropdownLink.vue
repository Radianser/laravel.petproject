<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: {
        type: String,
        required: false,
    },
});
</script>

<template>
    <!-- <Link
        :href="href"
        class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
    >
        <slot />
    </Link> -->
    <Link v-if="href"
        :href="href"
        class="block w-full px-4 py-2 text-left text-sm leading-5 text-darker dark:text-light hover:bg-light dark:hover:bg-dark-hover focus:bg-light dark:focus:bg-dark-hover focus:outline-none transition duration-150 ease-in-out"
    >
        <slot />
    </Link>
    <div v-else
        class="cursor-pointer select-none block w-full px-4 py-2 text-left text-sm leading-5 text-darker dark:text-light hover:bg-light dark:hover:bg-dark-hover focus:bg-light dark:focus:bg-dark-hover focus:outline-none transition duration-150 ease-in-out"
    >
        <slot />
    </div>
</template>
