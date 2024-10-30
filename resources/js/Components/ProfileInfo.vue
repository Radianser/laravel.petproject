<script setup>
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import InputError from '@/Components/InputError.vue';
    import { useForm } from '@inertiajs/vue3';
    import dayjs from 'dayjs';
    import 'dayjs/locale/ru';
    import relativeTime from 'dayjs/plugin/relativeTime';
    import library from '@/myLibraryObject.js';

    dayjs.extend(relativeTime);

    let props = defineProps(['user', 'subscription', 'localization', 'session']);
    let uri = window.location.pathname;
    let timezone = +4;
    const form = useForm({
        avatar: null,
        cover: null
    });
    function toggleFormRender(id, formClass) {
        document.querySelector('html').classList.toggle('overflow-hidden');
        document.querySelector('.' + formClass).classList.toggle('hidden');
        document.querySelector('#' + id).value = null;
        form.avatar = null;
        form.cover = null;
    }
    function onChange(id, param) {
        if(param == 'image') {
            form.avatar = document.querySelector('#' + id).files[0];
            let fileName = form.avatar.name;
            let span = document.querySelector('#image');
            span.innerText = fileName;
        } else {
            form.cover = document.querySelector('#' + id).files[0];
            let fileName = form.cover.name;
            let span = document.querySelector('#cover');
            span.innerHTML = fileName;
        }
    }
    function clearLabel(param) {
        if(param == 'image') {
            let span = document.querySelector('#image');
            span.innerText = props.localization[props.session.language].choose_profile_image;
        } else {
            let span = document.querySelector('#cover');
            span.innerHTML = props.localization[props.session.language].choose_profile_cover;
        }
    }

    // console.log(props.user);
</script>

