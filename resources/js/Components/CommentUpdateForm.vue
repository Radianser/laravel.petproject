<script setup>
    import InputError from '@/Components/InputError.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import SecondaryButton from '@/Components/SecondaryButton.vue';
    import InputButtons from '@/Components/InputButtons.vue';
    import Attachments from '@/Components/Attachments.vue';
    import { ref, onMounted } from 'vue';
    import { useForm } from '@inertiajs/vue3';
    import library from '@/myLibraryObject.js';

    const props = defineProps(['comment', 'editing', 'before', 'toggleScroll', 'localization', 'session']);
    const emit = defineEmits(['endEditing']);
    const form = useForm({
        message: props.comment.message,
        links: props.comment.links,
        images: props.comment.images,
        videos: props.comment.videos,
        files: props.comment.files,

        // для одной функции из Attachments
        type: 'update_form',

        add_files: {},
        add_links: [],
        delete_attachments: []
    });
    let after = {
        message: '',
        add_files: '',
        add_links: '',
        delete_attachments: ''
    };
    function update(user_id) {
        let data = {
            user_id: user_id,
            commentable_id: props.comment.id,
            commentable_type: props.comment.type,
            message: form.message,
            links: form.links,
            images: form.images,
            videos: form.videos,
            docs: form.files,
            add_files: form.add_files,
            add_links: form.add_links,
            delete_attachments: form.delete_attachments,
        }

        axios.put(route('comment.update', props.comment.id), data)
        .then(result => {
            console.log(result);
            if(result.data.id) {
                props.comment.message = result.data.message;
                props.comment.updated_at = result.data.updated_at;
                props.comment.links = result.data.links;
                props.comment.images = result.data.images;
                props.comment.videos = result.data.videos;
                props.comment.files = result.data.files;

                library.uploadFiles(form, props.comment, result.data.id, result.data.type);
                form.reset();
                form.clearErrors();
                emit('endEditing');
            }
        }).catch(error => {
            if (error.response.status === 422) {
                form.errors.message = error.response.data.message;
                library.clearErrors(form);
            } else {
                form.errors.message = props.localization[props.session.language].message_store_error;
                library.clearErrors(form);
            }
        });
    }
    const textarea = ref(null);
    onMounted(() => {
        textarea.value.focus();
    })
</script>

<template>
    <form
        class="mt-4"
        @submit.prevent="library.setComparisonData(after, form); library.isEqual(before, after) ? $emit('endEditing') : update($page.props.auth.user.id);
    ">
        <div class="relative flex gap-1.5 h-fit">
            <textarea
                ref="textarea"
                v-model="form.message"
                @input="library.textAreaAdjust($event.target)"
                @paste="library.copyData($event, form)"
                @drop="library.dropData($event, form)"
                @focus="library.textAreaAdjust($event.target)"
                @blur="library.collapseTextarea($event.target, form)"
                class="w-full h-10 pr-[60px] overflow-hidden resize-none text-darker dark:text-light border-light bg-light-primary dark:bg-dark-input focus:border-additional focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm dark:shadow-none dark:border dark:border-dark"
                :placeholder="localization[session.language].input_placeholder"
            >
            </textarea>
            <div class="absolute h-full flex right-0">
                <InputButtons
                    :form="form"
                    :toggleScroll="toggleScroll"
                />
            </div>
        </div>
        <InputError :message="form.errors.message" class="mt-4" />
        <div class="flex justify-between">
            <div class="w-fit mt-4 flex h-9">
                <PrimaryButton>{{ localization[session.language].save }}</PrimaryButton>
                <SecondaryButton @click="form.reset(); form.clearErrors(); $emit('endEditing');">{{ localization[session.language].cancel }}</SecondaryButton>
            </div>
            <div class="w-fit mt-4 flex h-9 items-center text-xs tracking-widest">
                {{ form.message.length }} / 1000
            </div>
        </div>
        <Attachments 
            :object="form"
            :editing="editing"
            :localization="localization"
            :session="session"
        />
    </form>
</template>