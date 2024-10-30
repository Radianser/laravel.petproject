<script setup>
    import Dropdown from '@/Components/Dropdown.vue';
    import AuthorImage from '@/Components/AuthorImage.vue';
    import Likes from '@/Components/Likes.vue';
    import Attachments from '@/Components/Attachments.vue';
    import CommentUpdateForm from '@/Components/CommentUpdateForm.vue';
    import dayjs from 'dayjs';
    import 'dayjs/locale/ru';
    import relativeTime from 'dayjs/plugin/relativeTime';
    import { ref } from 'vue';
    import library from '@/myLibraryObject.js';

    dayjs.extend(relativeTime);

    const props = defineProps(['comment', 'object', 'localization', 'session']);
    const emit = defineEmits(['reply']);
    const editing = ref(false);
    let before = {
        message: props.comment.message,
        add_files: '{}',
        add_links: '[]',
        delete_attachments: '[]'
    };
    function commentDestroy(id) {
        axios.delete(route('comment.destroy', id))
        .then(result => {
            if(result.data) {
                for(let i = 0; i < props.object.comments.length; i++) {
                    if(props.object.comments[i].id == result.data) {
                        props.object.comments.splice(i, 1);
                    }
                }
            }
        });
    }

    // console.log(props.comment);
</script>

<template>
        <div class="label flex">
            <AuthorImage
                :user="comment.user"
            />
            <div class="flex w-11/12 justify-between">
                <div class="flex flex-col justify-center">
                    <a v-if="comment.user != null" :href="'/profile/' + comment.user.id">
                        <span class="font-bold">{{ comment.user.name }}</span>
                    </a>
                    <p v-else class="font-bold">
                        {{ localization[session.language].deleted }}
                    </p>
                    <div class="">
                        <small>{{ new Date(comment.created_at).toLocaleString() }}</small>
                        <small class="ml-2">{{ dayjs(comment.created_at).locale(session.language).fromNow() }}</small>
                        <small v-if="comment.created_at !== comment.updated_at"> &middot; {{ localization[session.language].edited }}</small>
                    </div>
                </div>
                <Dropdown v-if="comment.user != null && $page.props.auth.user != null && comment.user.id === $page.props.auth.user.id">
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
                            @click="commentDestroy(comment.id)"
                        >
                            {{ localization[session.language].delete }}
                        </button>
                    </template>
                </Dropdown>
            </div>
        </div>
        <CommentUpdateForm v-if="editing"
            @end-editing="editing = false;"
            :comment="comment"
            :editing="editing"
            :before="before"
            :toggleScroll="false"
            :localization="localization"
            :session="session"
        />
        <div v-else>
            <div class="mt-4 text-base text-justify whitespace-pre-line" style="word-break: break-word;" v-html="library.urlify(comment.message)"></div>
            <Attachments
                :object="comment"
                :editing="false"
                :localization="localization"
                :session="session"
            />
        </div>
        <div v-if="!editing" class="mt-4 flex items-center justify-between">
            <span 
                class="text-sm text-additional select-none cursor-pointer"
                @click="$emit('reply', comment.user, $event.target);"
            >
                {{ localization[session.language].reply }}
            </span>
            <Likes 
                :object="comment"
                objectType="comment"
            />
        </div>
</template>