<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import GuestLayout from '@/Layouts/GuestLayout.vue';
    import Babble from '@/Components/Babble.vue';
    import ViewBox from '@/Components/ViewBox.vue';
    import { Head } from '@inertiajs/vue3';
    import { usePreviewStore } from '@/Stores/currentPreview.js';
    import library from '@/myLibraryObject.js';

    let props = defineProps(['babbles', 'title', 'localization', 'session']);
    const store = usePreviewStore();

    ;(() => {
        window.addEventListener('scroll', library.throttle(checkPosition, 1000));
        window.addEventListener('resize', library.throttle(checkPosition, 1000));
    })();

    function checkPosition() {
        if(props.babbles.data.length >= 10) {
            if(!props.babbles.stop) {
                let pixelsToBottom = Math.round(document.documentElement.offsetHeight - document.documentElement.scrollTop - window.innerHeight);
                if (pixelsToBottom < 1500) {
                    axios.post(route('feed.show'))
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
    }
</script>

<style>
    .list-enter-active,
    .list-leave-active {
        transition: all 0.5s ease;
    }
    .list-enter-from,
    .list-leave-to {
        opacity: 0;
        transform: translateX(30px);
    }
</style>

<template>
    <Head :title="localization[session.language][title]" />
    <AuthenticatedLayout v-if="$page.props.auth.user" :localization="localization" :session="session">
        <ViewBox v-if="store.show === true" :localization="localization" :session="session" />
        <div class="w-full p-4">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <Transition name="list">
                    <div v-if="babbles.data.length != 0" class="">
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
                    <div v-else class="p-10 bg-white shadow rounded-lg">
                        <span class="text-gray-800">{{ localization[session.language].no_posts }}</span>
                    </div>
                </Transition>
            </div>
        </div>
    </AuthenticatedLayout>
    <GuestLayout v-else :localization="localization" :session="session">
        <ViewBox v-if="store.show === true" :localization="localization" :session="session" />
        <div class="w-full p-4">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <Transition name="list">
                    <div v-if="babbles.data.length != 0" class="">
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
                    <div v-else class="p-10 bg-white shadow rounded-lg">
                        <span class="text-gray-800">{{ localization[session.language].no_posts }}</span>
                    </div>
                </Transition>
            </div>
        </div>
    </GuestLayout>
</template>