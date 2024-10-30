<script setup>
    import InputError from '@/Components/InputError.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import SecondaryButton from '@/Components/SecondaryButton.vue';
    import InputButtons from '@/Components/InputButtons.vue';
    import Attachments from '@/Components/Attachments.vue';
    import { useForm } from '@inertiajs/vue3';
    import { nextTick, onMounted, ref } from 'vue';
    import library from '@/myLibraryObject.js';

    const props = defineProps(['babbles', 'parentObject', 'toggleScroll', 'localization', 'session']);
    const emit = defineEmits(['endEditing']);
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
    let uri = window.location.pathname;
    const textarea = ref(null);

    function store(user_id) {
        let data = {
            user_id: user_id,
            babble_id: null,
            babble_user_id: null,
            message: form.message,
            add_files: form.add_files,
            add_links: form.add_links,
        }

        if(props.parentObject !== null) {
            data.babble_id = props.parentObject.id;
            data.babble_user_id = props.parentObject.user_id;
        }
        
        axios.post(route('babble.store'), data)
        .then(result => {
            if(result.data.id) {
                if(uri == '/feed' || uri == '/home') {
                    props.babbles.unshift(result.data);
                }
                if(props.parentObject !== null) {
                    props.parentObject.replies.push(result.data);
                    emit('endEditing');
                }

                library.uploadFiles(form, props.babbles[0], result.data.id, result.data.type);
                form.reset();
                form.clearErrors();
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

    // console.log(textarea);
    // onMounted(() => {
        // textarea.value.focus();
        // textarea._value.focus();
    // })
</script>

<template>
    <form
        class="w-full mt-1"
        @submit.prevent="store($page.props.auth.user.id); library.collapseTextarea(textarea, null, true);"
    >
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
                <SecondaryButton @click="form.reset(); form.clearErrors(); library.collapseTextarea(textarea, null, true);" class="text-darker dark:text-light">
                    {{ localization[session.language].cancel }}
                </SecondaryButton>
            </div>
            <div v-if="form.message.length > 0" class="w-fit mt-4 flex h-9 items-center text-xs tracking-widest">
                {{ form.message.length }} / 1000
            </div>
        </div>
    </form>
    <Attachments 
        :object="form"
        :editing="true"
        :localization="localization"
        :session="session"
    />
</template>