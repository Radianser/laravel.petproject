<script setup>
    import AuthorImage from '@/Components/AuthorImage.vue';
    import Comments from '@/Components/Comments.vue';
    import Likes from '@/Components/Likes.vue';
    import { ref } from 'vue';
    import { usePreviewStore } from '@/Stores/currentPreview.js';
    import library from '@/myLibraryObject.js';

    const props = defineProps(['localization', 'session']);
    const store = usePreviewStore();
    const height = ref(null);
    const width = ref(window.innerWidth);
    let element = null;
    const popUpWindow = ref(null);
    const popUpBox = ref(null);

    const scrollTopStart = ref(null);
    const scrollTopEnd = ref(null);
    const clientYStart = ref(null);
    const clientYEnd = ref(null);

    const clientXStart = ref(null);
    const clientXEnd = ref(null);

    ;(() => {
        window.addEventListener('resize', library.throttle(setHeight, 0));
        window.addEventListener('resize', library.throttle(getHeight, 0));
    })();
    function setHeight() {
        store.comment_section_height = element.height;
    }
    function getHeight() {
        height.value = store.comment_section_height;
        width.value = window.innerWidth;

        if(width.value >= 1024 && height.value !== null) {
            return `height: ${height.value}px;`;
        } else {
            return `height: auto;`;
        }
    }
    function touchStart(event) {
        popUpWindow.value = event.target.closest('.pop-up');
        popUpBox.value = event.target.closest('.pop-up-box');
        scrollTopStart.value = popUpWindow._value.scrollTop;
        clientYStart.value = event.changedTouches[0].clientY;
    }
    function touchMove(event) {
        store.weight++;
        if(popUpWindow._value.scrollTop === 0) {
            let touchDifference = clientYStart.value - event.changedTouches[0].clientY;
            if(touchDifference < 0) {
                let translate = Math.abs(touchDifference) - (Math.abs(touchDifference) + Math.pow(store.weight, 2))/1.5;
                if(translate > 0 && translate < 50) {
                    popUpBox._value.style.transform = `translateY(${translate}px)`;
                }
            }
        }
        store.weight = 0;
    }
    function touchEnd(event) {
        scrollTopEnd.value = popUpWindow._value.scrollTop;
        clientYEnd.value = event.changedTouches[0].clientY;
        let touchDifference = clientYStart.value - clientYEnd.value;

        if(scrollTopEnd.value === 0) {
            if(touchDifference < 0 && Math.abs(touchDifference) > 100) {
                popUpWindow.value = null;
                popUpBox.value = null;
                scrollTopStart.value = null;
                scrollTopEnd.value = null;
                clientYStart.value = null;
                clientYEnd.value = null;
                library.closeViewBox(store);
            } else {
                popUpBox._value.style.transform = `translateY(0px)`;
                popUpWindow._value.scrollTop = 0;
            }
        }
    }

    function switchNext() {
        if (store.index + 1 < store.items.length) {
            store.index++;
        }
        library.showViewBox(store, store.items, store.index, store.user)
    }
    function switchBack() {
        if (store.index - 1 >= 0) {
            store.index--;
        }
        library.showViewBox(store, store.items, store.index, store.user)
    }

	function swipeStart(event) {
        clientXStart.value = event.changedTouches[0].clientX;
	}
	function swipeEnd(event) {
        clientXEnd.value = event.changedTouches[0].clientX;
        let touchDifference = clientXStart.value - clientXEnd.value;

        if(touchDifference > 0 && Math.abs(touchDifference) > 100) {
            switchNext();
		}

        if(touchDifference < 0 && Math.abs(touchDifference) > 100) {
            switchBack();
		}
	}

    // console.log(store);
</script>

