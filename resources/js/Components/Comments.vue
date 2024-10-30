<script setup>
    import Comment from '@/Components/Comment.vue';
    import CommentStoreForm from '@/Components/CommentStoreForm.vue';
    import Dropdown from '@/Components/Dropdown.vue';
    import { ref, computed } from 'vue';
    import { usePage, useForm } from '@inertiajs/vue3';
    import { usePreviewStore } from '@/Stores/currentPreview.js';

    const props = defineProps(['object', 'showComments', 'smallPaddings', 'localization', 'session']);
    const emit = defineEmits(['reply']);
    const store = usePreviewStore();
    const form = useForm({
        message: '',
        links: [],
        images: [],
        videos: [],
        files: [],

        // тут похоже просто для вида
        type: 'store_form',

        add_files: {},
        add_links: [],
    });
    const sortingMethod = ref(null);
    const methodNumber = ref(usePage().props.auth.user ? usePage().props.auth.user.sorting : 0);

    if(methodNumber.value === 0) {
        sortingMethod.value = props.localization[props.session.language].oldest;
    } else if(methodNumber.value === 1) {
        sortingMethod.value = props.localization[props.session.language].newest;
    } else if(methodNumber.value === 2) {
        sortingMethod.value = props.localization[props.session.language].interesting;
    } else {
        sortingMethod.value = props.localization[props.session.language].oldest;
    }

    const sortedComments = computed(() => {
        if(props.object.comments) {
            return props.object.comments.sort(function(a, b) {
                if(methodNumber.value === 0) {
                    return a.id - b.id;
                } else if(methodNumber.value === 1) {
                    return b.id - a.id;
                } else if(methodNumber.value === 2) {
                    return b.likes.length - a.likes.length;
                } else {
                    return a.id - b.id;
                }
            });
        }
    })

    function transferNameOnReply(user, element) {
        let block = element.closest('div.comments-block');
        let textarea = block.getElementsByTagName('textarea')[0];
        let flag = false;

        if(user) {
            let insertable = user.name + ', ';
            for(let i = 0; i < insertable.length; i++) {
                if(insertable[i] !== textarea.value[i]) {
                    flag = true;
                }
            }
        }

        if(flag) {
            form.message = user.name + ', ' + form.message;
        }

        textarea.focus();
    }

    // console.log(store.comment_section_height);
</script>

<style>
    .list-enter-active,
    .list-leave-active,
    .preview-enter-active,
    .preview-leave-active {
        transition: all 0.5s ease;
    }
    .list-enter-from,
    .list-leave-to {
        opacity: 0;
        transform: translateX(30px);
    }
    .preview-enter-from,
    .preview-leave-to {
        opacity: 0;
        transform: translateY(15px);
    }
</style>

<template>
    <div v-if="showComments == true && $page.props.auth.user != null" class="comments-block box-border text-darker dark:text-light overflow-x-hidden" :class="{ 'py-4 pl-4 ': smallPaddings, 'py-4 pl-4 sm:py-6 sm:pl-6': !smallPaddings }">
        <Dropdown :class="{ 'mb-4': smallPaddings, 'mb-6': !smallPaddings }" :align="'left'">
            <template #trigger>
                <button class="flex items-center">
                    <span class="mr-1 text-additional">{{ sortingMethod }}</span>
                    <svg fill="none" height="8" viewBox="0 0 12 8" width="12" class="" xmlns="http://www.w3.org/2000/svg">
                        <path clip-rule="evenodd" d="M2.16 2.3a.75.75 0 0 1 1.05-.14L6 4.3l2.8-2.15a.75.75 0 1 1 .9 1.19l-3.24 2.5c-.27.2-.65.2-.92 0L2.3 3.35a.75.75 0 0 1-.13-1.05z" fill="currentColor" fill-rule="evenodd"></path>
                    </svg>
                </button>
            </template>
            <template #content>
                <button @click="sortingMethod = localization[session.language].oldest; methodNumber = 0;" class="block w-full px-4 py-2 text-left text-sm leading-5 text-dark dark:text-light hover:text-darker dark:hover:text-lighter hover:bg-light dark:hover:bg-dark-hover hover:border-light focus:outline-none focus:text-darker focus:bg-light dark:focus:bg-dark-hover transition duration-150 ease-in-out">
                    {{ localization[session.language].oldest }}
                </button>
                <button @click="sortingMethod = localization[session.language].newest; methodNumber = 1;" class="block w-full px-4 py-2 text-left text-sm leading-5 text-dark dark:text-light hover:text-darker dark:hover:text-lighter hover:bg-light dark:hover:bg-dark-hover hover:border-light focus:outline-none focus:text-darker focus:bg-light dark:focus:bg-dark-hover transition duration-150 ease-in-out">
                    {{ localization[session.language].newest }}
                </button>
                <button @click="sortingMethod = localization[session.language].interesting; methodNumber = 2;" class="block w-full px-4 py-2 text-left text-sm leading-5 text-dark dark:text-light hover:text-darker dark:hover:text-lighter hover:bg-light dark:hover:bg-dark-hover hover:border-light focus:outline-none focus:text-darker focus:bg-light dark:focus:bg-dark-hover transition duration-150 ease-in-out">
                    {{ localization[session.language].interesting }}
                </button>
            </template>
        </Dropdown>
        <div class="comments-section grid content-between">
            <div class="comments" :class="{ 'pr-4': smallPaddings, 'pr-4 sm:pr-6': !smallPaddings }">
                <!-- <TransitionGroup name="list" tag="div"> -->
                    <div v-for="(comment, index) in sortedComments" class="comment" :class="{ 'pt-6': index == sortedComments.length - 1, 'py-6 border-b border-light dark:border-dark': index != sortedComments.length - 1 }" :key="comment.id">
                        <Comment
                            @reply="(user, element) => transferNameOnReply(user, element)"
                            :comment="comment"
                            :object="object"
                            :localization="localization"
                            :session="session"
                        />
                    </div>
                <!-- </TransitionGroup> -->
            </div>
            <div class="form rounded-b-lg"
                :class="{ 
                    'mt-4': object.comments != null && object.comments.length > 0 && smallPaddings != null,
                    'mt-6': object.comments != null && object.comments.length > 0 && smallPaddings == null,
                    'pr-4': store.comment_section_height,
                    'pr-4 sm:pr-6': !store.comment_section_height,
                }">
                <CommentStoreForm v-if="$page.props.auth.user"
                    :form="form"
                    :comments="object.comments"
                    :parentObject="object"
                    :toggleScroll="false"
                    :localization="localization"
                    :session="session"
                />
            </div>
        </div>
    </div>
</template>