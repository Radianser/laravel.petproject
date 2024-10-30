<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import DeleteUserForm from './Partials/DeleteUserForm.vue';
    import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
    import ChangePreferences from './Partials/ChangePreferences.vue';
    import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
    import { Head } from '@inertiajs/vue3';

    const props = defineProps({
        mustVerifyEmail: {
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
        },
    });
</script>

<template>
    <Head :title="localization[session.language].profile" />
    <AuthenticatedLayout :localization="localization" :session="session">
        <div class="w-full p-4">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-6 text-darker dark:text-light bg-light-primary dark:bg-dark-primary dark:border dark:border-dark shadow dark:shadow-none rounded-lg">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        :localization="localization"
                        :session="session"
                        class="max-w-xl"
                    />
                </div>
                <div class="p-4 sm:p-6 text-darker dark:text-light bg-light-primary dark:bg-dark-primary dark:border dark:border-dark shadow dark:shadow-none rounded-lg">
                    <ChangePreferences class="max-w-xl" :localization="localization" :session="session" />
                </div>
                <div class="p-4 sm:p-6 text-darker dark:text-light bg-light-primary dark:bg-dark-primary dark:border dark:border-dark shadow dark:shadow-none rounded-lg">
                    <UpdatePasswordForm class="max-w-xl" :localization="localization" :session="session" />
                </div>
                <div class="p-4 sm:p-6 text-darker dark:text-light bg-light-primary dark:bg-dark-primary dark:border dark:border-dark shadow dark:shadow-none rounded-lg">
                    <DeleteUserForm class="max-w-xl" :localization="localization" :session="session" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>