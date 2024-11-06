<script setup>
    import GuestLayout from '@/Layouts/GuestLayout.vue';
    import InputError from '@/Components/InputError.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import TextInput from '@/Components/TextInput.vue';
    import { Head, useForm } from '@inertiajs/vue3';

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

    const form = useForm({
        email: '',
        language: props.session.language,
    });

    const submit = () => {
        form.errors = {};
        form.language = props.session.language;
        form.post(route('password.email'));
    };
</script>

<template>
    <GuestLayout :localization="localization" :session="session">
        <Head :title="localization[session.language].forgot_password_title" />
        <div class="flex justify-center items-center p-6">
            <div class="w-full max-w-xl mx-auto p-6 rounded-xl text-darker dark:text-light bg-light-primary dark:bg-dark-primary shadow dark:shadow-none dark:border dark:border-dark">
                <div class="mb-4 text-sm">
                    {{ localization[session.language].forgot_password_text }}
                </div>
                <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                    {{ status }}
                </div>
                <form @submit.prevent="submit">
                    <div>
                        <InputLabel for="email" :value="localization[session.language].email" />
                        <TextInput
                            id="email"
                            type="email"
                            class="mt-2 block w-full"
                            v-model="form.email"
                            required
                            autocomplete="username"
                            :placeholder="localization[session.language].email_placeholder"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>
                    <div class="flex items-center justify-end mt-4">
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            {{ localization[session.language].password_reset_button }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </GuestLayout>
</template>