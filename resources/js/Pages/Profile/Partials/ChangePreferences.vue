<script setup>
    import InputError from '@/Components/InputError.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import { useForm, usePage } from '@inertiajs/vue3';
    import { computed } from 'vue';
    import library from '@/myLibraryObject.js';
    import { useSessionStore } from '@/Stores/sessionStore.js';

    const props = defineProps({
        localization: {
            type: Object,
        },
        session: {
            type: Object,
        },
    });
    const store = useSessionStore();

    store.theme = computed(() => {return props.session.theme});
    store.language = computed(() => {return props.session.language});
    store.sorting = computed(() => {return usePage().props.auth.user.sorting});

    const form = useForm({
        theme: computed(() => {return store.theme}),
        language: computed(() => {return store.language}),
        sorting: computed(() => {return store.sorting}),
    });

    function toggle(param, value) {
        setTimeout(() => {
            if(param == 'theme') {
                props.session.theme = Number(value);
            }
            
            if(param == 'language') {
                props.session.language = value;
            }

            if(param == 'sorting') {
                usePage().props.auth.user.sorting = Number(value);
            }
        }, 0);
    }
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium">{{ localization[session.language].change_preferences }}</h2>

            <p class="mt-1 text-sm">
                {{ localization[session.language].change_preferences_text }}
            </p>
        </header>
        <form @submit.prevent="form.put(route('profile.update_session'), {preserveScroll: true, onError: (error) => { form.errors.message = error.message; library.clearErrors(form);}});" class="mt-6 space-y-6">
            <div>
                <div class="grid grid-cols-3 gap-2 mb-2">
                    <InputLabel for="language" :value="localization[session.language].language" />
                    <InputLabel for="theme" :value="localization[session.language].theme" />
                    <InputLabel for="sorting" :value="localization[session.language].sorting" />
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <select @change="toggle('language', $event.target.value)" :value="store.language" name="language" id="language" class="border-gray-300 text-darker dark:text-light border-light bg-light-primary dark:bg-dark-input focus:border-additional focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm dark:shadow-none dark:border dark:border-dark mt-1 block w-full">
                        <option value="en">{{ localization[session.language].en }}</option>
                        <option value="ru">{{ localization[session.language].ru }}</option>
                    </select>
                    <select @change="toggle('theme', $event.target.value)" :value="store.theme" name="theme" id="theme" class="border-gray-300 text-darker dark:text-light border-light bg-light-primary dark:bg-dark-input focus:border-additional focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm dark:shadow-none dark:border dark:border-dark mt-1 block w-full">
                        <option :value="0">{{ localization[session.language].light }}</option>
                        <option :value="1">{{ localization[session.language].dark }}</option>
                    </select>
                    <select @change="toggle('sorting', $event.target.value)" :value="store.sorting" name="sorting" id="sorting" class="border-gray-300 text-darker dark:text-light border-light bg-light-primary dark:bg-dark-input focus:border-additional focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm dark:shadow-none dark:border dark:border-dark mt-1 block w-full">
                        <option :value="0">{{ localization[session.language].oldest }}</option>
                        <option :value="1">{{ localization[session.language].newest }}</option>
                        <option :value="2">{{ localization[session.language].interesting }}</option>
                    </select>
                </div>
            </div>
            <InputError :message="form.errors.message" class="mt-2" />
            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">{{ localization[session.language].save }}</PrimaryButton>
                <Transition enter-from-class="opacity-0" leave-to-class="opacity-0" class="transition ease-in-out">
                    <p v-if="form.recentlySuccessful" class="text-sm">{{ localization[session.language].saved }}.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>