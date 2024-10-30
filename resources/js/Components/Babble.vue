<script setup>
    import Dropdown from '@/Components/Dropdown.vue';
    import AuthorImage from '@/Components/AuthorImage.vue';
    import Likes from '@/Components/Likes.vue';
    import Reply from '@/Components/Reply.vue';
    import ReplyButton from '@/Components/ReplyButton.vue';
    import Comments from '@/Components/Comments.vue';
    import Attachments from '@/Components/Attachments.vue';
    import BabbleUpdateForm from '@/Components/BabbleUpdateForm.vue';
    import dayjs from 'dayjs';
    import 'dayjs/locale/ru';
    import relativeTime from 'dayjs/plugin/relativeTime';
    import { ref } from 'vue';
    import { usePage } from '@inertiajs/vue3';
    import library from '@/myLibraryObject.js';

    dayjs.extend(relativeTime);

    const props = defineProps(['babble', 'babbles', 'localization', 'session']);
    const editing = ref(false);
    let before = {
        message: '',
        add_files: '',
        add_links: '',
        delete_attachments: ''
    };
    const showComments = ref(false);
    const showReply = ref(false);

    function babbleDestroy(id) {
        axios.delete(route('babble.destroy', id))
        .then(result => {
            if(result.data) {
                for(let i = 0; i < props.babbles.length; i++) {
                    if(props.babbles[i].id == result.data) {
                        props.babbles.splice(i, 1);
                    }
                }
            }
        });
    }
    function toggleComments() {
        if(usePage().props.auth.user) {
            showComments.value = !showComments.value;
        } else {
            window.location.href = route('login');
        }
    }

    // onMounted(() => {
    //     console.log('awef');
    // });
</script>

<template>
    <div class="p-4 sm:p-6 text-darker dark:text-light bg-light-primary dark:bg-dark-primary dark:border dark:border-dark shadow dark:shadow-none rounded-lg" :class="{ 'mt-4': babble.id != babbles[0].id }">
        <div class="flex space-x-2">
            <div class="flex-1 w-full">
                <div class="label flex">
                    <AuthorImage 
                        :user="babble.user"
                    />
                    <div class="flex w-11/12 justify-between">
                        <div class="flex flex-col justify-center">
                            <a v-if="babble.user != null" :href="'/profile/' + babble.user.id">
                                <span class="font-bold">{{ babble.user.name }}</span>
                            </a>
                            <p v-else class="font-bold">
                                {{ localization[session.language].deleted }}
                            </p>
                            <div class="hover:underline decoration-additional decoration-2 cursor-pointer">
                                <a :href="/babble/ + babble.id">
                                    <small>{{ new Date(babble.created_at).toLocaleString() }}</small>
                                    <small class="ml-2">{{ dayjs(babble.created_at).locale(session.language).fromNow() }}</small>
                                    <small v-if="babble.created_at !== babble.updated_at"> &middot; {{ localization[session.language].edited }}</small>
                                </a>
                            </div>
                        </div>
                        <Dropdown v-if="babble.user != null && $page.props.auth.user != null && babble.user.id === $page.props.auth.user.id">
                            <template #trigger>
                                <button>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                    </svg>
                                </button>
                            </template>
                            <template #content>
                                <button
                                    class="block w-full px-4 py-2 text-left text-sm leading-5 text-dark dark:text-light hover:text-darker dark:hover:text-lighter hover:bg-light dark:hover:bg-dark-hover hover:border-light focus:outline-none focus:text-darker focus:bg-light dark:focus:bg-dark-hover transition duration-150 ease-in-out"
                                    @click="editing = true">
                                    {{ localization[session.language].edit }}
                                </button>
                                <button class="block w-full px-4 py-2 text-left text-sm leading-5 text-dark dark:text-light hover:text-darker dark:hover:text-lighter hover:bg-light dark:hover:bg-dark-hover hover:border-light focus:outline-none focus:text-darker focus:bg-light dark:focus:bg-dark-hover transition duration-150 ease-in-out"
                                    @click="babbleDestroy(babble.id)"
                                >
                                    {{ localization[session.language].delete }}
                                </button>
                            </template>
                        </Dropdown>
                    </div>
                </div>
                <BabbleUpdateForm v-if="editing"
                    @end-editing="editing = false;"
                    :babble="babble"
                    :editing="editing"
                    :before="before"
                    :toggleScroll="false"
                    :localization="localization"
                    :session="session"
                />
                <div v-else>
                    <div class="mt-4 text-base text-justify whitespace-pre-line" v-html="library.urlify(babble.message)"></div>
                    <Attachments 
                        :object="babble"
                        :editing="false"
                        :localization="localization"
                        :session="session"
                    />
                </div>
            </div>
        </div>
        <div v-if="babble.id != null" class="flex flex-col">
            <div v-if="babble.reply != null" class="mt-4 w-full">
                <span class="text-sm">{{ localization[session.language].in_reply_to + " " }}</span>
                <span @click="showReply = !showReply" class="text-additional cursor-pointer font-bold text-sm">{{ babble.reply.user ? babble.reply.user.name : localization[session.language].deleted }}</span>
                <div v-if="showReply == true">
                    <Reply 
                        :babble="babble.reply"
                        :localization="localization"
                        :session="session"
                    />
                </div>
            </div>
            <div class="grid grid-cols-2 mt-4 text-sm">
                <div @click="toggleComments()" class="w-32 cursor-pointer hover:underline decoration-additional decoration-2 select-none">{{ localization[session.language].comments }} {{ babble.comments.length != 0 ? '(' + babble.comments.length + ')' : '' }}</div>
                <div class="flex justify-end">
                    <Likes
                        :object="babble"
                        objectType="babble"
                        class="mr-4"
                    />
                    <ReplyButton 
                        :babble="babble" 
                        :babbles="babbles"
                        :localization="localization"
                        :session="session"
                    />
                </div>
            </div>
        </div>
    </div>
    <div class="mt-0.5 max-[640px]:mx-3 shadow max-w-xl mx-auto rounded-b-lg bg-light-primary dark:bg-dark-primary">
        <Comments
            :object="babble"
            :showComments="showComments"
            :smallPaddings="false"
            :localization="localization"
            :session="session"
        />
    </div>
</template>