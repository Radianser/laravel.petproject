<script setup>
    defineProps(['user']);
</script>

<template>
    <a v-if="user != null" :href="'/profile/' + user.id" class="mr-4 flex items-center">
        <img v-if="user.avatar != null" class="w-14 object-cover rounded-full aspect-square bg-white dark:bg-dark-primary select-none" :src="'/storage/avatars/' + user.avatar" loading="lazy" alt="">
        <img v-else class="w-14 object-cover rounded-full aspect-square bg-white dark:bg-dark-primary select-none" src="/storage/icons/default.webp" loading="lazy" alt="">
    </a>
    <div v-else class="mr-4 flex items-center">
        <img class="w-14 object-cover rounded-full aspect-square bg-white dark:bg-dark-primary select-none" src="/storage/icons/deleted.webp" loading="lazy" alt="">
    </div>
</template>