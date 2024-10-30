<script setup>
    import DeletedBabble from '@/Components/DeletedBabble.vue';
    import AuthorImage from '@/Components/AuthorImage.vue';
    import Attachments from '@/Components/Attachments.vue';
    import dayjs from 'dayjs';
    import 'dayjs/locale/ru';
    import relativeTime from 'dayjs/plugin/relativeTime';
    import library from '@/myLibraryObject.js';
    
    dayjs.extend(relativeTime);
    defineProps(['babble', 'localization', 'session']);
</script>

<template>
    <div class="mt-4 text-darker dark:text-light">
        <div v-if="babble.id != null" class="flex space-x-2">
            <div class="flex-1">
                <div class="grid grid grid-flow-row-dense grid-cols-8 grid-rows-1">
                    <AuthorImage 
                        :user="babble.user"
                    />
                    <div class="grid grid grid-flow-row-dense grid-cols-6 grid-rows-1 col-span-7">
                        <div class="col-span-5">
                            <a v-if="babble.user != null" :href="'/profile/' + babble.user.id">
                                <span class="font-bold">{{ babble.user.name }}</span>
                            </a>
                            <p v-else class="font-bold">
                                {{ localization[session.language].deleted }}
                            </p>
                            <div class="text-base hover:underline decoration-additional decoration-2 cursor-pointer">
                                <a :href="/babble/ + babble.id">
                                    <small>{{ new Date(babble.created_at).toLocaleString() }}</small>
                                    <small class="ml-2">{{ dayjs(babble.created_at).locale(session.language).fromNow() }}</small>
                                    <small v-if="babble.created_at !== babble.updated_at"> &middot; {{ localization[session.language].edited }}</small>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-4 text-base text-justify" v-html="library.urlify(babble.message)"></div>
                <Attachments 
                    :object="babble"
                    :editing="false"
                    :localization="localization"
                    :session="session"
                />
            </div>
        </div>
        <div v-else>
            <DeletedBabble
                :object="babble"
                :localization="localization"
                :session="session"
            />
        </div>
    </div>
</template>