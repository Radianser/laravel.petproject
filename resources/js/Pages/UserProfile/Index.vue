<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import GuestLayout from '@/Layouts/GuestLayout.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import InputError from '@/Components/InputError.vue';
    import Babble from '@/Components/Babble.vue';
    import ProfileInfo from '@/Components/ProfileInfo.vue';
    import BabbleStoreForm from '@/Components/BabbleStoreForm.vue';
    import { Head, useForm } from '@inertiajs/vue3';
    import ViewBox from '@/Components/ViewBox.vue';
    import Clip from '@/Components/Clip.vue';
    import { usePreviewStore } from '@/Stores/currentPreview.js';
    import library from '@/myLibraryObject.js';
    
    let props = defineProps(['babbles', 'user', 'images', 'subscription', 'localization', 'session']);
    let uri = window.location.pathname;
    const store = usePreviewStore();
    const form = useForm({
        user: props.user,
        images: props.images,
        add_files: {},
        automatic_upload: true
    });

    ;(() => {
        window.addEventListener('scroll', library.throttle(checkPosition, 1000));
        window.addEventListener('resize', library.throttle(checkPosition, 1000));
    })();

    function checkPosition() {
        if(!props.babbles.stop) {
            let pixelsToBottom = Math.round(document.documentElement.offsetHeight - document.documentElement.scrollTop - window.innerHeight);
            if (pixelsToBottom < 1500) {
                axios.post(route('next.page'), props.babbles)
                .then(result => {
                    if(result.data.stop) {
                        props.babbles.stop = result.data.stop;
                    }
                    if(result.data.data) {
                        for(let element of result.data.data) {
                            props.babbles.data.push(element);
                        }
                    }
                });
            }
        }
    }
</script>

<style>
    .list-enter-active,
    .list-leave-active {
        transition: all 0.2s ease;
    }
    .list-enter-from,
    .list-leave-to {
        opacity: 0;
        transform: translateX(30px);
    }

    .viewbox-enter-active,
    .viewbox-leave-active {
        transition: all 0.2s ease;
    }
    .viewbox-enter-from,
    .viewbox-leave-to {
        opacity: 0;
        transform: translateY(15px);
    }
</style>
 
<template>
    <Head :title="user.name" />
    <AuthenticatedLayout v-if="$page.props.auth.user" :localization="localization" :session="session">

        <div v-if="store.show === true"
            class="form fixed left-0 top-0 w-screen h-screen bg-black opacity-30 z-40">               
        </div>
        <Transition name="viewbox">
            <ViewBox v-if="store.show === true" :localization="localization" :session="session" />
        </Transition>

        <div class="w-full p-4">
            <ProfileInfo :user="user" :subscription="subscription" :localization="localization" :session="session" />

            <div v-if="$page.props.auth.user" class="gallery mt-4">
                <div v-if="images.length > 0" class="max-w-2lg md:max-w-5xl mx-auto p-4 shadow dark:shadow-none dark:border dark:border-dark overflow-hidden bg-light-primary dark:bg-dark-primary rounded-xl">

                    <div class="w-full min-h-10 grid grid-cols-3 sm:grid-cols-6 gap-2 lg:gap-4 overflow-hidden rounded-lg">
                        <picture v-for="(image, index) in form.images" :key="image" class="w-full text-darker dark:text-light">
                            <div v-if="image.stub" class="w-full flex flex-col justify-center items-center aspect-square rounded-lg cursor-pointer object-cover bg-light dark:bg-dark shadow dark:shadow-none dark:border dark:border-dark">
                                <span class="loader"></span>
                                <span class="percentage mt-1 text-base font-semibold">{{ image.percentage }}</span>
                            </div>
                            <img v-else @click="library.showViewBox(store, form.images, index, user)" :src="image.src" alt="" class="w-full aspect-square rounded-lg cursor-pointer object-cover bg-light dark:bg-dark shadow-sm dark:shadow-none dark:border dark:border-dark" loading="lazy">
                        </picture>
                    </div>

                    <hr class="my-4 border-light dark:border-dark">
                    <div class="h-8 flex gap-4" :class="{ 'justify-between': images.length > 0 || $page.props.auth.user.id === user.id, 'justify-center': images.length == 0}">
                        <PrimaryButton v-if="images.length > 0 || $page.props.auth.user.id === user.id">
                            <a :href="'/gallery/' + user.id" class="">
                                {{ localization[session.language].browse }}
                            </a>
                        </PrimaryButton>
                        <div class="flex justify-between items-center text-darker dark:text-light font-semibold text-xs uppercase tracking-widest">
                            {{ localization[session.language].user_gallery }}
                        </div>
                        <PrimaryButton v-if="$page.props.auth.user.id === user.id">
                                <Clip
                                    :form="form"
                                    :visible="false"
                                >
                                <template #sign>
                                    {{ localization[session.language].add_image }}
                                </template>
                            </Clip>
                        </PrimaryButton>
                        <div v-else-if="images.length > 0" class="w-32 h-full"></div>
                    </div>
                    <InputError :message="form.errors.message" class="mt-4" />
                </div>
            </div>

            <div class="max-w-2xl mx-auto mt-4 sm:px-6 lg:px-8">
                <div v-if="uri == '/home'" class="p-4 sm:p-6 mb-4 text-darker dark:text-light bg-light-primary dark:bg-dark-primary shadow dark:shadow-none dark:border dark:border-dark rounded-lg">
                    <BabbleStoreForm
                        :babbles="babbles.data"
                        :parentObject="null"
                        :toggleScroll="false"
                        :localization="localization"
                        :session="session"
                    />
                </div>
                <div class="max-w-2xl mx-auto">
                    <div class="">
                        <TransitionGroup name="list" tag="div">
                            <div v-for="babble in babbles.data" :key="babble.id">
                                <Babble
                                    :babble="babble"
                                    :babbles="babbles.data"
                                    :localization="localization"
                                    :session="session"
                                />
                            </div>
                        </TransitionGroup>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

    <GuestLayout v-else :localization="localization" :session="session">

        <div v-if="store.show === true"
            class="form fixed left-0 top-0 w-screen h-screen bg-black opacity-30 z-40">               
        </div>
        <Transition name="viewbox">
            <ViewBox v-if="store.show === true" :localization="localization" :session="session" />
        </Transition>

        <div class="w-full p-4">
            <ProfileInfo :user="user" :subscription="subscription" :localization="localization" :session="session" />
            <div class="max-w-2xl mx-auto mt-4 sm:px-6 lg:px-8">
                <div class="max-w-2xl mx-auto">
                    <div class="">
                        <TransitionGroup name="list" tag="div">
                            <div v-for="babble in babbles.data" :key="babble.id">
                                <Babble
                                    :babble="babble"
                                    :babbles="babbles.data"
                                    :localization="localization"
                                    :session="session"
                                />
                            </div>
                        </TransitionGroup>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>