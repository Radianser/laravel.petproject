<script setup>
    import { usePreviewStore } from '@/Stores/currentPreview.js';
    import library from '@/myLibraryObject.js';

    const props = defineProps(['object', 'editing', 'localization', 'session']);
    const store = usePreviewStore();

    function attachmentDestroy(attachment, type) {
        for(let i = 0; i < props.object[type].length; i++) {
            if(props.object[type][i].id == attachment.id) {
                props.object[type].splice(i, 1);
            }
        }
        for(let key in props.object.add_files) {
            if(key == attachment.id) {
                delete props.object.add_files[key];
            }
        }
        if(props.object.type == "update_form") {
            props.object.delete_attachments.push({object_id: attachment.id, type: type});
        }
    }
    function getImageClass(length) {
        if(length < 7) {
            return length;
        } else {
            return '';
        }
    }

    // console.log(props.object);
</script>

<template>
    <!-- images -->
    <div
        class="w-full grid gap-[5px] image-attachments"
        :class="[object.images.length > 0 ? 'mt-4' : '', 'images' + getImageClass(object.images.length)]"
    >
        <picture v-for="(image, index) in object.images" :key="image" class="relative attachment aspect-square text-darker dark:text-light">
            <div v-if="image.stub" class="w-full flex flex-col justify-center items-center aspect-square rounded-lg cursor-pointer object-cover bg-light dark:bg-dark shadow-sm dark:shadow-none dark:border dark:border-dark">
                <span class="loader"></span>
                <span class="percentage mt-1 text-base font-semibold">{{ image.percentage }}</span>
            </div>
            <img v-else @click="library.showViewBox(store, object.images, index, object.user)" :src="image.src" alt="" class="w-full h-full rounded-lg cursor-pointer object-cover aspect-square bg-light dark:bg-dark shadow-sm dark:shadow-none dark:border dark:border-dark" loading="lazy">
            
            <div v-if="editing"
                @click="attachmentDestroy(image, 'images');"
                class="destroy-button absolute top-0 right-0 ring-4 ring-light-primary dark:ring-dark-primary w-6 h-6 grid justify-center items-center bg-dark-primary dark:bg-dark hover:bg-dark-hover text-sm rounded-tr-lg rounded-bl-lg select-none cursor-pointer"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 -960 960 960" class="fill-white">
                    <path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/>
                </svg>
            </div>
        </picture>
    </div>

    <!-- videos -->
    <div>
        <div v-for="(video, index) in object.videos" :key="video" class="relative w-full flex flex-wrap mt-4 text-darker dark:text-light">
            <div v-if="video.stub" class="w-full flex flex-col justify-center items-center aspect-video rounded-lg cursor-pointer bg-light dark:bg-dark shadow-sm dark:shadow-none dark:border dark:border-dark">
                <span class="loader"></span>
                <span class="percentage mt-1 text-base font-semibold ">{{ video.percentage + ', ' + video.identifier}}</span>
            </div>
            <video v-else-if="!video.embedded" :src="video.src" controls width="800" class="rounded-lg bg-light dark:bg-dark shadow-sm dark:shadow-none dark:border dark:border-dark"></video>
            <iframe v-else :src="video.src" frameborder="0" loading="lazy" class="w-full aspect-video rounded-lg bg-light dark:bg-dark shadow-sm dark:shadow-none dark:border dark:border-dark" allow="autoplay; fullscreen; accelerometer; gyroscope; picture-in-picture; encrypted-media;"></iframe>
            <div class="mt-1.5 text-sm hover:underline decoration-additional decoration-2 cursor-pointer" @click="library.showViewBox(store, object.videos, index, object.user)">
                {{ localization[session.language].look }}
            </div>
            <div v-if="editing"
                @click="attachmentDestroy(video, 'videos');"
                class="destroy-button absolute top-0 right-0 ring-4 ring-light-primary dark:ring-dark-primary w-6 h-6 grid justify-center items-center bg-dark-primary dark:bg-dark hover:bg-dark-hover text-sm rounded-tr-lg rounded-bl-lg select-none cursor-pointer"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 -960 960 960" class="fill-white">
                    <path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- links -->
    <div>
        <div v-for="link in object.links" :key="link" class="relative mt-4 text-darker dark:text-light">
            <div v-if="link.stub" class="w-full h-14 flex flex-col justify-center items-center rounded-lg cursor-pointer bg-light dark:bg-dark shadow-sm dark:shadow-none dark:border dark:border-dark">
                <span class="loader"></span>
            </div>
            <a v-else :href="link.domain + link.uri" target="_blank" :title="link.title">
                <div class="grid grid-cols-7 h-14">
                    <div class="max-h-14 mr-2 overflow-hidden">
                        <img :src="'https://t0.gstatic.com/faviconV2?client=SOCIAL&type=FAVICON&fallback_opts=TYPE,SIZE,URL&url=' + link.domain + link.uri + '&size=256'" alt="" class="w-full h-full object-contain">
                    </div>
                    <div class="col-span-6 p-2 rounded-lg bg-light dark:bg-dark-input shadow-sm dark:shadow-none dark:border dark:border-dark">
                        <div class="truncate font-bold text-sm">{{ link.title }}</div>
                        <div class="truncate text-sm">{{ link.domain }}</div>
                    </div>
                </div>
            </a>
            <div v-if="editing"
                @click="attachmentDestroy(link, 'links');"
                class="destroy-button absolute top-0 right-0 ring-4 ring-light-primary dark:ring-dark-primary w-6 h-6 grid justify-center items-center bg-dark-primary dark:bg-dark hover:bg-dark-hover text-sm rounded-tr-lg rounded-bl-lg select-none cursor-pointer"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 -960 960 960" class="fill-white">
                    <path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- files -->
    <div>
        <div v-for="file in object.files" :key="file" class="relative mt-4 text-darker dark:text-light">
            <div v-if="file.stub" class="w-full flex flex-row gap-4 py-2 justify-center items-center rounded-lg cursor-pointer bg-light dark:bg-dark shadow-sm dark:shadow-none dark:border dark:border-dark">
                <span class="loader"></span>
                <span class="percentage mt-1 text-base font-semibold">{{ file.percentage }}</span>
            </div>
            <a v-else :href="file.src" :title="file.original_title">
                <div class="grid grid-cols-7 h-14">
                    <div class="flex justify-center items-center max-h-14 mr-2 bg-light dark:bg-dark-primary overflow-hidden rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 -960 960 960" class="fill-gray-600 dark:fill-white">
                            <path d="M240-80q-33 0-56.5-23.5T160-160v-640q0-33 23.5-56.5T240-880h320l240 240v480q0 33-23.5 56.5T720-80H240Zm280-520v-200H240v640h480v-440H520ZM240-800v200-200 640-640Z"/>
                        </svg>
                    </div>
                    <div class="col-span-6 p-2 rounded-lg bg-light dark:bg-dark-input shadow-sm dark:shadow-none dark:border dark:border-dark">
                        <!-- <div v-if="file.extension" class="truncate font-bold text-sm">{{ file.original_title }}</div>
                        <div v-else class="truncate font-bold text-sm">{{ file.original_title }}</div> -->
                        <div class="truncate font-bold text-sm">{{ file.original_title }}</div>

                        <div v-if="Number(file.size) !== NaN" class="truncate text-sm">{{ (file.size / 1024).toFixed(1) + " KB" }}</div>
                        <div v-else class="truncate text-sm">{{ localization[session.language].linked }}</div>
                    </div>
                </div>
            </a>
            <div v-if="editing"
                @click="attachmentDestroy(file, 'files');"
                class="destroy-button absolute top-0 right-0 ring-4 ring-light-primary dark:ring-dark-primary w-6 h-6 grid justify-center items-center bg-dark-primary dark:bg-dark hover:bg-dark-hover text-sm rounded-tr-lg rounded-bl-lg select-none cursor-pointer"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 -960 960 960" class="fill-white">
                    <path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/>
                </svg>
            </div>
        </div>
    </div>
</template>