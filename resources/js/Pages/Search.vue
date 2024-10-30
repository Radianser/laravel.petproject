<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import ProfileLink from '@/Components/ProfileLink.vue'
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import InputError from '@/Components/InputError.vue';
    import { Head, useForm } from '@inertiajs/vue3';
    import { reactive } from 'vue';
    import library from '@/myLibraryObject.js';

    let props = defineProps(['title', 'localization', 'session']);
    const form = useForm({
        name: '',
    });
    const profiles = reactive({
        data: [],
        stop: null,
        empty: false
    });
    
    ;(() => {
        window.addEventListener('scroll', library.throttle(checkPosition, 1000));
        window.addEventListener('resize', library.throttle(checkPosition, 1000));
    })();
    function checkPosition() {
        if(profiles.data.length > 0) {
            if(!profiles.stop) {
                let pixelsToBottom = Math.round(document.documentElement.offsetHeight - document.documentElement.scrollTop - window.innerHeight);
                if (pixelsToBottom < 1500) {
                    axios.post(route('search'), {next: true})
                    .then(result => {
                        if(result.data[1]) {
                            profiles.stop = result.data[1];
                        }
                        if(result.data[0]) {
                            for(let element of result.data[0]) {
                                profiles.data.push(element);
                            }
                        }
                    });
                }
            }
        }
    }
    function search() {
        let data = {
            name: form.name
        };

        axios.post(route('search'), data)
        .then(result => {
            if(result.data.stop) {
                profiles.stop = result.data.stop;
            }
            if(result.data.data.length > 0) {
                profiles.empty = false;
                profiles.data = [];

                for(let item of result.data.data) {
                    profiles.data.push(item);
                }
            } else {
                profiles.data = [];
                profiles.empty = true;
            }

            form.clearErrors();
        }).catch(error => {
            if (error.response.status === 422) {
                form.errors.message = error.response.data.message;
                library.clearErrors(form);
            }
        });
    }
</script>

<style>
    /*.list-move,  apply transition to moving elements */
    .list-enter-active,
    .list-leave-active {
        transition: all 0.5s ease;
    }
    .list-enter-from,
    .list-leave-to {
        opacity: 0;
        transform: translateX(30px);
    }
    /* ensure leaving items are taken out of layout flow so that moving
    animations can be calculated correctly. */
    .list-leave-active {
        position: absolute;
    }
</style>

<template>
    <Head :title="localization[session.language].search" />
    <AuthenticatedLayout :localization="localization" :session="session">
        <div class="w-full p-4 text-darker dark:text-light">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="p-6 mb-4 bg-light-primary dark:bg-dark-primary dark:border dark:border-dark shadow dark:shadow-none rounded-lg">
                    <form @submit.prevent="search()" class="flex">
                        <input
                            type="search"
                            v-model="form.name"
                            :placeholder="localization[session.language].search_placeholder"
                            class="w-full h-10 block p-2 box-border border-light bg-light-primary dark:bg-dark-input focus:border-additional focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm dark:shadow-none dark:border dark:border-dark"
                        >
                        <PrimaryButton class="px-2 sm:px-4 ml-3">
                            <div class="w-5 h-5 block sm:hidden w-full aspect-square bg-magnifier bg-contain bg-no-repeat bg-center"></div>
                            <div class="hidden sm:block">{{ localization[session.language].search_button }}</div>
                        </PrimaryButton>
                    </form>
                    <InputError :message="form.errors.message" class="mt-5" />
                </div>
                <TransitionGroup name="list" tag="div">
                    <div v-for="profile in profiles.data" :key="profile.id">
                        <ProfileLink
                            :profile = profile
                            :profiles="profiles.data"
                            :title="title"
                            :localization="localization"
                            :session="session"
                        />
                    </div>
                    <div v-if="profiles.empty" class="text-center">{{ localization[session.language].no_matches }}</div>
                </TransitionGroup>
            </div>
        </div>
    </AuthenticatedLayout>
</template>