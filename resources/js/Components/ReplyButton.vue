<script setup>
    import AuthorImage from '@/Components/AuthorImage.vue';
    import BabbleStoreForm from '@/Components/BabbleStoreForm.vue';
    import { ref } from 'vue';
    import { usePage } from '@inertiajs/vue3';
    import library from '@/myLibraryObject.js';

    const props = defineProps(['babble', 'babbles', 'localization', 'session']);
    const showReplyForm = ref(false);
    const html = document.querySelector('html');

    function toggleHTMLScroll() {
        if(usePage().props.auth.user) {
            html.classList.toggle('overflow-hidden');
            showReplyForm.value = !showReplyForm.value;
        } else {
            window.location.href = route('login');
        }
    }

    // console.log(props.babble.user);
</script>

<style>
    .list-enter-active,
    .list-leave-active {
        transition: all 0.2s ease;
    }
    .list-enter-from,
    .list-leave-to {
        opacity: 0;
        transform: translateY(15px);
    }
</style>
 
<template>
    <!-- Reply button -->
    <div class="text-gray-400 text-[11px] flex items-center group/replies">
        <a v-if="babble.replies.length > 0" :href="'/replies/' + babble.id" class="mr-1 font-bold">
            {{ library.roundValue(babble.replies.length) }}
        </a>
        <div
            @click="toggleHTMLScroll()"
            class="h-4 w-4 cursor-pointer bg-answer-light-gray hover:bg-answer-gray bg-no-repeat bg-center bg-contain">
        </div>
    </div>

    <!-- black cover -->
    <div v-if="showReplyForm"
        class="form fixed left-0 top-0 w-screen h-screen bg-black opacity-30 z-40">               
    </div>
    <!-- Form -->
    <Transition name="list">
        <div v-if="showReplyForm === true" class="pop-up" @click="toggleHTMLScroll()">
            <div class="w-full min-h-full py-24 flex justify-center items-center">
                <div @click.stop="" class="w-full sm:w-1/2 p-6 max-[640px]:mx-4 sm:mr-4 h-fit max-w-2xl rounded-lg bg-light-primary dark:bg-dark-primary dark:border dark:border-dark shadow dark:shadow-none">
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
                        @end-editing="toggleHTMLScroll()"
                        :babbles="babbles"
                        :parentObject="babble"
                        :toggleScroll="false"
                        :localization="localization"
                        :session="session"
                    />
                </div>
            </div>
        </div>
    </Transition>
</template>