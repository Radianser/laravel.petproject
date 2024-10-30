<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
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
 
<template>
    <Head :title="localization[session.language].gallery" />
    <AuthenticatedLayout :localization="localization" :session="session">
        <ViewBox v-if="store.show === true" :localization="localization" :session="session"/>
        <div class="w-full p-4">
            <div class="max-w-2lg md:max-w-5xl mx-auto p-2 lg:p-4 text-darker dark:text-light bg-light-primary dark:bg-dark-primary dark:border dark:border-dark shadow dark:shadow-none rounded-lg">
                <div v-if="$page.props.auth.user.id === user.id" class="h-8 flex gap-4 justify-between">
                    <button @click="images.data.length > 0 ? edit = !edit : false;" class="w-32 h-full flex justify-center items-center rounded-lg text-lighter bg-dark-primary dark:bg-dark hover:bg-dark-hover active:bg-dark-hover transition ease-in-out duration-150">
                        {{ !edit ? localization[session.language].edit : localization[session.language].finish }}
                    </button>
                    <div class="flex justify-between items-center font-semibold text-xs uppercase tracking-widest">
                        {{ localization[session.language].user_gallery }}
                    </div>
                    <Clip
                        class="w-32 h-full cursor-pointer flex justify-center items-center rounded-lg text-lighter bg-dark-primary dark:bg-dark hover:bg-dark-hover active:bg-dark-hover transition ease-in-out duration-150"
                        :form="form"
                        :visible="false"
                    >
                        <template #sign>
                            {{ localization[session.language].add_image }}
                        </template>
                    </Clip>
                </div>
                <div v-else class="flex justify-center items-center font-semibold text-xs uppercase tracking-widest">
                    {{ localization[session.language].user_gallery }}
                </div>
                <hr class="my-4 border-light dark:border-dark">
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