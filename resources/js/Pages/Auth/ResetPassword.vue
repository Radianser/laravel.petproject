<script setup>
    import GuestLayout from '@/Layouts/GuestLayout.vue';
    import InputError from '@/Components/InputError.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import TextInput from '@/Components/TextInput.vue';
    import { Head, useForm } from '@inertiajs/vue3';

    const props = defineProps({
        email: {
            type: String,
            required: true,
        },
        token: {
            type: String,
            required: true,
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
        token: props.token,
        email: props.email,
        password: '',
        password_confirmation: '',
    });

    const submit = () => {
        form.post(route('password.store'), {
            onFinish: () => form.reset('password', 'password_confirmation'),
        });
    };
</script>

<template>
    <GuestLayout :localization="localization" :session="session">
        <Head :title="localization[session.language].reset_password_title" />
        <div class="flex justify-center items-center p-6">
            <div class="w-full max-w-xl mx-auto p-6 rounded-xl text-darker dark:text-light bg-light-primary dark:bg-dark-primary shadow dark:shadow-none dark:border dark:border-dark">
                <form @submit.prevent="submit">
                    <div>
                        <InputLabel for="email" :value="localization[session.language].email" />
                        <TextInput
                            id="email"
                            type="email"
                            class="mt-2 block w-full"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                            :placeholder="localization[session.language].email_placeholder"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>
                    <div class="mt-4">
                        <InputLabel for="password" :value="localization[session.language].password" />
                        <TextInput
                            id="password"
                            type="password"
                            class="mt-2 block w-full"
                            v-model="form.password"
                            required
                            autocomplete="new-password"
                            :placeholder="localization[session.language].create_password_placeholder"
                        />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>
                    <div class="mt-4">
                        <InputLabel for="password_confirmation" :value="localization[session.language].confirm_password" />
                        <TextInput
                            id="password_confirmation"
                            type="password"
                            class="mt-2 block w-full"
                            v-model="form.password_confirmation"
                            required
                            autocomplete="new-password"
                            :placeholder="localization[session.language].confirm_password_placeholder"
                        />
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
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