<script setup>
    import InputError from '@/Components/InputError.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import TextInput from '@/Components/TextInput.vue';
    import { useForm } from '@inertiajs/vue3';
    import { ref } from 'vue';

    const props = defineProps({
        localization: {
            type: Object,
        },
        session: {
            type: Object,
        },
    });
    const passwordInput = ref(null);
    const currentPasswordInput = ref(null);

    const form = useForm({
        current_password: '',
        password: '',
        password_confirmation: '',
        language: props.session.language,
    });

    const updatePassword = () => {
        form.errors = {};
        form.language = props.session.language;
        form.put(route('password.update'), {
            preserveScroll: true,
            onSuccess: () => form.reset(),
            onError: () => {
                if (form.errors.password) {
                    form.reset('password', 'password_confirmation');
                    passwordInput.value.focus();
                }
                if (form.errors.current_password) {
                    form.reset('current_password');
                    currentPasswordInput.value.focus();
                }
            },
        });
    };
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium">{{ localization[session.language].update_password }}</h2>
            <p class="mt-1 text-sm">
                {{ localization[session.language].update_password_text }}
            </p>
        </header>
        <form @submit.prevent="updatePassword" class="mt-6 space-y-6">
            <div>
                <InputLabel for="current_password" :value="localization[session.language].current_password" />
                <TextInput
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    class="mt-1 block w-full"
                    :placeholder="localization[session.language].current_password_placeholder"
                />
                <InputError :message="form.errors.current_password" class="mt-2" />
            </div>
            <div>
                <InputLabel for="password" :value="localization[session.language].new_password" />
                <TextInput
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    :placeholder="localization[session.language].new_password_placeholder"
                />
                <InputError :message="form.errors.password" class="mt-2" />
            </div>
            <div>
                <InputLabel for="password_confirmation" :value="localization[session.language].confirm_password" />
                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    :placeholder="localization[session.language].confirm_password_placeholder"
                />
                <InputError :message="form.errors.password_confirmation" class="mt-2" />
            </div>
            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">{{ localization[session.language].save }}</PrimaryButton>
                <Transition enter-from-class="opacity-0" leave-to-class="opacity-0" class="transition ease-in-out">
                    <p v-if="form.recentlySuccessful" class="text-sm">{{ localization[session.language].saved }}.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>