<template>
    <div class="">
        <div class="max-w-2lg md:max-w-5xl mx-auto shadow dark:shadow-none dark:border dark:border-dark overflow-hidden rounded-xl">

            <div class="cover relative bg-cover dark:bg-dark-primary bg h-60 group">
                <div v-if="uri == '/home'"
                    @click="toggleFormRender('profileCoverUpload', 'cover-form')"
                    class="absolute top-4 right-4 px-2 py-1.5 rounded-lg cursor-pointer select-none hover:bg-my-hover-bg hover:shadow-md active:bg-dark-primary dark:active:bg-dark opacity-0 group-hover:opacity-100 transition-all"
                >
                    <div class="text-lighter flex items-center">{{ localization[session.language].change_cover }}</div>
                </div>
                <img v-if="user.cover != null" class="w-full h-full object-cover select-none" :src="'/storage/covers/' + user.cover" alt="" loading="lazy">
            </div>

            <div class="profile-information bg-white dark:bg-dark-primary min-h-fit">

                <div class="h-24 sm:h-0 relative select-none">
                    <img v-if="uri == '/home'" @click="toggleFormRender('profileImageUpload', 'image-form')" class="absolute h-48 w-48 -top-24 left-0 sm:left-16 right-0 mx-auto sm:mx-0 object-cover rounded-full border-solid border-white dark:border-dark-primary border-4 cursor-pointer bg-white dark:bg-dark-primary" :src="user.avatar != null ? '/storage/avatars/' + user.avatar : '/storage/icons/default.jpg'" alt="" loading="lazy">
                    <img v-else class="absolute h-48 w-48 -top-24 left-0 sm:left-16 right-0 mx-auto sm:mx-0 object-cover rounded-full border-solid border-white dark:border-dark-primary border-4 bg-white dark:bg-dark-primary" :src="user.avatar != null ? '/storage/avatars/' + user.avatar : '/storage/icons/default.webp'" alt="" loading="lazy">
                </div>
                

                <!-- <div class="flex flex-col justify-between sm:flex-row"> -->
                <div class="grid p-4">

                    <div class="main-information flex flex-col items-center sm:items-start sm:pl-72 text-darker dark:text-light pb-4">
                        <h2 class="text-xl font-bold">{{ user.name }}</h2>
                        <p>{{ localization[session.language].registered + ':' }} {{ dayjs(user.created_at).format("DD.MM.YYYY") }}</p>
                        <p v-if="library.isOnline(user.last_action, timezone)">{{ localization[session.language].online }}</p>
                        <p v-else>{{ localization[session.language].last_seen + ':' }} {{ dayjs(user.last_action).add(timezone, 'hour').locale(session.language).fromNow() }}</p>
                    </div>

                    <div class="subscribers flex justify-between text-darker dark:text-light pb-4 px-4">
                        <div>
                            <a :href="'/home/following/' + user.id" class="hover:underline decoration-additional decoration-2">
                                {{ localization[session.language].following + ':' }} {{ user.following != null ? user.following.length : 0 }}
                            </a>
                        </div>
                        <div>
                            <a :href="'/home/followers/' + user.id" class="hover:underline decoration-additional decoration-2">
                                {{ localization[session.language].followers + ':' }} {{ user.followers != null ? user.followers.length : 0 }}
                            </a>
                        </div>
                    </div>

                    <div class="buttons">

                        <div v-if="uri != '/home' && $page.props.auth.user" class="flex justify-between select-none">
                            <PrimaryButton>
                                <a href="/messenger">
                                    {{ localization[session.language].send_message }}
                                </a>
                            </PrimaryButton>
                            <PrimaryButton v-if="subscription == false" @click="useForm({ id: user.id }).post(route('user.subscribe'))">
                                {{ localization[session.language].follow }}
                            </PrimaryButton>
                            <PrimaryButton v-else @click="useForm({ id: user.id }).post(route('user.subscribe'))">
                                {{ localization[session.language].unfollow }}
                            </PrimaryButton>
                        </div>

                        <div v-else-if="$page.props.auth.user" class="select-none">
                            <PrimaryButton>
                                <a href="/messenger">
                                    {{ localization[session.language].send_message }}
                                </a>
                            </PrimaryButton>
                        </div>

                        <div v-else></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="relative">
        <div class="image-form hidden">
            <div @click="toggleFormRender('profileImageUpload', 'image-form'); clearLabel('image'); form.reset(); form.clearErrors();" class="form fixed left-0 top-0 w-screen h-screen bg-black opacity-30 z-40"></div>
            <form 
                @submit.prevent="form.post(route('image.upload'), { onSuccess: () => { clearLabel('image'); toggleFormRender('profileImageUpload', 'image-form'); form.reset(); form.clearErrors(); }, onError: (error) => { form.reset(); library.clearErrors(form);} })"
                class="absolute left-0 right-0 top-0 bottom-0 px-10 pt-10 pb-8 m-auto w-fit h-fit bg-light-primary dark:bg-dark-primary rounded-lg shadow dark:shadow-none dark:border dark:border-dark flex flex-col justify-evenly items-center z-50"
                enctype="multipart/form-data">
                <input
                    @change="onChange('profileImageUpload', 'image')"
                    id="profileImageUpload"
                    type="file"
                    class="hidden"
                >
                <!-- <label class="inline-flex items-center px-4 py-2 bg-dark-primary dark:bg-dark border border-transparent cursor-pointer select-none rounded-md font-semibold text-xs text-lighter uppercase tracking-widest hover:bg-dark-hover 
                active:ring-2 ring-additional ring-offset-2 ring-offset-light dark:ring-offset-dark transition ease-in-out duration-150" for="profileImageUpload"> -->
                <label class="inline-flex items-center px-4 py-2 bg-dark-primary dark:bg-dark border border-transparent cursor-pointer select-none rounded-md font-semibold text-xs text-lighter uppercase tracking-widest hover:bg-dark-hover 
                dark:ring-offset-dark transition ease-in-out duration-150" for="profileImageUpload">
                    <strong class="flex w-full justify-between">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17" fill="white" class="mr-3">
                            <path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"/>
                        </svg> 
                        
                        <span id="image" class="w-44 truncate text-center">{{ localization[session.language].choose_profile_image }}</span>
                    </strong>
                </label>
                <button class="bg-transparent mt-3 text-additional hover:text-additional-dark font-semibold tracking-widest">
                    {{ localization[session.language].upload }}
                </button>
                <InputError class="mt-3" :message="form.errors.avatar" />
            </form>
        </div>
        <div class="cover-form hidden">
            <div @click="toggleFormRender('profileCoverUpload', 'cover-form'); clearLabel('cover'); form.reset(); form.clearErrors();" class="form fixed left-0 top-0 w-screen h-screen bg-black opacity-30 z-40"></div>
            <form 
                @submit.prevent="form.post(route('image.upload'), { onSuccess: () => { clearLabel('cover'); toggleFormRender('profileCoverUpload', 'cover-form'); form.reset(); form.clearErrors(); }, onError: (error) => { form.reset(); library.clearErrors(form); } })"
                class="absolute left-0 right-0 top-0 bottom-0 px-10 pt-10 pb-8 m-auto w-fit h-fit bg-light-primary dark:bg-dark-primary rounded-lg shadow dark:shadow-none dark:border dark:border-dark flex flex-col justify-evenly items-center z-50"
                enctype="multipart/form-data">
                <input
                    @change="onChange('profileCoverUpload', 'cover')"
                    id="profileCoverUpload"
                    type="file"
                    class="hidden"
                >
                <!-- <label class="inline-flex items-center px-4 py-2 bg-dark-primary dark:bg-dark border border-transparent cursor-pointer select-none rounded-md font-semibold text-xs text-lighter uppercase tracking-widest hover:bg-dark-hover 
                active:ring-2 ring-additional ring-offset-2 ring-offset-light dark:ring-offset-dark transition ease-in-out duration-150" for="profileCoverUpload"> -->
                <label class="inline-flex items-center px-4 py-2 bg-dark-primary dark:bg-dark border border-transparent cursor-pointer select-none rounded-md font-semibold text-xs text-lighter uppercase tracking-widest hover:bg-dark-hover 
                dark:ring-offset-dark transition ease-in-out duration-150" for="profileCoverUpload">
                    <strong class="flex w-full justify-between">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17" fill="white" class="mr-3">
                            <path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"/>
                        </svg> 
                        
                        <span id="cover" class="w-44 truncate text-center">{{ localization[session.language].choose_profile_cover }}</span>
                    </strong>
                </label>
                <button class="bg-transparent mt-3 text-additional hover:text-additional-dark font-semibold tracking-widest">
                    {{ localization[session.language].upload }}
                </button>
                <InputError class="mt-3" :message="form.errors.cover" />
            </form>
        </div>
    </div>
</template>