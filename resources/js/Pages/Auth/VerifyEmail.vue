<script setup>
    import { computed } from 'vue';
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import { Head, Link, useForm } from '@inertiajs/vue3';

    const props = defineProps({
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

    const form = useForm({});

    const submit = () => {
        form.post(route('verification.send'));
    };

    const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <AuthenticatedLayout :localization="localization" :session="session">
        <Head :title="localization[session.language].email_verification_title" />
        <div class="flex justify-center items-center p-6">
            <div class="w-full max-w-xl mx-auto p-6 rounded-xl text-darker dark:text-light bg-light-primary dark:bg-dark-primary shadow dark:shadow-none dark:border dark:border-dark">
                <div class="mb-4 text-sm">
                    {{ localization[session.language].verification_text }}
                </div>
                <div class="mb-4 font-medium text-sm text-green-600" v-if="verificationLinkSent">
                    {{ localization[session.language].verification_status }}
                </div>
                <form @submit.prevent="submit">
                    <div class="mt-4 flex items-center justify-between">
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            {{ localization[session.language].verification_resend_button }}
                        </PrimaryButton>
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="underline text-sm hover:text-additional"
                        >{{ localization[session.language].logout }}</Link>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>