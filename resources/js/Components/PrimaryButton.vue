<script setup>
    function touchStartButtonState(e) {
        e.target.classList.add('hover:bg-dark-hover');
    }

    function touchEndButtonState(e) {
        e.target.classList.remove('hover:bg-dark-hover');
    }
</script>

<template>
    <!-- <button
        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
    >
        <slot />
    </button> -->

    <!-- <button
        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-700 active:bg-gray-900 focus:outline-none active:ring-2 active:ring-indigo-500 active:ring-offset-2 transition ease-in-out duration-150"
    >
        <slot />
    </button> -->

    <!-- <button
        class="inline-flex justify-center items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 transition ease-in-out duration-150"
    >
        <slot />
    </button> -->

    <button
        @touchstart="touchStartButtonState($event)"
        @touchend="touchEndButtonState($event)"
        class="inline-flex justify-center items-center px-3 py-2 border border-transparent rounded-md font-semibold text-xs text-lighter uppercase tracking-wider select-none bg-dark-primary dark:bg-dark sm:hover:bg-dark-hover active:bg-dark-hover transition ease-in-out duration-150"
    >
        <slot />
    </button>
</template>