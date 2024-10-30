<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: false,
    },
    active: {
        type: Boolean,
        required: false,
    },
});

// const classes = computed(() =>
//     props.active
//         ? 'block w-full pl-3 pr-4 py-2 border-l-4 border-indigo-400 text-left text-base font-medium text-indigo-700 bg-indigo-50 focus:outline-none focus:text-indigo-800 focus:bg-indigo-100 focus:border-indigo-700 transition duration-150 ease-in-out'
//         : 'block w-full pl-3 pr-4 py-2 border-l-4 border-transparent text-left text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out'
// );

const classes = computed(() =>
    props.active
        ? 'block w-full pl-3 pr-4 py-2 border-l-4 border-additional text-left text-base font-medium text-additional bg-additional-light dark:bg-dark-hover transition duration-150 ease-in-out'
        : 'block w-full pl-3 pr-4 py-2 border-l-4 border-transparent text-left text-base font-medium text-dark dark:text-light hover:text-darker dark:hover:text-lighter hover:bg-light dark:hover:bg-dark-hover hover:border-light focus:outline-none focus:text-darker focus:bg-light dark:focus:bg-dark-hover focus:border-light transition duration-150 ease-in-out'
);
</script>

<template>
    <Link v-if="href" :href="href" :class="classes">
        <slot />
    </Link>
    <div v-else
        class="cursor-pointer select-none"
        :class="classes"
    >
        <slot />
    </div>
</template>