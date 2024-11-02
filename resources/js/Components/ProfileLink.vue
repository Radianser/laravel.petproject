<script setup>
    import dayjs from 'dayjs';
    import 'dayjs/locale/ru';
    import relativeTime from 'dayjs/plugin/relativeTime';
    import library from '@/myLibraryObject.js';

    dayjs.extend(relativeTime);

    const props = defineProps(['profile', 'profiles', 'title', 'localization', 'session']);
    let uri = window.location.pathname;
    let timezone = +4;

    function removeSubscription(auth_user_id, user_id, title) {
        let data = {
            authUserId: auth_user_id,
            userId: user_id,
            title: title
        };
        
        axios.post(route('user.unsubscribe'), data)
        .then(result => {
            if(result.data) {
                for(let i = 0; i < props.profiles.length; i++) {
                    if(props.profiles[i].id == result.data) {
                        props.profiles.splice(i, 1);
                    }
                }
            }
        });
    }

    // console.log(props.profile);
</script>
 
<template>
    <a v-if="profile.id != null" :href="'/profile/' + profile.id" class="relative px-6 py-4 flex items-center text-darker dark:text-light bg-light-primary dark:bg-dark-primary dark:border dark:border-dark shadow dark:shadow-none rounded-lg cursor-pointer overflow-hidden" :class="{ 'mt-4': profile.id != profiles[0].id }">
        <img v-if="profile.avatar != null" :class="{'ring ring-green-400 ring-offset-[3px]': library.isOnline(profile.last_action, timezone)}" class="h-14 w-14 mr-4 object-cover rounded-full ring-offset-light dark:ring-offset-dark bg-light dark:bg-dark" :src="'/storage/avatars/' + profile.avatar" alt="">
        <img v-else :class="{'ring ring-green-400 ring-offset-[3px]': library.isOnline(profile.last_action, timezone)}" class="h-14 w-14 mr-4 object-cover rounded-full ring-offset-light dark:ring-offset-dark bg-light dark:bg-dark" src="/storage/icons/default.jpg" alt="">
        <div class="flex-1">
            <div class="flex justify-between items-center">
                <div>
                    <span>{{ profile.name }}</span>
                </div>
                <div class="text-sm">
                    <span v-if="library.isOnline(profile.last_action, timezone)">{{ localization[session.language].online }}</span>
                    <!-- <span v-else>{{ localization[session.language].last_seen + ':' }} {{ dayjs(profile.last_action).add(timezone, 'hour').locale($page.props.auth.user.language).fromNow() }}</span> -->
                    <span v-else>{{ dayjs(profile.last_action).add(timezone, 'hour').locale($page.props.auth.user.language).fromNow() }}</span>
                </div>
            </div>
        </div>
    </a>
    <div v-else class="px-6 py-4 mt-4 flex items-center space-x-2 text-darker dark:text-light bg-light-primary dark:bg-dark-primary dark:border dark:border-dark shadow dark:shadow-none rounded-lg cursor-default select-none">
        <img class="h-14 w-14 mr-4 object-cover rounded-full" src="/storage/icons/deleted.jpg" alt="">
        <div class="flex-1">
            <div class="flex justify-between items-center">
                <div>
                    <span class="">{{ localization[session.language].deleted }}</span>
                </div>
                <div class="flex items-center">
                    <button v-if="uri == '/home/followers/' + $page.props.auth.user.id || uri == '/home/following/' + $page.props.auth.user.id" @click="removeSubscription($page.props.auth.user.id, profile.id, title);" class="w-10 h-10 flex justify-center items-center ring-2 ring-transparent active:ring-additional rounded-full" :title="localization[session.language].remove">
                        <svg class="h-9 w-9 rounded-full p-2 text-darker dark:text-light bg-light-primary dark:bg-dark-primary" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>