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
        <div class="flex justify-center items-center p-6">
            <div class="w-full max-w-xl mx-auto p-6 rounded-xl text-darker dark:text-light bg-light-primary dark:bg-dark-primary shadow dark:shadow-none dark:border dark:border-dark">
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