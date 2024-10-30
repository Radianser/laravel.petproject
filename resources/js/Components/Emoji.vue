<script setup>
    import emojis from '@/emoji.js';
    const props = defineProps(['form', 'width']);
    
    function pasteEmoji(event) {
        if(props.form.message != null || props.form.message != undefined) {
            props.form.message += event.target.innerHTML.trim();
        } else {
            props.form.message = event.target.innerHTML.trim();
        }
    }

    // console.log(props.width);
</script>

<template>
    <div 
        class="emoji_panel absolute grid p-1.5 justify-between bottom-[40px] right-[0] max-h-[300px] bg-dark-primary overflow-auto rounded-lg z-[999999999] shadow dark:shadow-none dark:border dark:border-dark"
        :style="{ 'width': width + 'px' }"
    >
        <div 
            class="w-8 h-8 cursor-pointer flex justify-center items-center hover:bg-dark-hover rounded-lg p-1"
            @click="pasteEmoji($event)"
            v-for="emoji in emojis" :key="emoji" :data-prop="emoji">
            {{ String.fromCodePoint(`0x` + emoji) }}
        </div>
    </div>
</template>