<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: {
        type: String,
        required: true,
    },
    theme: {
        type: Number,
        required: false,
    },
});

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="dark:border-none text-darker bg-light-primary shadow-sm dark:shadow-none dark:text-light dark:bg-dark-input focus:border-additional focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md"
        :class="{ 'text-light bg-dark-input border-none': theme, 'text-darker bg-light-primary border-light': !theme }"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        ref="input"
    />
</template>
