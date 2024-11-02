<script setup>
    import GuestLayout from '@/Layouts/GuestLayout.vue';
    import LoginForm from '@/Components/LoginForm.vue';
    import { Head } from '@inertiajs/vue3';

    let props = defineProps({
        canResetPassword: {
            type: Boolean,
        },
        status: {
            type: String,
        },
        localization: {
            type: Object,
        },
        session: {
            type: Object,
            required: true,
        },
    });
</script>

<template>
    <GuestLayout :localization="localization" :session="session">
        <Head :title="localization[session.language].log_in" />
        <div class="grid items-center sm:flex sm:justify-center sm:items-center px-6 min-h-[calc(100vh-64px)]">
            <div class="w-full max-w-xl p-6 mb-16 rounded-xl text-darker dark:text-light bg-light-primary dark:bg-dark-primary shadow dark:shadow-none dark:border dark:border-dark">
                <LoginForm 
                    :canResetPassword="canResetPassword"
                    :status="status"
                    :localization="localization"
                    :session="session"
                />
            </div>
        </div>
    </GuestLayout>
</template>