<template>
    <div class="pop-up bg-black cursor-pointer" @click="library.closeViewBox(store)">
        <div class="pop-up-box min-h-full flex justify-center items-center transition-all ease-linear text-darker dark:text-light">
            <!-- <div
                @click.stop=""
                class="flex max-[1023px]:flex-wrap m-9 max-[1023px]:w-full max-[1023px]:m-2 bg-light-primary dark:bg-dark-primary dark:border dark:border-dark shadow dark:shadow-none rounded-lg overflow-hidden"
                @touchstart="touchStart($event);"
                @touchmove="touchMove($event);"
                @touchend="touchEnd($event);"
            >
                    <img v-if="store.type == 'image'"
                        @load="element = $event.target; setHeight();"
                        :src="store.src"
                        alt=""
                        class="relative lg:min-h-[500px] w-full sm:w-[calc(100%-350px)] lg:max-h-pc object-contain bg-light dark:bg-dark cursor-default"
                    >
                    <iframe v-if="store.type == 'video' && store.embedded == true" :src="store.src" frameborder="0"
                        @load="element = $event.target; setHeight();"
                        class="lg:min-h-[500px] 2xl:min-h-[845px] lg:max-h-pc aspect-video w-full" 
                        allow="autoplay; fullscreen; accelerometer; gyroscope; picture-in-picture; encrypted-media;"
                    >
                    </iframe>
                    <video v-if="store.type == 'video' && store.embedded == false" :src="store.src" controls class="lg:min-h-[500px] 2xl:min-h-[700px] lg:max-h-pc"></video> -->

            <div
                @click.stop=""
                class="grid grid-cols-1 m-9 max-[1023px]:w-full max-[1023px]:m-2 bg-light-primary dark:bg-dark-primary dark:border dark:border-dark shadow dark:shadow-none rounded-lg overflow-hidden"
                :class="{'lg:grid-cols-[1fr_350px]': Number(store.id)}"
                @touchstart="touchStart($event);"
                @touchmove="touchMove($event);"
                @touchend="touchEnd($event);"
            >
                <div class="relative">
                    <div v-if="store.type == 'image' && store.index - 1 >= 0" @click="switchBack()" class="hidden sm:flex absolute group justify-center items-center h-full w-28 left-0 top-0 hover:bg-my-hover-bg">
                        <img src="/storage/icons/arrow_up.svg" class="-rotate-90 select-none opacity-0 group-hover:opacity-100" alt="">
                    </div>

                    <img v-if="store.type == 'image'"
                        @load="element = $event.target; setHeight();"
                        @touchstart="swipeStart($event);"
                        @touchend="swipeEnd($event);"
                        :src="store.src"
                        alt=""
                        class="lg:min-h-[500px] lg:max-h-pc object-contain bg-light dark:bg-dark cursor-default select-none"
                    >

                    <div v-if="store.type == 'image' && store.index + 1 < store.items.length"@click="switchNext()" class="hidden sm:flex absolute group justify-center items-center h-full w-28 right-0 top-0 hover:bg-my-hover-bg">
                        <img src="/storage/icons/arrow_up.svg" class="rotate-90 select-none opacity-0 group-hover:opacity-100" alt="">
                    </div>

                    <iframe v-if="store.type == 'video' && store.embedded == true" :src="store.src" frameborder="0"
                        @load="element = $event.target; setHeight();"
                        class="lg:min-h-[500px] 2xl:min-h-[845px] lg:max-h-pc aspect-video" 
                        allow="autoplay; fullscreen; accelerometer; gyroscope; picture-in-picture; encrypted-media;"
                    >
                    </iframe>
                    <video v-if="store.type == 'video' && store.embedded == false" :src="store.src" controls class="lg:min-h-[500px] 2xl:min-h-[700px] lg:max-h-pc"></video>
                </div>

                <div v-if="Number(store.id)" class="rounded-r-lg cursor-auto" :style="getHeight()">
                    <div class="flex p-4 h-20 border-b border-light dark:border-dark">
                        <AuthorImage
                            :user="store.user"
                        />
                        <div class="flex w-full justify-between">
                            <div class="flex flex-col justify-center">
                                <a v-if="store.user != null" :href="'/profile/' + store.user.id">
                                    <span class="font-bold">{{ store.user.name }}</span>
                                </a>
                                <p v-else class="font-bold">
                                    {{ localization[session.language].deleted }}
                                </p>
                                <div class="">
                                    <div>
                                        <small>{{ new Date(store.created_at).toLocaleString() }}</small>
                                    </div>
                                </div>
                            </div>
                            <Likes 
                                :object="store"
                                :objectType="store.type"
                            />
                        </div>
                    </div>
                
                <!-- <div class="lg:w-[350px] w-full rounded-r-lg cursor-auto" :style="getHeight()">
                    <div class="flex p-4 h-20 border-b border-light dark:border-dark">
                        <AuthorImage
                            :user="store.user"
                        />
                        <div class="flex w-11/12 justify-between">
                            <div class="flex flex-col justify-center">
                                <a v-if="store.user != null" :href="'/profile/' + store.user.id">
                                    <span class="font-bold">{{ store.user.name }}</span>
                                </a>
                                <p v-else class="font-bold">
                                    {{ localization[session.language].deleted }}
                                </p>
                                <div class="">
                                    <div>
                                        <small>{{ new Date(store.created_at).toLocaleString() }}</small>
                                    </div>
                                </div>
                            </div>
                            <Likes 
                                :object="store"
                                :objectType="store.type"
                            />
                        </div>
                    </div> -->
                    
                    <Comments
                        :object="store"
                        :showComments="true"
                        :smallPaddings="true"
                        :localization="localization"
                        :session="session"
                    />
                </div>
            </div>
        </div>
    </div>
</template>