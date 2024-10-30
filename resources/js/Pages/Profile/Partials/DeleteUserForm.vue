<script setup>
    import DangerButton from '@/Components/DangerButton.vue';
    import InputError from '@/Components/InputError.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import Modal from '@/Components/Modal.vue';
    import SecondaryButton from '@/Components/SecondaryButton.vue';
    import TextInput from '@/Components/TextInput.vue';
    import { useForm } from '@inertiajs/vue3';
    import { nextTick, ref } from 'vue';

    defineProps({
        localization: {
            type: Object,
        },
        session: {
            type: Object,
        },
    });
    const confirmingUserDeletion = ref(false);
    const passwordInput = ref(null);

    const form = useForm({
        password: '',
    });

    const confirmUserDeletion = () => {
        confirmingUserDeletion.value = true;

        nextTick(() => passwordInput.value.focus());
    };

    const deleteUser = () => {
        form.delete(route('profile.destroy'), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
            onError: () => passwordInput.value.focus(),
            onFinish: () => form.reset(),
        });
    };

    const closeModal = () => {
        confirmingUserDeletion.value = false;

        form.reset();
    };
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium">{{ localization[session.language].delete_account }}</h2>

            <p class="mt-1 text-sm">
                {{ localization[session.language].delete_account_text }}
            </p>
        </header>
        <DangerButton @click="confirmUserDeletion">{{ localization[session.language].delete_account_button }}</DangerButton>
        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-4 sm:p-6 rounded-lg" :class="{ 'text-light bg-dark-primary border border-dark shadow-none': $page.props.auth.user.theme, 'text-darker bg-light-primary shadow': !$page.props.auth.user.theme }">
                <h2 class="text-lg font-medium">
                    {{ localization[session.language].delete_account_confirmation }}
                </h2>
                <p class="mt-1 text-sm">
                    {{ localization[session.language].delete_account_confirmation_text }}
                </p>
                <div class="mt-6">
                    <InputLabel for="password" value="Password" class="sr-only" />
                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-full"
                        :placeholder="localization[session.language].password"
                        :theme="$page.props.auth.user.theme"
                        @keyup.enter="deleteUser"
                    />
                    <InputError :message="form.errors.password" class="mt-2" />
                </div>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal"> {{ localization[session.language].cancel }} </SecondaryButton>
                    <DangerButton
                        class="ml-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        {{ localization[session.language].delete_account_button }}
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>