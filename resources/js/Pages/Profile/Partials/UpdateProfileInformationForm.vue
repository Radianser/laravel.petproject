<script setup>
    import InputError from '@/Components/InputError.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import TextInput from '@/Components/TextInput.vue';
    import { Link, useForm, usePage } from '@inertiajs/vue3';

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

    const user = usePage().props.auth.user;

    const form = useForm({
        name: user.name,
        email: user.email,
        language: props.session.language,
    });

    const submit = () => {
        form.errors = {};
        form.language = props.session.language;
        form.patch(route('profile.update'));
    };
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium">{{ localization[session.language].profile_information }}</h2>

            <p class="mt-1 text-sm">
                {{ localization[session.language].profile_information_text }}
            </p>
        </header>
        <form @submit.prevent="submit" class="mt-6 space-y-6">
            <div>
                <InputLabel for="name" :value="localization[session.language].name" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autocomplete="name"
                    :placeholder="localization[session.language].name_placeholder"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>
            <div>
                <InputLabel for="email" :value="localization[session.language].email" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                    :placeholder="localization[session.language].email_placeholder"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>
            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="text-sm mt-2">
                    {{ localization[session.language].unverified_email }}
                    <br>
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        class="underline text-sm hover:text-additional"
                    >
                        {{ localization[session.language].send_verification_email }}
                    </Link>
                </p>
                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 font-medium text-sm text-green-600"
                >
                    {{ localization[session.language].verification_link }}
                </div>
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