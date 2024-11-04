<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import { Head, useForm } from '@inertiajs/vue3';
    import { ref } from 'vue';
    import ViewBox from '@/Components/ViewBox.vue';
    import Clip from '@/Components/Clip.vue';
    import { usePreviewStore } from '@/Stores/currentPreview.js';
    import library from '@/myLibraryObject.js';
    
    let props = defineProps(['user', 'images', 'localization', 'session']);
    const store = usePreviewStore();
    const edit = ref(false);
    const form = useForm({
        user: props.user,
        images: props.images.data,
        add_files: {},
        automatic_upload: true
    });

    function imageDestroy(image) {
        axios.delete(route('image.destroy', image.id))
        .then(result => {
            if(result.data === 'destroyed') {
                for(let i = 0; i < props.images.data.length; i++) {
                    if(props.images.data[i].id == image.id) {
                        props.images.data.splice(i, 1);
                    }
                }
                for(let i = 0; i < form.images.length; i++) {
                    if(form.images[i].id == image.id) {
                        form.images.splice(i, 1);
                    }
                }
            }
        });
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
    <Head :title="localization[session.language].gallery" />
    <AuthenticatedLayout :localization="localization" :session="session">

        <div v-if="store.show === true"
            class="form fixed left-0 top-0 w-screen h-screen bg-black opacity-30 z-40">               
        </div>
        <Transition name="viewbox">
            <ViewBox v-if="store.show === true" :localization="localization" :session="session" />
        </Transition>

        <div class="w-full p-4">
            <div class="max-w-2lg md:max-w-5xl mx-auto p-2 lg:p-4 text-darker dark:text-light bg-light-primary dark:bg-dark-primary dark:border dark:border-dark shadow dark:shadow-none rounded-lg">

                <h1 class="text-center mb-2 font-semibold text-xs uppercase tracking-widest">
                    {{ localization[session.language].user_gallery }}
                </h1>
                <hr class="my-2 lg:my-4 border-light dark:border-dark">

                <div v-if="$page.props.auth.user.id === user.id" class="h-8 mb-2 lg:mb-4 flex gap-4 justify-between">
                    <PrimaryButton @click="edit = !edit">
                        {{ !edit ? localization[session.language].edit : localization[session.language].finish }}
                    </PrimaryButton>
                    <PrimaryButton>
                            <Clip
                                :form="form"
                                :visible="false"
                            >
                            <template #sign>
                                {{ localization[session.language].add_image }}
                            </template>
                        </Clip>
                    </PrimaryButton>
                </div>
                
                <div v-if="images.data.length > 0" class="grid grid-cols-3 sm:grid-cols-5 gap-2 lg:gap-4 rounded-lg">
                    <picture v-for="(image, index) in form.images" :key="image" class="relative w-full">
                        <div v-if="image.stub" class="w-full flex flex-col justify-center items-center aspect-square rounded-lg cursor-pointer object-cover bg-light dark:bg-dark shadow dark:shadow-none dark:border dark:border-dark">
                            <span class="loader"></span>
                            <span class="percentage mt-1 text-base font-semibold">{{ image.percentage }}</span>
                        </div>
                        <img v-else @click="library.showViewBox(store, form.images, index, user)" :src="image.src" alt="" class="w-full aspect-square rounded-lg cursor-pointer object-cover bg-light dark:bg-dark shadow-sm dark:shadow-none dark:border dark:border-dark" loading="lazy">
                        <div v-if="edit"
                            @click="imageDestroy(image)"
                            class="destroy-button absolute top-0 right-0 ring-4 ring-light-primary dark:ring-dark-primary w-6 h-6 grid justify-center items-center bg-dark-primary dark:bg-dark hover:bg-dark-hover text-sm rounded-tr-lg rounded-bl-lg select-none cursor-pointer"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 -960 960 960" class="fill-white">
                                <path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/>
                            </svg>
                        </div>
                    </picture>
                </div>
                <div v-else class="px-4 flex justify-center items-center">
                    <div class="px-10 py-8">
                        {{ localization[session.language].gallery_empty }}
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>