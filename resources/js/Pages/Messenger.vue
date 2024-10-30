<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head } from '@inertiajs/vue3';

    let props = defineProps(['title', 'localization', 'session']);
</script>

<template>
    <Head :title="localization[session.language].messenger" />
    <AuthenticatedLayout :localization="localization" :session="session">
        <div class="w-full h-[calc(100vh-64px)] px-4 -mt-8 flex justify-center items-center">
            <div class="px-10 py-6 text-darker dark:text-light bg-light-primary dark:bg-dark-primary dark:border dark:border-dark shadow dark:shadow-none rounded-lg">
                {{ localization[session.language].work_in_progress }}
            </div>
        </div>
    </AuthenticatedLayout>
</template>