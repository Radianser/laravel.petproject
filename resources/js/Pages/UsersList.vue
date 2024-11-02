<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import ProfileLink from '@/Components/ProfileLink.vue'
    import { Head } from '@inertiajs/vue3';
    import library from '@/myLibraryObject.js';

    let props = defineProps(['profiles', 'title', 'localization', 'session']);
    
    ;(() => {
        window.addEventListener('scroll', library.throttle(checkPosition, 1000));
        window.addEventListener('resize', library.throttle(checkPosition, 1000));
    })();
    function checkPosition() {
        if(!props.profiles.stop) {
            let pixelsToBottom = Math.round(document.documentElement.offsetHeight - document.documentElement.scrollTop - window.innerHeight);
            if (pixelsToBottom < 1500) {
                axios.post(route('likes.show'))
                .then(result => {
                    if(result.data[1]) {
                        props.profiles.stop = result.data[1];
                    }
                    if(result.data[0]) {
                        for(let element of result.data[0]) {
                            props.profiles.data.push(element);
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
    <AuthenticatedLayout :localization="localization" :session="session">
        <div class="w-full p-4 text-darker dark:text-light">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <TransitionGroup name="list" tag="div">
                    <div v-for="profile in profiles.data" :key="profile">
                        <ProfileLink
                            :profile = profile
                            :profiles="profiles.data"
                            :title="title"
                            :localization="localization"
                            :session="session"
                        />
                    </div>
                    <div v-if="profiles.empty">{{ localization[session.language].no_users }}</div>
                </TransitionGroup>
            </div>
        </div>
    </AuthenticatedLayout>
</template>