<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import InputError from '@/Components/InputError.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import TextInput from '@/Components/TextInput.vue';
    import { Head, useForm } from '@inertiajs/vue3';

    const props = defineProps({
        localization: {
            type: Object,
        },
        session: {
            type: Object,
            required: true,
        },
    });

    const form = useForm({
        password: '',
    });

    const submit = () => {
        form.post(route('password.confirm'), {
            onFinish: () => form.reset(),
        });
    };
</script>

<template>
    <AuthenticatedLayout :localization="localization" :session="session">
        <Head :title="localization[session.language].confirm_password_title" />
        <div class="flex justify-center items-center p-6">
            <div class="w-full max-w-xl mx-auto p-6 rounded-xl text-darker dark:text-light bg-light-primary dark:bg-dark-primary shadow dark:shadow-none dark:border dark:border-dark">
                <div class="mb-4 text-sm">
                    {{ localization[session.language].confirm_password_text }}
                </div>
                <form @submit.prevent="submit">
                    <div>
                        <InputLabel for="password" :value="localization[session.language].password" />
                        <TextInput
                            id="password"
                            type="password"
                            class="mt-2 block w-full"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                            autofocus
                            :placeholder="localization[session.language].confirm_password_placeholder"
                        />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>
                    <div class="flex justify-end mt-4">
                        <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            {{ localization[session.language].confirm_password }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>