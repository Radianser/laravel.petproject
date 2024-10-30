<script setup>
    import AuthorImage from '@/Components/AuthorImage.vue';
    defineProps(['object', 'localization', 'session']);
</script>
 
<template>
    <div class="mt-6 flex space-x-2">
        <AuthorImage 
            :user="object.user"
        />
        <div class="flex-1">
            <div class="flex justify-between items-center">
                <div>
                    <a v-if="object.user != null" :href="'/profile/' + object.user.id">
                        <span class="font-bold">{{ object.user.name }}</span>
                    </a>
                    <p v-else class="font-bold">
                        {{ localization[session.language].deleted }}
                    </p>
                </div>
            </div>
            <div class="mt-4 text-base pr-10 flex justify-center items-center italic">
                {{ localization[session.language].deleted_message }}
            </div>
        </div>
    </div>
</template>