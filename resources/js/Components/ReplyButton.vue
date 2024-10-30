<script setup>
    import AuthorImage from '@/Components/AuthorImage.vue';
    import BabbleStoreForm from '@/Components/BabbleStoreForm.vue';
    import { ref } from 'vue';
    import { usePage } from '@inertiajs/vue3';
    import library from '@/myLibraryObject.js';

    const props = defineProps(['babble', 'babbles', 'localization', 'session']);
    const show = ref(false);

    function toggleScroll() {
        if(usePage().props.auth.user) {
            let html = document.querySelector('html');
            html.classList.toggle('overflow-hidden');
            show.value = !show.value;
        } else {
            window.location.href = route('login');
        }
    }

    // console.log(props.babble.user);
</script>
 
<template>
    <!-- Reply button -->
    <div class="text-gray-400 text-[11px] flex items-center group/replies">
        <a v-if="babble.replies.length > 0" :href="'/replies/' + babble.id" class="mr-1 font-bold">{{ library.roundValue(babble.replies.length) }}</a>
        <div @click="toggleScroll()" class="h-4 w-4 cursor-pointer bg-answer-light-gray hover:bg-answer-gray bg-no-repeat bg-center bg-contain"></div>
    </div>

    <!-- Form -->
    <div v-if="show === true" class="pop-up bg-black" @click="toggleScroll()">
        <div class="w-full min-h-full py-24 flex justify-center items-center">
            <div @click.stop="" class="reply-form w-full sm:w-1/2 p-6 max-[640px]:mx-4 sm:mr-4 h-fit max-w-2xl rounded-lg bg-light-primary dark:bg-dark-primary dark:border dark:border-dark shadow dark:shadow-none">
                <div class="relative flex flex-wrap h-16 mb-8 pl-32">
                    <div>
                        <AuthorImage
                            class="absolute top-0 left-0 rounded-full border-solid border-light-primary dark:border-dark-primary border-4"
                            :user="$page.props.auth.user"
                        />
                        <AuthorImage
                            class="absolute top-4 left-12 rounded-full border-solid border-light-primary dark:border-dark-primary border-4"
                            :user="babble.user"
                        />
                    </div>
                    <div class="flex w-full items-center">
                        <p class="">{{ localization[session.language].reply_on }} <b :title="babble.message">{{ localization[session.language].post }}</b> {{ localization[session.language].from }} {{ new Date(babble.created_at).toLocaleString() }}</p>
                    </div>
                    <div class="flex w-full items-center">
                        <p class="max-w-full truncate text-additional" :title="babble.message"><b>{{ babble.message }}</b></p>
                    </div>
                </div>
                <BabbleStoreForm
                    @end-editing="toggleScroll()"
                    :babbles="babbles"
                    :parentObject="babble"
                    :toggleScroll="false"
                    :localization="localization"
                    :session="session"
                />
            </div>
        </div>
    </div>
</template>